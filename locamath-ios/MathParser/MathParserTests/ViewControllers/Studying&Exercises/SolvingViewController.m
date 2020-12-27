//
//  SolvingViewController.m
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "SolvingViewController.h"
#import "SolutionView.h"
#import "MTToolsView.h"
#import "ObjectiveView.h"
#import "Action.h"
#import "Task.h"
#import "Level.h"
#import "TaskErrorManager.h"
#import "MTMovableView.h"
#import "GameManager.h"
#import "SolutionViewDataSource.h"
#import "SolutionViewDelegate.h"
#import "CoreData+MagicalRecord.h"
#import "DataUtils.h"
#include <mach/mach_time.h>
#import "SynchronizationManager.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"
#import "AnimationManager.h"
#import "SocialHTTPClient.h"
#import "LevelsPath.h"
#import "SolvingHelpViewController.h"
#import "ParserWrapper.h"

#import "TaskError.h"

@interface SolvingViewController ()<SolutionViewDataSource, SolutionViewDelegate>

@property (strong, nonatomic) Task *task;
@property (strong, nonatomic) TaskError *taskError;

@property (strong, nonatomic) NSFetchedResultsController *actionsFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *expressionsFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *solutionsFetchedResultsController;

@property (weak, nonatomic) IBOutlet UIView *popUpAddingView;
@property (weak, nonatomic) IBOutlet UILabel *labelTitle;
@property (weak, nonatomic) IBOutlet UIButton *buttonDone;
@property (weak, nonatomic) IBOutlet MTToolsView *theToolsView;
@property (strong, nonatomic) IBOutlet UIImageView *backgroundImage;
@property (weak, nonatomic) IBOutlet UIButton *solveByOperationsButton;
@property (weak, nonatomic) IBOutlet UIButton *solveByExpressionButton;
@property (strong, nonatomic) IBOutlet UILabel *labelForEditOrSEButton;

- (IBAction)onTapAddExpression:(id)sender;
- (IBAction)onTapAddSolution:(id)sender;
- (IBAction)onTapSave:(id)sender;
- (IBAction)onTapDone:(id)sender;
- (IBAction)onTapAddOperation:(id)sender;
- (IBAction)onTapHelp:(id)sender;
@end

@implementation SolvingViewController {
    SolutionView    *solutionView;
    ObjectiveView   *objective;
    BOOL            expressionOnly;
    NSInteger       solvedActions;
    NSInteger       maxSolutions;
}

- (id)initWithAchievement:(id)achievement
{
    self = [super init];
    if (self) {
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(chooseComponent:)
                                                     name:kNotificationChooseComponent
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(putComponent:)
                                                     name:kNotificationPutComponent
                                                   object:nil];
        
        if ([NSStringFromClass([achievement class]) isEqualToString:@"TaskError"]) {
            self.taskError = (TaskError *)achievement;
            self.task = self.taskError.task;
        } else {
            self.task = (Task*)achievement;
        }
        
        solutionView = [[SolutionView alloc] initWithFrame:CGRectMake(87.0f, 244.0f, 558.0f, 447.0f)];
        solutionView.dataSource = self;
        solutionView.delegateSolution = self;
                
        objective = [[ObjectiveView alloc] initWithTask:self.task];
        
        [[GameManager sharedInstance] startTaskTimerForTask:self.task];
    }
    return self;
}

- (void)dealloc
{
    NSLog(@"clear Solving Page");
}

- (void)chooseComponent:(NSNotification *)notification
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[8] loop:NO];
}

- (void)putComponent:(NSNotification *)notification
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[9] loop:NO];
}


#pragma mark - View Methods

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    if (self.taskError != nil && self.task.status != kTaskStatusSolved) {
        self.labelForEditOrSEButton.text = NSLocalizedString(@"Edit", nil);
    }
    
    [self updateLevelBackgroundImage:self.backgroundImage];
    
    [self.theToolsView excludeDisplayingCharacters:@[@"*", @"/", @"{", @"}"]];

//    NSLog(@"<<>> task errors >> : %@", [self.task.taskErrors allObjects]);
//    TaskError *tempTaskError = [[self.task.taskErrors allObjects] lastObject];
//    NSLog(@"task error actions : %@ ",[tempTaskError.actions allObjects]);
    
    if (self.task.status == kTaskStatusSolved || self.taskError != nil) {
        [self.theToolsView.tools each:^(MTMovableView *sender) {
            sender.isMoveEnabled = NO;
        }];
    }
    
    [self.theToolsView displayAdditionalViews:[self tooldViewForCurrentTask]];
    
    [self.view addSubview:solutionView];
    
    if ([self.task.actions count] > 0) {
        [self.buttonDone setEnabled:YES];
    }
    
    NSInteger count = [self.task.expressions count];
    maxSolutions = count;
    
    if ([self.task.solutions isEqualToString:kBothSolutionsType]) {
        [self.labelTitle setText:[NSString stringWithFormat:@"%@ #%@ (%@ %d %@ %d)",
                                  NSLocalizedString(@"Task", nil),
                                  self.task.numberTask,
                                  NSLocalizedString(@"expression", nil),
                                  count,
                                  NSLocalizedString(@"solution", nil),
                                  count]];
        
        maxSolutions = maxSolutions * 2;
    }
    else {
        expressionOnly = YES;
        [self.labelTitle setText:[NSString stringWithFormat:@"%@ #%@ (%@ %d)",
                                  NSLocalizedString(@"Task", nil),
                                  self.task.numberTask,
                                  NSLocalizedString(@"expression", nil),
                                  count]];
        [self.popUpAddingView setHidden:YES];
        
        if ([self.task.actions count] == 0) {
            [self createActionWithType:kActionTypeExpression];
        }
    }
    
    if (self.task.status == kTaskStatusUndefined) {
        self.task.status = kTaskStatusStarted;
    }
    
    self.task.lastChangeDate = [NSDate date];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];

    self.theToolsView.overlayView = [UIView overlayForStudyingAndExervices];
    [solutionView reloadData];
    
    [self.view addSubview:objective];
    
    [[AnimationManager sharedInstance] playSolvingAnimationsIfNeededWithViews:@[self.solveByExpressionButton, self.solveByOperationsButton]
                                                                         task:self.task];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [solutionView cleanView];
    [super viewDidDisappear:animated];
}

- (void)viewDidUnload
{
    [self setLabelTitle:nil];
    [self setButtonDone:nil];
    [self setTheToolsView:nil];
    [self setPopUpAddingView:nil];
    [self setBackgroundImage:nil];
    [self setSolveByOperationsButton:nil];
    [self setSolveByExpressionButton:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [solutionView reloadData];
}

#pragma mark - IBAction Methods

- (IBAction)onTapAddExpression:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    if ([self countExpressions] < [self.task.expressions count]) {
        [self createActionWithType:kActionTypeExpression];
        [self.popUpAddingView setHidden:YES];
    } else {
        [self showAlertViewWithTitle:NSLocalizedString(@"Message", @"Alert title")
                         withMessage:NSLocalizedString(@"You have added all possible expressions",
                                                       @"Solving page")];
    }
}

- (IBAction)onTapAddSolution:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    if (expressionOnly != YES) {
        if ([self countSolutions] < [self.task.expressions count] && self.task.status != kTaskStatusSolved) {
            [self createActionWithType:kActionTypeSolution];
            [self.popUpAddingView setHidden:YES];
        } else {
            [self showAlertViewWithTitle:NSLocalizedString(@"Message", @"Alert title") withMessage
                                        :NSLocalizedString(@"You have added all possible solutions",
                                                           @"Solving page")];
        }
    } else {
        [self showAlertViewWithTitle:NSLocalizedString(@"Message", @"Alert title") withMessage
                                    :NSLocalizedString(@"You can add just expression for this task!",
                                                       @"Solving page")];
    }
}


- (void)saveTimeForTask
{
    GameManager *gameManager = [GameManager sharedInstance];
    
    self.task.secondsPerTask = [NSNumber numberWithInteger:[gameManager getSecondsForTask]];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (IBAction)onTapSave:(id)sender
{
    if ([self.labelForEditOrSEButton.text isEqualToString:NSLocalizedString(@"Edit", nil)]) {
        self.taskError = nil;
        
        [solutionView reloadData];
        
        //enabled tools for editing
        [self.theToolsView.tools each:^(MTMovableView *sender) {
            sender.isMoveEnabled = YES;
        }];
        
        self.theToolsView.overlayView = [UIView overlayForStudyingAndExervices];
        
        self.labelForEditOrSEButton.text = NSLocalizedString(@"Save & Exit", nil);
        
    } else {
        [self saveAndExit];
    }
}

- (IBAction)onTapDone:(id)sender
{
    if (self.task.status == kTaskStatusSolved) {
        
        [super goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
        
    } else {
        
        [self saveTimeForTask];
        
        [self.actionsFetchedResultsController performFetch:NULL];
        
        NSArray *actions = [self.actionsFetchedResultsController fetchedObjects];
        
        self.task.currentScore = [NSNumber numberWithInt:0];
        [[NSManagedObjectContext  contextForCurrentThread] saveToPersistentStoreAndWait];
        
        //check input format
        NSString *inputFormatErrorDescription = [TaskErrorManager errorDescriptionOnAnswerForActions:actions withTask:self.task];
        
        if ([self isHaveSameInActions:actions]) {
            [UIAlertView showAlertViewWithMessage:@"The same solutions, change one of them!"];
            
        } else if ([inputFormatErrorDescription isEqualToString:@"No Error"]) {
            
            [[ParserWrapper new] parseWithActions:actions withEtalons:self.task.expressions];

            NSMutableDictionary *errorInfo = [TaskErrorManager errorInfoOnTaskSolvingWithActions:actions withTask:self.task];
            
            if ([[errorInfo valueForKey:kTaskErrorInfoStatus] isEqualToString:@"No Error"]) {
                [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[10] loop:NO];
                self.task.status = kTaskStatusSolved;
                               
                self.task.currentScore = [DataUtils scoreForTask:self.task withActions:actions];
                [self setCorrectActions:actions];
            } else if (([[errorInfo valueForKey:kTaskErrorInfoStatus] isEqualToString:@"No Error not all solv"])) {
                [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[10] loop:NO];
                self.task.status = kTaskStatusSolvedNotAll;
                
                self.task.currentScore = [DataUtils scoreForTask:self.task withActions:actions];
                [self setCorrectActions:actions];
            } else {
                [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[11] loop:NO];
                self.task.status = kTaskStatusError;
            }
            
            self.task.lastChangeDate = [NSDate date];
            
            [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
            
            [super goBackAnimated:YES withDelegate:self.backDelegate withOption:YES completion:^{
                [[SynchronizationManager sharedInstance] setChildLevelsDataWithSuccess:^{
                    NSLog(@"Success set Levels");
                    //[self postToFBIfNeeded];
                } failure:^(NSError *error) {
                    NSLog(@"Failure set Levels: %@", [error localizedDescription]);
                } progress:^(CGFloat progress) {
                }];
            }];
            
        } else {
            
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[5] loop:NO];
            
            if ([inputFormatErrorDescription isEqualToString:@"Error Answer"]) {
                
            [UIAlertView showAlertViewWithTitle:@""
                                        message:NSLocalizedString(@"Task can have just one correct answer! Input the same ANSWER to all fields", @"Solving page")
                              cancelButtonTitle:NSLocalizedString(@"Change answer", @"Solving page")
                              otherButtonTitles:@[NSLocalizedString(@"Save & Exit", @"Solving page")]
                                        handler:^(UIAlertView *alert, NSInteger buttonIndex) {
                                            if (buttonIndex == 1) {
                                                 [self saveAndExit];
                                            }
                                        }];
            } else {
                [self showAlertViewWithTitle:NSLocalizedString(@"Error", @"") withMessage:inputFormatErrorDescription];
            }
        }
    }
}

- (IBAction)onTapAddOperation:(id)sender
{
    [self.popUpAddingView setHidden:NO];
    [[AnimationManager sharedInstance] playSolvingAnimationsIfNeededWithViews:@[self.solveByExpressionButton,
                                                                                self.solveByOperationsButton]
                                                                         task:self.task];
}

- (IBAction)onTapHelp:(id)sender
{
    SolvingHelpViewController *helpPage = [SolvingHelpViewController new];
    [self presentViewController:helpPage animated:YES completion:nil];
}

- (void)saveAndExit
{
    [self saveTimeForTask];
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [super goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}

#pragma mark - Create Action Method

- (void)createActionWithType:(ActionType)type
{
    Action *action = [Action createEntity];
    
    action.identifier =  [NSString stringWithFormat:@"%llu", mach_absolute_time()]; //timestamp
    action.task = self.task;
    action.type = type;
    action.answer = @"";
    
    [action addSubActionWithString:@""];
    
    if (!self.buttonDone.enabled) {
        [self.buttonDone setEnabled:YES];
    }
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    [self.actionsFetchedResultsController performFetch:NULL];
    
    [solutionView reloadDataWithAnimation];
}


#pragma mark - SolutionView datasource

- (NSArray *)fetchedActions
{
    [self.actionsFetchedResultsController performFetch:NULL];
    self.actionsFetchedResultsController = nil;
    return [self.actionsFetchedResultsController fetchedObjects];
}

- (ActionView *)createActionViewWithAction:(Action *)action
{
    ActionView *actionView = [[ActionView alloc] initWithAction:action];
    
    [actionView setBackgroundColor:[UIColor clearColor]];
    actionView.answer = action.answer;
    actionView.subActions = [NSMutableArray arrayWithArray:[action.subActions array]];
    
    [actionView.headerLabel setBackgroundColor:[UIColor clearColor]];
    [actionView.headerLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:22.0f]];
    
    if (action.type == kActionTypeExpression) {
        [self.expressionsFetchedResultsController performFetch:NULL];
        actionView.headerLabel.text = [NSString stringWithFormat
                                       :@"%@ #%@", NSLocalizedString(@"Expression", nil), action.task == nil ?
                                       action.taskError.task.numberTask :
                                       action.task.numberTask];
    } else {
        [self.solutionsFetchedResultsController performFetch:NULL];
        actionView.headerLabel.text = [NSString stringWithFormat
                                       :@"%@ #%@", NSLocalizedString(@"Solution", nil), action.task == nil ?
                                       action.taskError.task.numberTask :
                                       action.task.numberTask];
    }
    
    return actionView;
}


#pragma mark - SolutionView delegate

- (void)setNeedsFont
{
    [self setActualFonts];
}

- (void)deleteAction:(Action *)action
{
    if (self.task.status != kTaskStatusSolved) {
        [action deleteEntity];
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
        [self enabledStateForButtonDone];
        [[AnimationManager sharedInstance] playSolvingAnimationsIfNeededWithViews:@[self.solveByExpressionButton,
                                                                                    self.solveByOperationsButton]
                                                                             task:self.task];
    }
}

- (void)addSubActionToAction:(Action *)action
{
    [action addSubActionWithString:@""];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

- (void)deleteSubActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    [[action.subActions objectAtIndex:subActionIndex] deleteEntity];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    NSString *str = [action.subActions objectAtIndex:subActionIndex];
    str = [str stringByAppendingString:component];
    [action replaceObjectInSubActionsAtIndex:subActionIndex withObject:[Action actionWithString:str]];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)didChangeComponent:(NSString *)component withSubActionIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
//    [action replaceObjectInSubActionsAtIndex:subActionIndex withObject:[Action actionWithString:component]];
    Action *subAction = [action.subActions objectAtIndex:subActionIndex];
    [subAction updateWithString:component];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)didChangeAnswerComponent:(NSString *)component forAction:(Action *)action
{
    action.answer = component;
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

- (void)addAnswerWithComponent:(NSString *)component forAction:(Action *)action
{
    action.answer = [action.answer stringByAppendingString:component];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

#pragma mark - Helper

- (void)setCorrectActions:(NSArray *)actions
{
    [actions each:^(Action *action) {
        if (action.error == kActionErrorTypeNone) {
            action.isCorrect = @YES;
            
            [action.subActions each:^(Action *subAction) {
                subAction.isCorrect = @YES;
            }];
        }
    }];
}

- (BOOL)isHaveSameInActions:(NSArray *)actions
{
    BOOL isHaveSame = NO;
    
    NSArray *expressionActions = [DataUtils actionsOfType:kActionTypeExpression
                                              fromActions:actions];
    
    NSArray *solutionActions = [DataUtils actionsOfType:kActionTypeSolution
                                            fromActions:actions];
    
    if (expressionActions.count > 1) {
        Action *action1 = expressionActions[0];
        Action *action2 = expressionActions[1];
        
        if ([action1.etalon isEqualToNumber:action2.etalon]) {
            isHaveSame = YES;
        }
    }
    
    if (!isHaveSame && solutionActions.count > 1) {
        Action *action1 = solutionActions[0];
        Action *action2 = solutionActions[1];
        
        if ([action1.etalon isEqualToNumber:action2.etalon]) {
            isHaveSame = YES;
        }
    }

    return isHaveSame;
}

- (NSArray *)tooldViewForCurrentTask
{
    NSMutableArray *additionalLabels = [NSMutableArray new];
    for (NSString *letter in self.task.letters) {
        UILabel *lblToCopy = (UILabel *)[self.theToolsView.tools.lastObject carriedView];
        UILabel *letterLbl = [NSKeyedUnarchiver unarchiveObjectWithData:[NSKeyedArchiver archivedDataWithRootObject:lblToCopy]];
        
        letterLbl.frame    = CGRectMake(0, 0, 0, 0);
        letterLbl.text     = letter;
        [letterLbl sizeToFit];
        [additionalLabels addObject:letterLbl];
    }
    
    return additionalLabels;
}

//- (void)postToFBIfNeeded
//{
//    Level *level = self.task.level;
//    LevelsPath *levelPath = level.path;
//    
//    if ([level.isTest boolValue] && (self.task.status == kTaskStatusSolved || self.task.status == kTaskStatusSolvedNotAll)) {
//        
//        NSString *message = [NSString stringWithFormat:
//                             NSLocalizedString(@"I solved test problem #%@ from the topic \"%@\"!", nil),
//                             self.task.numberTask, levelPath.name];
//        
//        [SocialHTTPClient postMessageToFB:message withAdditionalMessage:nil success:^(BOOL finished, NSError *error) {
//            NSLog(@"success post to FB :)");
//            if ([DataUtils isAllTasksSolvedFromTasks:[level.tasks allObjects]]) {
//                NSString *messageAllSolved = [NSString stringWithFormat:
//                           NSLocalizedString(@"I solved all the test problems from the topic \"%@\"!", nil),
//                           levelPath.name];
//                [SocialHTTPClient postMessageToFB:messageAllSolved withAdditionalMessage:nil success:^(BOOL finished, NSError *error) {
//                    NSLog(@"success post messageAllSolved to FB :)");
//                } failure:^(BOOL finished, NSError *error) {
//                    NSLog(@"falure post messageAllSolved to FB :(");
//                }];
//            }
//
//        } failure:^(BOOL finished, NSError *error) {
//            NSLog(@"falure post to FB :(");
//        }];
//    }
//}

#pragma mark - Setters&Getters

- (NSFetchedResultsController *)actionsFetchedResultsController
{
    
    if (!_actionsFetchedResultsController) {
        
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@", self.task];
        if (self.taskError) {
            predicate = [NSPredicate predicateWithFormat:@"taskError == %@", self.taskError];
        }
        
        self.actionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES
                                                          withPredicate:predicate
                                                                groupBy:nil
                                                               delegate:nil];
    }
    
    return _actionsFetchedResultsController;
}

- (NSFetchedResultsController *)expressionsFetchedResultsController
{
    if (!_expressionsFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@ AND typeNumber == %@", self.task, [NSNumber numberWithInteger:kActionTypeExpression]];
        
        self.expressionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _expressionsFetchedResultsController;
}

- (NSFetchedResultsController *)solutionsFetchedResultsController
{
    if (!_solutionsFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@ AND typeNumber == %@", self.task, [NSNumber numberWithInteger:kActionTypeSolution]];
        
        self.solutionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _solutionsFetchedResultsController;
}


#pragma mark Services Methods

- (NSInteger)countExpressions
{
    [self.expressionsFetchedResultsController performFetch:NULL];
    return [[self.expressionsFetchedResultsController fetchedObjects] count];
}

- (NSInteger)countSolutions
{
    [self.solutionsFetchedResultsController performFetch:NULL];
    return [[self.solutionsFetchedResultsController fetchedObjects] count];
}

- (void)enabledStateForButtonDone
{
    NSInteger *countExpressions = [self countExpressions];
    NSInteger *countSolutions = [self countSolutions];
    
    if (countExpressions == 0 && countSolutions == 0) {
        [self.buttonDone setEnabled:NO];
    }
}

#pragma mark - UIAlertView

- (void)showAlertViewWithTitle:(NSString *)title withMessage:(NSString *)message
{
    NSArray *arrayButtonsTitles = nil;
    
    if ([message isEqualToString:NSLocalizedString(@"Answer field is empty", @"Solving page")]) {
        arrayButtonsTitles = [NSArray arrayWithObjects:
                              NSLocalizedString(@"Add answer", @"Solving page"),
                              NSLocalizedString(@"Save & Exit", @"Solving page"), nil];
    } else if ([message isEqualToString:NSLocalizedString(@"Answers are not identical to each other.", @"Solving page")]) {
        arrayButtonsTitles = [NSArray arrayWithObjects:
                              NSLocalizedString(@"Change answer", @"Solving page"),
                              NSLocalizedString(@"Save & Exit", @"Solving page"), nil];
        
    } else {        
        [UIAlertView showAlertViewWithTitle:title
                                    message:message
                          cancelButtonTitle:nil
                          otherButtonTitles:nil
                                    handler:nil];
        return;
    }
    
    [UIAlertView showAlertViewWithTitle:title
                                message:message
                      cancelButtonTitle:[arrayButtonsTitles objectAtIndex:0]
                      otherButtonTitles:@[[arrayButtonsTitles objectAtIndex:1]]
                                handler:^(UIAlertView *alertView, NSInteger buttonIndex) {
                                    if (buttonIndex == 1) {
                                        NSArray *actions = [self.actionsFetchedResultsController fetchedObjects];
                                        for (Action *action in actions) {
                                            action.error = kActionErrorTypeNone;
                                        }
                                        [self saveAndExit];
                                    }
                                }];
}

@end
