//
//  SolvingViewController.m
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "OlympiadSolvingViewController.h"
#import "PopupOlympiadFailViewController.h"
#import "PopupOlympiadSolvedViewController.h"

#import "PresentingSeguesStructure.h"

#import "MTToolsView.h"
#import "ObjectiveView.h"

#import "OlympiadTask.h"
#import "OlympiadLevel.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"
#import "Child.h"
#import "Game.h"

#import "TaskErrorManager.h"
#import "SolutionViewDataSource.h"
#import "SolutionViewDelegate.h"
#import "UIView+LWAutoFont.h"

#import "MTMovableView.h"
#import "MTMovableViewCollection.h"

#import "OlympiadActionCell.h"
#import "NSSet+ExtractInnerSets.h"
#import "SynchronizationManager.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"
#import "SocialHTTPClient.h"

#define STRING_NIL(str)(str?str:@"")


#define MAXSIZEFONT 40.0f
#define MINSIZEFONT 30.0f

@interface OlympiadSolvingViewController ()<UITableViewDataSource, UITableViewDelegate>

@property (strong, nonatomic) OlympiadTask       *task;
@property (weak, nonatomic) IBOutlet UILabel     *labelTitle;
@property (weak, nonatomic) IBOutlet UIButton    *buttonDone;
@property (weak, nonatomic) IBOutlet MTToolsView *theToolsView;
@property (strong, nonatomic) ObjectiveView      *objective;
@property (strong, nonatomic) NSArray            *actions;

@property (strong, nonatomic) IBOutlet UILabel     *sourceTool;
@property (strong, nonatomic) IBOutlet UITableView *actionsTable;

- (IBAction)onTapSave:(id)sender;
- (IBAction)onTapDone:(id)sender;

@end


@implementation OlympiadSolvingViewController

- (id)initWithAchievement:(OlympiadTask *)task
{
    self = [super init];
    if (self) {        
        self.task = task;
        self.actions = [[self.task.actions allObjects]sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
            return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
        }];
        
        self.objective = [[ObjectiveView alloc] initWithTask:self.task
                                                       frame:CGRectMake(110.0f, 80.0f, 405.0f, 75.0f)];
    }
    return self;
}


#pragma mark - View Methods

- (void)viewDidLoad
{
    [self.actionsTable registerNib:[UINib nibWithNibName:@"OlympiadActionCell" bundle:nil] forCellReuseIdentifier:kCellIdentifier];
    
    [super viewDidLoad];
    
    self.actionsTable.scrollEnabled = NO;
    
    self.theToolsView.rowWidth = 270.0f;
    self.theToolsView.isTaskCompleted = self.task.isCorrect;
    
    self.labelTitle.text = [NSString stringWithFormat:@"%@ #%@", NSLocalizedString(@"Task", nil), self.task.numberTask];
        
    [self reloadToolsView];
            
    [self.view addSubview:self.objective];
    
    if ([self.task.actions count] > 0) {
        [self.buttonDone setEnabled:YES];
    }
    
    if (self.task.status == kTaskStatusUndefined) {
        self.task.status = kTaskStatusStarted;
    }
    
    //[self setActualFonts];
    
    self.task.lastChangeDate = [NSDate date];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];

    self.theToolsView.overlayView = [UIView overlayForOlympiads];
    [self.actionsTable reloadData];
}

- (void)viewDidUnload
{
    [self setLabelTitle:nil];
    [self setButtonDone:nil];
    [self setTheToolsView:nil];
    [self setSourceTool:nil];
    [self setActionsTable:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - IBAction Methods

- (IBAction)onTapSave:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    [super goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}

- (IBAction)onTapDone:(id)sender
{
    if (![self areActionsFilled]) {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"There are empty gaps. You should fill every gap", @"Olympiad solving page")];
    } else {
        self.task.tryCounter = @([self.task.tryCounter integerValue] + 1);
        self.task.status = self.task.isCorrect ? kTaskStatusSolved : kTaskStatusError;
        self.task.lastChangeDate = [NSDate date];
        
        if ([self.task isCorrect]) {
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[10] loop:NO];
    
            PopupOlympiadSolvedViewController *solvedPopup = [PopupOlympiadSolvedViewController new];
            [solvedPopup view]; // loading view;
            solvedPopup.pointsLabel.text = [NSString stringWithFormat:STRING_NIL(solvedPopup.pointsLabel.text), [self.task.currentScore integerValue], [self.task.points integerValue]];
            
            [solvedPopup presentOnViewController:self finish:^{
                [self synchronizationData];
            }];
        } else {
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[11] loop:NO];
            
            PopupOlympiadFailViewController *failedPopup = [PopupOlympiadFailViewController new];
            [failedPopup view]; // loading view;
            failedPopup.timesLabel.text = [NSString stringWithFormat:STRING_NIL(failedPopup.timesLabel.text), [self.task.tryCounter integerValue]];
            
            [failedPopup presentOnViewController:self finish:^{
                [self synchronizationData];
            }];
        }
    }
        
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
}

#pragma mark UITableView delegate and datasource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return self.task.actions.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    OlympiadActionCell *cell   = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    OlympiadAction     *action = [self.actions objectAtIndex:indexPath.row];
    
    cell.backgroundColor = [UIColor clearColor];
    
    __weak OlympiadSolvingViewController *weakSelf = self;
    
    if ([self.task.isOneToolToOneAnswerMapping boolValue]) {
        cell.didReloadBlock = ^(){
            [weakSelf reloadToolsView];
        };
    }
    
    cell.task = self.task;

    cell.hints = [[action.hints allObjects] sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    
    return cell;
}

#pragma mark - Helper

- (BOOL)areActionsFilled
{
    BOOL filled = YES;
    for (OlympiadAction *action in self.actions) {
        if ( ! [[action isFilled] boolValue]) {
            filled = NO;
        }
    }
    
    return filled;
}

- (void)reloadToolsView
{
    [self.theToolsView excludeAllCharacters];
    
    NSMutableArray *additionalLabels = [NSMutableArray new];
    
    for (NSString *letter in self.task.tools) {
        UILabel *letterLbl = [NSKeyedUnarchiver unarchiveObjectWithData:[NSKeyedArchiver archivedDataWithRootObject:self.sourceTool]];
        letterLbl.text = letter;
        letterLbl.hidden = NO;
        [letterLbl sizeToFit];
        if (letterLbl.frame.size.width > 250) {
            CGRect shrinkedFrame = letterLbl.frame;
            shrinkedFrame.size.width = 250;
            letterLbl.frame = shrinkedFrame;
        }
        letterLbl.adjustsFontSizeToFitWidth = YES;
        letterLbl.minimumFontSize = 1.0f;
        
        [additionalLabels addObject:letterLbl];
    }
    
    [self.theToolsView displayAdditionalViews:additionalLabels];
    
    if ([self.task.isOneToolToOneAnswerMapping boolValue]) {
        
        NSSet *hints = [self.task.actions valueForKey:@"hints"];
        hints = [hints setByExtractingInnerSets];
        
        hints = [hints select:^BOOL(OlympiadHint *hint) {
            return hint.userInput != nil;
        }];
        
        NSSet *usersInputs = [hints valueForKey:@"userInput"];
        
        [self.theToolsView excludeDisplayingCharacters:[usersInputs allObjects]];
    }
    
    [self.theToolsView.tools each:^(MTMovableView *tool) {
        tool.isReturnOnMoveEnd = ![self.task.isOneToolToOneAnswerMapping boolValue];
    }];
    
    self.theToolsView.overlayView = [UIView overlayForOlympiads];
}

- (void)synchronizationData
{
    [super goBackAnimated:YES withDelegate:self.backDelegate withOption:YES completion:^{
        [[SynchronizationManager sharedInstance] setChildOlympiadLevelsDataWithSuccess:^{
            NSLog(@"Success set Olympiad Levels");
            //[self postToFBIfNeeded];
        } failure:^(NSError *error) {
            NSLog(@"Failure set Olympiad Levels");
        } progress:^(CGFloat progress) {
        }];
    }];
}

//- (void)postToFBIfNeeded
//{
//    OlympiadLevel *level = self.task.level;
//    
//    if (self.task.status == kTaskStatusSolved) {
//        
//        NSString *message = [NSString stringWithFormat:
//                             NSLocalizedString(@"I solved olympiad problem #%@ of the first level of the \"%@\"!", nil),
//                             self.task.numberTask, level.name];
//        
//        [SocialHTTPClient postMessageToFB:message withAdditionalMessage:nil success:^(BOOL finished, NSError *error) {
//            
//            NSLog(@"success post to FB :)");
//            
//            if ([DataUtils isAllTasksSolvedFromTasks:[level.tasks allObjects]]) {
//                NSString *messageAlSolved = [NSString stringWithFormat:
//                                     NSLocalizedString(@"I solved the first level of the \"%@\"!", nil),
//                                     level.name];
//                [SocialHTTPClient postMessageToFB:messageAlSolved withAdditionalMessage:nil success:^(BOOL finished, NSError *error) {
//                    NSLog(@"success post messageAlSolved to FB :)");
//                } failure:^(BOOL finished, NSError *error) {
//                    NSLog(@"falure post messageAlSolved to FB :(");
//                }];
//            }
//
//        } failure:^(BOOL finished, NSError *error) {
//            NSLog(@"falure post to FB :(");
//        }];
//    }
//}

@end