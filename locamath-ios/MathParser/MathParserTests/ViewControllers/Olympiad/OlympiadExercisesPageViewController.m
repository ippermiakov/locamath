//
//  ExercisesPage.m
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "OlympiadExercisesPageViewController.h"
#import "OlympiadSolvingViewController.h"
#import "OlympiadTask.h"
#import "OlympiadLevel.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"

#import "OlympiadActionCell.h"

#import "PopUpController.h"
#import "SchemeViewController.h"
#import "AnimationViewController.h"
#import "ConcretOlympiadViewController.h"
#import "OlympiadNextTaskPopupViewController.h"

#import "UIView+Transform.h"
#import "LWRatingView.h"
#import "DebugMode.h"
#import "MBProgressHUD+Mathematic.h"
#import "AnimationManager.h"
#import "TTTAttributedLabel.h"
#import "TTTAttributedLabel+MTTextView.h"
#import "SynchronizationManager.h"
#import "BaseViewController+RegistrationAndLogin.h"

@interface OlympiadExercisesPageViewController ()<UITableViewDelegate, UITableViewDataSource, PopUpControllerDelegate>

@property (strong, nonatomic) NSFetchedResultsController *tasksFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *solutionFetchedResultsController;

@property (weak, nonatomic) IBOutlet TTTAttributedLabel *textViewObjective;
@property (weak, nonatomic) IBOutlet UIImageView *cupImageView;
@property (weak, nonatomic) IBOutlet UITableView *theTableView;
@property (weak, nonatomic) IBOutlet UILabel *levelLabel;
@property (weak, nonatomic) IBOutlet UIButton *errorButton;
@property (weak, nonatomic) IBOutlet UILabel *labelNumberTask;

@property (strong, nonatomic) NSArray *actions;

@property (strong, nonatomic) OlympiadSolvingViewController *solvingViewController;
@property (strong, nonatomic) PopUpController *popUpController;
@property (strong, nonatomic) IBOutlet LWRatingView *stars;
- (IBAction)onSolving:(id)sender;
@property (strong, nonatomic) IBOutlet UIButton *solvingButton;
@property (strong, nonatomic) IBOutletCollection(UIView) NSArray *viewsToAnimate;


@end

@implementation OlympiadExercisesPageViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}


#pragma mark - Main View Method

- (void)viewDidLoad
{
    [self.theTableView registerNib:[UINib nibWithNibName:@"OlympiadActionCell" bundle:nil] forCellReuseIdentifier:kCellIdentifier];
        
    [self.labelNumberTask setText:[NSString stringWithFormat:@"%@", [self.task.numberTask stringValue]]];
    self.textViewObjective.text = self.task.objective;
    [self.cupImageView setImage:[UIImage imageNamed:self.level.image]];
    
    if (self.cupImageView.frame.origin.y + self.cupImageView.frame.size.height + 10 >= self.stars.frame.origin.y) {
        self.cupImageView.frame = (CGRect){self.cupImageView.frame.origin.x, self.cupImageView.frame.origin.y - 20, self.cupImageView.frame.size.width,self.cupImageView.frame.size.height};
    }
    self.levelLabel.text = self.level.name;

//    [self updateMe];
    
    [super viewDidLoad];
    
#ifdef DEBUG
    self.solvingButton.hidden = NO;
#endif
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setTextViewObjective:nil];
    [self setCupImageView:nil];
    [self setTheTableView:nil];
    [self setLevelLabel:nil];
    [self setErrorButton:nil];
    [self setErrorButton:nil];
    [self setLabelNumberTask:nil];
    
    [self setSolvingButton:nil];
    [self setViewsToAnimate:nil];
    [super viewDidUnload];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    [self.theTableView reloadData];
    
    self.stars.tasks = self.level.sortedArrayOfTasks;
    self.stars.maxRating = self.level.tasks.count > 0 ? self.level.tasks.count : kDefaultStarsCount;
    self.stars.rating = [[self.level.sortedArrayOfTasks select:^BOOL(OlympiadTask *obj) {
        return [obj isCorrect];
    }] count];
    
    [[AnimationManager sharedInstance] playOlympiadTaskAnimationsIfNeededWithViews:self.viewsToAnimate
                                                                              task:self.task];
}

#pragma mark - BaseViewControllerDelegate

- (void)didFinishBackWithOption:(BOOL)option
{
    self.solvingViewController = nil;
    
    [self updateMe];
    
    if (option) {
        [self showPopUpWithInfo];
    }

#ifdef DEBUG
    self.solvingButton.hidden = NO;
#endif
}

#pragma mark - Alert Methods

- (void)showPopUpWithInfo
{
}

- (void)showSolvingPage
{
    self.solvingViewController = [[OlympiadSolvingViewController alloc] initWithAchievement:self.task];
    self.solvingViewController.backDelegate = self;
    [self presentViewController:self.solvingViewController animated:YES completion:nil];
}

- (void)alertViewOkButtonTapped
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
}

#pragma mark - Actions

- (IBAction)onSolving:(id)sender
{
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];

    [DebugMode solveTaskOlympiad:self.task
              withUpdateToServer:YES
                        progress:^(CGFloat progress) {
//                            [HUD updateWithProgress:progress];
    }
                          finish:^{
                              [self updateMe];
//                              [HUD hideOnSyncCompletion];
    }
                         failure:^(NSError *error) {
                             [self updateMe];
//                             [HUD hideOnSyncFailure];
     }];
}

- (IBAction)onTapStartSolve:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
    [self showSolvingPage];
}

- (IBAction)onTapBackHome:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onTapSolutionButton:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
    
    [self showSolvingPage];
}

- (IBAction)onTapErrorButton:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
    
    [self showPopUpWithInfo];
}

- (IBAction)onTapNextTask:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    id transitToNextTask = ^() {
        ConcretOlympiadViewController *parentVC = (ConcretOlympiadViewController *)self.presentingViewController;
        [self dismissViewControllerAnimated:YES completion:^{
            OlympiadExercisesPageViewController *nextTaskVC = [OlympiadExercisesPageViewController new];
            
            NSUInteger taskIdx = [self.task.numberTask integerValue] + 1;
            
            if (taskIdx < [parentVC.level.tasks count] + 1) {
                nextTaskVC.level = parentVC.level;
                nextTaskVC.task  = [parentVC taskWithIndex:[self.task.numberTask integerValue] + 1];
                
                [parentVC presentViewController:nextTaskVC animated:YES completion:nil];
            } else {
                [parentVC dismissViewControllerAnimated:YES completion:nil];
            }
        }];
    };
    
    OlympiadNextTaskPopupViewController *nextTaskPopupVC = [OlympiadNextTaskPopupViewController new];
    
    if (self.task.isCorrect) {
        nextTaskPopupVC.isFailPopup = NO;
        nextTaskPopupVC.task = self.task;
    } else {
        nextTaskPopupVC.isFailPopup = YES;
    }
    
    [nextTaskPopupVC presentOnViewController:self finish:^{
        if (nextTaskPopupVC.actionType == NextActionRedo) {
            [self showSolvingPage];
        } else if (nextTaskPopupVC.actionType == NextActionTask){
            [transitToNextTask invoke];
        }
    }];
}

- (IBAction)onButtonHelp:(id)sender
{
    self.popUpController = [[PopUpController alloc] initWithNibName:@"PopupOlympiadHelpViewController" bundle:nil];
    
    self.popUpController.delegate = self;
    
    [self.view addSubview:self.popUpController.view];
}

#pragma mark - PopUpControllerDelegate

- (void)popOverDidTapRestoreButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
    [self showSolvingPage];
}

- (void)popOverDidTapHomeButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
}

- (void)popOverDidTapNextButton
{
    [self dismissViewControllerAnimated:NO completion:nil];
}

- (void)popOverDidTapOkButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
}

#pragma mark - UITableViewDelegate

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    self.actions = [self.task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    return self.actions.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    OlympiadActionCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    cell.backgroundColor = [UIColor clearColor];
    cell.cellType = ActionCellDisplaying;
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    
    cell.task = self.task;
    
    OlympiadAction *action = [self.actions objectAtIndex:indexPath.row];
    
    cell.hints = [[action.hints allObjects] sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    cell.xscale = 0.6;
    cell.yscale = 0.6;
    
    return cell;
}

- (BOOL)canPresentViewControllerClass:(Class)class
{
    return NO;
}

#pragma mark - Update Method

- (void)updateMe
{
    [self.theTableView reloadData];
}

#pragma mark - Setters&Getters

- (NSFetchedResultsController *)tasksFetchedResultsController
{
    if (!_tasksFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"identifier == %@", self.task.identifier];
        
        self.tasksFetchedResultsController = [OlympiadTask fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _tasksFetchedResultsController;
}

@end