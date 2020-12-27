//
//  SolvingAndExercisesViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 11.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SolvingAndExercisesViewController.h"
#import "ObjectiveView.h"
#import "GameManager.h"
#import "AnimationManager.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"


#import "Task.h"
#import "Level.h"
#import "LevelsPath.h"
#import "Scheme.h"
#import "SchemeElement.h"

#import "MTToolsView.h"
#import "MTMovableView.h"
#import "SolutionView.h"
//#import "Action+Creation.h"
#import "DataUtils.h"

#include <mach/mach_time.h>
#import "DistanceCalculation.h"

#import "ParserWrapper.h"

#import "TaskErrorManager.h"
#import "SynchronizationManager.h"
#import "ChildManager.h"

#import "PopUpController.h"
#import "SolvingHelpViewController.h"
#import "AnimationViewController.h"
#import "PresentableViewController.h"
#import "HelpChainBuilder.h"
#import "SolvingSolutionViewController.h"
#import "SolvingSchemesViewController.h"
#import "UIViewController+DismissViewController.h"

@interface SolvingAndExercisesViewController () <PopUpControllerDelegate, SolvingSchemesViewControllerDelegate,
                                                                            SolvingSolutionViewControllerDelegate>

@property (strong, nonatomic) IBOutlet UIView *greenView;
@property (nonatomic, strong) ObjectiveView * objective;
@property (nonatomic, strong) ObjectiveView * objectiveSchemes;

@property (strong, nonatomic) IBOutlet UIView *solvingView;
@property (strong, nonatomic) IBOutlet UILabel *levelNameLabel;
@property (strong, nonatomic) IBOutlet UILabel *problemNumberLabel;

@property (strong, nonatomic) IBOutlet UILabel *expressionsCountLabel;
@property (strong, nonatomic) IBOutlet UILabel *solutionCountLabel;
@property (strong, nonatomic) IBOutlet UILabel *completionSchemeLabel;

@property (strong, nonatomic) IBOutlet UIButton *playAnimationButton;
@property (strong, nonatomic) IBOutlet UIButton *errorsButton;

@property (strong, nonatomic) IBOutlet UIButton *editButton;
@property (strong, nonatomic) IBOutlet UIButton *doneButton;

@property (strong, nonatomic) IBOutlet UIImageView *exprressionHaveErrorImageView;
@property (strong, nonatomic) IBOutlet UIImageView *solutionHaveErrorImageView;
@property (unsafe_unretained, nonatomic) BOOL isToolsAnimationsInProgress;

@property (strong, nonatomic) SolvingSolutionViewController *solvingPage;
@property (strong, nonatomic) SolvingSchemesViewController *solvingSchemesPage;
@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *completeImagesArray;
@property (strong, nonatomic) NSMutableArray *letersToDelete;

@property (unsafe_unretained, nonatomic) BOOL isTapEdit;
@property (unsafe_unretained, nonatomic) BOOL isTapError;
@property (unsafe_unretained, nonatomic) BOOL isSchemeToolsShowed;

- (IBAction)onConcretLevel:(id)sender;
- (IBAction)onDone:(id)sender;
- (IBAction)onSaveAndExit:(id)sender;
- (IBAction)onTapHelp:(id)sender;
- (IBAction)onPlayAnimations:(id)sender;
- (IBAction)onError:(id)sender;
//cheng tools
- (IBAction)onTaskNumberTools:(id)sender;
- (IBAction)onTapSchemeTools:(id)sender;
- (IBAction)onEdit:(id)sender;

//tools
@property (strong, nonatomic) IBOutlet UIView *schemeView;
@property (strong, nonatomic) IBOutlet MTToolsView *numberToolsView;
@property (strong, nonatomic) IBOutlet MTToolsView *schemeToolsView;

@property (strong, nonatomic) NSMutableArray *movableViewsForTask;
@property (strong, nonatomic) PopUpController *popUpController;

@end

@implementation SolvingAndExercisesViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (id)initWithAchievement:(id)achievement
{
    self = [super init];
    if (self) {
        if ([NSStringFromClass([achievement class]) isEqualToString:@"TaskError"]) {
            self.taskError = (TaskError *)achievement;
            self.task = self.taskError.task;
        } else {
            self.task = (Task *)achievement;
        }
        
        [[GameManager sharedInstance] stopTaskTimer];
        [[GameManager sharedInstance] startTaskTimerForTask:self.task];
    }
    return self;
}

- (void)viewDidLoad
{
    [self updateSolvingPage];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    //we can't get proper overlay while transition not finished
    self.numberToolsView.overlayView = [UIView overlayForStudyingAndExervices];
    self.schemeToolsView.overlayView = [UIView overlayForStudyingAndExervices];
    
    [self updateSolvingPage];
    
    [self.view addSubview:self.objective];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Actions

- (IBAction)onConcretLevel:(id)sender
{
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onDone:(id)sender
{
    if (self.task.status == kTaskStatusSolved) {
            [self showPopUpWithInfoWithOkButton:NO];
    } else {
        
        [self saveTimeForTask];
        
        NSArray *actions = [self.task.actions allObjects];
        
        self.task.currentScore = [NSNumber numberWithInt:0];
        [[NSManagedObjectContext  contextForCurrentThread] saveToPersistentStoreAndWait];
        
        //check input format
        NSString *inputFormatErrorDescription = [TaskErrorManager errorDescriptionOnAnswerForActions:actions withTask:self.task];
        
        if ([inputFormatErrorDescription isEqualToString:@"No Error"]) {
            
            NSArray *actionWithAnswer = [actions select:^BOOL(Action *obj) {
                return obj.answer.length > 0;
            }];
            
            [self deleteEmptyAction];
            
            [[ParserWrapper new] parseWithActions:actionWithAnswer withEtalons:self.task.expressions];
            
            if ([self isHaveSameInActions:actionWithAnswer]) {
                [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"The same solutions, change one of them!", nil)];
                
            } else {
            
                NSMutableDictionary *errorInfo = [TaskErrorManager errorInfoOnTaskSolvingWithActions:actionWithAnswer withTask:self.task];
                
                if ([[errorInfo valueForKey:kTaskErrorInfoStatus] isEqualToString:@"No Error"]) {
                    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[10] loop:NO];
                    self.task.status = kTaskStatusSolved;
                    
                    self.task.currentScore = [DataUtils scoreForTask:self.task withActions:actionWithAnswer];
                    [self setCorrectActions:actionWithAnswer];
                } else if (([[errorInfo valueForKey:kTaskErrorInfoStatus] isEqualToString:@"No Error not all solv"])) {
                    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[10] loop:NO];
                    self.task.status = kTaskStatusSolvedNotAll;
                    
                    self.task.currentScore = [DataUtils scoreForTask:self.task withActions:actionWithAnswer];
                    [self setCorrectActions:actionWithAnswer];
                } else {
                    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[11] loop:NO];
                    self.task.status = kTaskStatusError;
                }
                
                self.task.lastChangeDate = [NSDate date];
                
                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                
                [self setImageErrorsIfNeeded];
                [self setImageCompleteIfNeeded];
                
                [self.solvingPage reloadSolvingPage];
                
                [self showPopUpWithInfoWithOkButton:NO];
                
                [[SynchronizationManager sharedInstance] setChildLevelsDataWithSuccess:^{
                        NSLog(@"Success set Levels");
                        //[self postToFBIfNeeded];
                } failure:^(NSError *error) {
                        NSLog(@"Failure set Levels: %@", [error localizedDescription]);
                } progress:^(CGFloat progress) {
                }];
            }
            
        } else {
            
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[5] loop:NO];
            if (self.isCalledFromStatistic) {
                [self updateSolvingPage];
                [self saveAndExit];
                
            } else if ([inputFormatErrorDescription isEqualToString:@"Error Answer"]) {
                [UIAlertView showAlertViewWithTitle:@""
                                            message:NSLocalizedString(@"Task can have just one correct answer! Input the same ANSWER to all fields", @"Solving page")
                                  cancelButtonTitle:NSLocalizedString(@"Change answer", @"Solving page")
                                  otherButtonTitles:@[NSLocalizedString(@"Save & Exit", @"Solving page")]
                                            handler:^(UIAlertView *alert, NSInteger buttonIndex) {
                                                if (buttonIndex == 1) {
                                                    [self dismissViewControllerAnimated:YES completion:nil];
                                                }
                                            }];
                [self updateSolvingPage];
            } else {
                [self showAlertViewWithTitle:NSLocalizedString(@"Error", @"") withMessage:inputFormatErrorDescription];
                [self updateSolvingPage];
            }
        }
    }
}

//Sync manager remove on sync actions with indexes greater then count of allowed actions.
- (void)deleteEmptyAction
{
    NSArray *actions = [self.task.actions allObjects];

    NSArray *actionToRemove = [actions select:^BOOL(Action *obj) {
        return obj.answer.length == 0;
    }];
        
    //deleting empty actinos
    [actionToRemove each:^(Action *obj) {
        [obj deleteEntity];
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (IBAction)onSaveAndExit:(id)sender
{
    [self deleteEmptyAction];
    [self saveAndExit];
}

- (IBAction)onTapHelp:(id)sender
{
    self.task.isHelpSelected = @YES;
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    PresentingSeguesStructure *seguesStructure = [HelpChainBuilder helpChainWithLevelID:self.task.level.identifier];
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];

}

- (IBAction)onPlayAnimations:(id)sender
{
    self.task.isAnimationSelected = @YES;
    
    if ([self.task.helpIndex integerValue] == 0) {
        
        if(![self isSchemesCompleted]) {
            self.task.helpIndex = @([self.task.helpIndex integerValue] + kSchemeTutorial1);
        } else {
            self.task.helpIndex = @([self.task.helpIndex integerValue] + kTaskTutorial1);
        }
        
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    AnimationViewController *animationVC = [[AnimationViewController alloc] initWithAnimationFileName:self.task.animation];
    [self presentModalViewController:animationVC animated:YES];
}

- (IBAction)onError:(id)sender
{
    self.isTapError = YES;
    
    [[AnimationManager sharedInstance] hideErrorHelpAnimatios];
    [self showPopUpWithInfoWithOkButton:YES];
}

- (IBAction)onTaskNumberTools:(id)sender
{
    [self hiddenSchemeToolsAndShowNumberTools];
    
    if ([self.task.helpIndex integerValue] == kTaskTutorial1 && [self isSchemesCompleted]) {
        self.task.helpIndex = @([self.task.helpIndex integerValue] + 1);
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    } else if ([self.task.helpIndex integerValue] <= kTaskTutorial1 && [self.task.helpIndex integerValue] > kSchemeTutorial1){
        self.task.helpIndex = @(kSchemeTutorial1);
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (IBAction)onTapSchemeTools:(id)sender
{
    [self hiddenNumberToolsAndShowSchemeTools];
    
    if ([self.task.helpIndex integerValue] == kSchemeTutorial1 && ![self isSchemesCompleted]) {
        self.task.helpIndex = @([self.task.helpIndex integerValue] + 1);
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    } else if ([self.task.helpIndex integerValue] >= 1 && [self.task.helpIndex integerValue] <= 4 && [self isSchemesCompleted]) {

        if ([self.task.helpIndex integerValue] == kTaskTutorial2) {
             self.task.helpIndex = @(kTaskTutorial1);
        }
        
        [[AnimationManager sharedInstance] hideHelpAnimatios];
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (IBAction)onEdit:(id)sender
{
    if (!self.editButton.hidden) {
        
        self.task.lastChangeDate = [NSDate date];
        self.taskError = nil;
        
        //enabled tools for editing
        [self.numberToolsView.tools each:^(MTMovableView *sender) {
            sender.isMoveEnabled = YES;
        }];
        
        self.numberToolsView.overlayView = [UIView overlayForStudyingAndExervices];
        
        self.editButton.hidden = YES;
        self.isTapEdit = YES;
        self.doneButton.hidden = NO;
        
        [self updateSolvingPage];
        
    } else {
        [self saveAndExit];
    }
}

#pragma mark - Animations

- (void)hiddenNumberToolsAndShowSchemeTools
{
    if (!self.isToolsAnimationsInProgress) {
        
        self.isToolsAnimationsInProgress = YES;
        self.isSchemeToolsShowed = YES;
        
        [UIView animateWithDuration:0.5f delay:0.0f options:UIViewAnimationOptionCurveEaseInOut animations:^{
            [[self.numberToolsView tools] each:^(MTMovableView *sender) {
                sender.alpha = 0.0f;
            }];
        } completion:^(BOOL finished) {
            [self.schemeToolsView setHidden:NO];
            
            [self.view insertSubview:self.schemeToolsView aboveSubview:self.numberToolsView];

            [self.schemeToolsView.tools each:^(MTMovableView *sender) {
                sender.alpha = 1.0f;
            }];
            
            self.isToolsAnimationsInProgress = NO;
        }];
        
        [self completionSchemeLabelWithShowOptions:self.isSchemeToolsShowed];
    }
}

- (void)hiddenSchemeToolsAndShowNumberTools
{
    if (!self.isToolsAnimationsInProgress) {
         self.isToolsAnimationsInProgress = YES;
        
        self.isSchemeToolsShowed = NO;
        [UIView animateWithDuration:0.5f delay:0.0f options:UIViewAnimationOptionCurveEaseInOut animations:^{
            [[self.schemeToolsView tools] each:^(MTMovableView *sender) {
                sender.alpha = 0.0f;
            }];
        } completion:^(BOOL finished) {
            [self.numberToolsView setHidden:NO];
            
            [self.view insertSubview:self.numberToolsView aboveSubview:self.schemeToolsView];
            
            [[self.numberToolsView tools] each:^(MTMovableView *sender) {
                sender.alpha = 1.0f;
                
                if (self.task.status != kTaskStatusSolved && ![GameManager sharedInstance].statisticMode) {
                    sender.isMoveEnabled = YES;
                } else {
                    sender.isMoveEnabled = NO;
                }
            }];
            
            self.isToolsAnimationsInProgress = NO;
        }];
        
        [self completionSchemeLabelWithShowOptions:self.isSchemeToolsShowed];
    }
}

- (void)completionSchemeLabelWithShowOptions:(BOOL)isNeedShow
{
    if ([self isSchemesCompleted] && isNeedShow) {
        self.completionSchemeLabel.hidden = !isNeedShow;
        self.completionSchemeLabel.text = NSLocalizedString(@"scheme_success_message", nil);
        self.completionSchemeLabel.alpha = 0.0f;
        [UIView animateWithDuration:0.5f delay:0.5f options:UIViewAnimationOptionCurveEaseInOut animations:^{
            self.completionSchemeLabel.alpha = 1.0f;
        } completion:nil];
    } else {
        self.completionSchemeLabel.alpha = 0.0f;
        self.completionSchemeLabel.hidden = !isNeedShow;
    }
}

#pragma mark - SolutionView delegate

- (void)setNeedsFont
{
    [self setActualFonts];
}

#pragma mark - SolvingSchemesViewControllerDelegate

- (void)needReload
{
    [self reloadSchemesToolsView];
    [self completionSchemeLabelWithShowOptions:self.isSchemeToolsShowed];
    
    if ([self.task.helpIndex integerValue] == kSchemeTutorial2 && [self isSchemesCompleted]) {
        self.task.helpIndex = @([self.task.helpIndex integerValue] + 1);
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    } else if ([self.task.helpIndex integerValue] != 0) {
        [[AnimationManager sharedInstance] hideHelpAnimatios];
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

#pragma mark - PopOverActions

- (void)popOverDidTapOkButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
}

- (void)popOverDidTapHomeButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (void)popOverDidTapRestoreButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
}

- (void)popOverDidTapNextButton
{
    [self.popUpController.view removeFromSuperview];
    self.popUpController = nil;
    self.isTapError = NO;
    
    Level *taskLevel = (Level *)self.task.level;
    Task *lastTaskForLevel = [[taskLevel sortedArrayOfTasks] lastObject];
    
    self.letersToDelete = [self.task.letters mutableCopy];
    
    BOOL isLastTask = [lastTaskForLevel.identifier isEqualToString:self.task.identifier];
    
    BOOL needToDisplayNextLevelsAnimation = [self needToDisplayNextLevelsAnimation];
    BOOL needToDisplayTestLevelCompletionAnimation = [self needToDisplayTestLevelCompletionAnimation];
    
    NSLog(@"needToDisplayNextLevelsAnimation: %@ needToDisplayTestLevelCompletionAnimation: %@ isLastTask: %@", needToDisplayNextLevelsAnimation ? @"YES":@"NO", needToDisplayTestLevelCompletionAnimation ? @"YES":@"NO", isLastTask ? @"YES":@"NO");
    
    if (needToDisplayNextLevelsAnimation || needToDisplayTestLevelCompletionAnimation || isLastTask) {
        [self dismissGameFlowViewControllersWithViewController:self];
    } else {
        Task *currentTask = self.task;
        
        NSArray * tasks = [currentTask.level sortedArrayOfTasks];
        NSInteger idx = 0;
        for (NSInteger i = 0; i < [tasks count]; i++) {
            if ([tasks objectAtIndex:i] == currentTask) {
                idx = i;
                break;
            }
        }
        
        idx+=1;
        
        if (idx >= [tasks count]) idx = 0;
        
        Task *task = [tasks objectAtIndex:idx];
        
        [[GameManager sharedInstance] stopTaskTimer];
        
        self.task = task;
        
        [[GameManager sharedInstance] startTaskTimerForTask:self.task];
        [self updateSolvingPage];
    }
}

#pragma mark - Helper

- (void)prepareToPresentInfo
{
    NSInteger count = [self.task.expressions count];
    
    if ([self.task.solutions isEqualToString:kBothSolutionsType]) {
        [self.expressionsCountLabel setText:[NSString stringWithFormat:@"%d %@", count,
                                  NSLocalizedString(@"math expression", nil)]];
        [self.solutionCountLabel setText:[NSString stringWithFormat:@"%d %@", count,
                                             NSLocalizedString(@"math solution", nil)]];
    }
    else {
        [self.expressionsCountLabel setText:[NSString stringWithFormat:@"%d %@", count,
                                             NSLocalizedString(@"math expression", nil)]];
        [self.solutionCountLabel setHidden:YES];
    }

    self.levelNameLabel.text = self.task.level.name;
    self.problemNumberLabel.text = [self.task.numberTask stringValue];
}

- (BOOL)isHaveSameInActions:(NSArray *)actions
{
    BOOL isHaveSame = [actions any:^BOOL(Action *action) {
        
        NSMutableArray *actionsWithoutCurrentAction = [actions mutableCopy];
        [actionsWithoutCurrentAction removeObject:action];
        
        return [actionsWithoutCurrentAction any:^BOOL(Action *otherAction) {
            return [action isActionEqualToAction:otherAction];
        }];
    }];
    
    return isHaveSame;
}

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

- (void)configurateAnimationForTaks
{
    if ([self.task.animation length]) {
        // Animation exist
        [self.playAnimationButton setHidden:NO];
    } else {
        [self.playAnimationButton setHidden:YES];
    }
}

- (void)setImageErrorsIfNeeded
{
    if (self.task.status != kTaskStatusSolved && self.task.taskErrors.count > 0) {
        
        NSArray *expressionError = [[self.task.actions allObjects] select:^BOOL(Action *action) {
            return action.error != kActionErrorTypeNone &&
                   self.task.taskErrors.count > 0 &&
                   action.type == kActionTypeExpression;
        }];
        
        NSArray *solutionError = [[self.task.actions allObjects] select:^BOOL(Action *action) {
            return action.error != kActionErrorTypeNone &&
                   self.task.taskErrors.count > 0 &&
                   action.type == kActionTypeSolution;
        }];
        
        self.exprressionHaveErrorImageView.hidden = (expressionError.count <= 0);
        self.solutionHaveErrorImageView.hidden = (solutionError.count <= 0);
        
    } else {
        self.exprressionHaveErrorImageView.hidden = YES;
        self.solutionHaveErrorImageView.hidden = YES;
    }
}

- (void)setImageCompleteIfNeeded
{
    if (self.task.status == kTaskStatusSolved || self.task.status == kTaskStatusSolvedNotAll) {
        
        NSArray *expressionSolv = [[self.task.actions allObjects] select:^BOOL(Action *action) {
            return action.error == kActionErrorTypeNone &&  action.answer.length > 0 &&
            action.type == kActionTypeExpression;
        }];
        
        NSArray *solutionSolv = [[self.task.actions allObjects] select:^BOOL(Action *action) {
            return action.error == kActionErrorTypeNone && action.answer.length > 0 &&
            action.type == kActionTypeSolution;
        }];
        
        [self.completeImagesArray enumerateObjectsUsingBlock:^(UIImageView *obj, NSUInteger idx, BOOL *stop) {
            //0, 1 - indexes of images for expressions, so solutionIdx starts with 2
            NSUInteger solutionIdx = solutionSolv.count + 1;
            
            if (expressionSolv.count > idx) {
                obj.hidden = NO;
            } else if (idx > 1 && //0, 1 - indexes of images for expressions
                       [self.task.solutions isEqualToString:kBothSolutionsType] &&
                       solutionIdx >= idx && solutionSolv.count > 0) {
                obj.hidden = NO;
            } else {
                obj.hidden = YES;
            }
        }];
    } else {
        
        [self.completeImagesArray each:^(UIImageView *obj) {
            obj.hidden = YES;
        }];
    }
}


- (TaskError *)lastErrors
{
    NSArray *sortedArray = [[self.task.taskErrors allObjects] sortedArrayUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1, id<AbstractAchievement> obj2) {
        return [obj1.lastChangeDate timeIntervalSince1970GMT] > [obj2.lastChangeDate timeIntervalSince1970GMT];
    }];
    
    return [sortedArray lastObject];
}

- (BOOL)isTask:(Task *)task haveActionWithType:(ActionType)actionTye
{
    BOOL isHave = [task.actions any:^BOOL(Action *errorAction) {
        return errorAction.type == actionTye;
    }];
    
    return isHave;
}

- (void)saveTimeForTask
{
    GameManager *gameManager = [GameManager sharedInstance];
    
    self.task.secondsPerTask = [NSNumber numberWithInteger:[gameManager getSecondsForTask]];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)updateSolvingPage
{
    if (self.taskError == nil) {
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    }
    
    [self reloadSchemesToolsView];
    [self onTaskNumberTools:nil];
    [self.objective removeFromSuperview];
    
    [self.solvingPage.view removeFromSuperview];
    self.solvingPage = nil;
    
    [self.solvingSchemesPage.view removeFromSuperview];
    self.solvingSchemesPage = nil;
    
    //redrow error tutorial
    [[AnimationManager sharedInstance] hideErrorHelpAnimatios];
    
    if (!self.isTapEdit && !self.isTapError) {
        [[AnimationManager sharedInstance] showErrorTutorialIfNeededOnView:self.view forTask:self.task];
    }
    
    self.objective = [[ObjectiveView alloc] initWithTask:self.task andColor:[UIColor colorWithRed:0.98f green:0.80f blue:0.19f alpha:1]];
    
    if ((self.isCalledFromStatistic && !self.isTapEdit) && self.task.status != kTaskStatusSolved) {
        [GameManager sharedInstance].statisticMode = YES;
    } else {
        [GameManager sharedInstance].statisticMode = NO;
    }
    
    //disable input if detailed error viewing mode
    if (self.taskError) {
        self.solvingPage = [[SolvingSolutionViewController alloc] initWithAchievement:self.taskError];
        [self.numberToolsView.tools each:^(MTMovableView *sender) {
            sender.isMoveEnabled = NO;
        }];
        
    } else {
        self.solvingPage = [[SolvingSolutionViewController alloc] initWithAchievement:self.task];
    }
    
    self.solvingPage.delegate = self;
    
    [self.solvingView addSubview:self.solvingPage.view];
    
    self.solvingSchemesPage = [SolvingSchemesViewController new];
    self.solvingSchemesPage.task = self.task;


    self.solvingSchemesPage.toolsView = self.schemeToolsView;
    self.solvingSchemesPage.delegate = self;
    
    [self.schemeView addSubview:self.solvingSchemesPage.view];
    //not needed for 1st level, but there is in xib
    [self.numberToolsView excludeDisplayingCharacters:@[@"*", @"/", @"{", @"}"]];
    
    if ([self.task.letters count]) {
        
        if (self.letersToDelete.count) {
            [self.letersToDelete addObjectsFromArray:self.task.letters];
        } else {
            self.letersToDelete = [self.task.letters mutableCopy];
        }
        
        [self.numberToolsView excludeDisplayingCharacters:self.letersToDelete];
        [self.numberToolsView displayAdditionalViews:[self toolsViewForCurrentTask]];
    }
    
    //disable input if task solved
    if (self.task.status == kTaskStatusSolved || self.taskError != nil) {
        [self.numberToolsView.tools each:^(MTMovableView *sender) {
            sender.isMoveEnabled = NO;
        }];
    }
    
    if ([DataUtils isHaveErrorActionInTask:self.task]) {
        [self.errorsButton setHidden:NO];
    } else {
        [self.errorsButton setHidden:YES];
    }
    
    [self setImageErrorsIfNeeded];
    [self setImageCompleteIfNeeded];
    
    [self showEditButtonIFNeeded];
    [self prepareToPresentInfo];
    
    [self configurateAnimationForTaks];
    
    if (!self.isCalledFromStatistic && self.task.status != kTaskStatusSolved) {
        self.task.lastChangeDate = [NSDate date];
    }
    
    if (self.isViewLoaded) {
        [self.view addSubview:self.objective];
    }
}

- (void)showEditButtonIFNeeded
{
    if ((self.taskError && [DataUtils isHaveErrorActionInTask:self.task]) ||
                                                    [GameManager sharedInstance].statisticMode) {
        self.doneButton.hidden = YES;
        self.editButton.hidden = NO;
    }
}

- (void)reloadSchemesToolsView
{
    //force reinit
    self.movableViewsForTask = nil;
    
    //skip items, which have been placed to board
    NSArray *movableViewsForTask = [self movableViewsForTools];
    
    //NSLog(@"movableViewsForTask %@", movableViewsForTask);
    NSArray *tools = [self.schemeToolsView.displayedTools select:^BOOL(MTMovableView *tool) {
        return [movableViewsForTask any:^BOOL(MTMovableView *view) {
            return view.tag == tool.tag;
        }];
    }];
    
   // NSLog(@"tools: %@", tools);
    [self.schemeToolsView reloadDataWithViews:tools];
}

- (NSArray *)movableViewsForTools
{
    NSSet *notFilledElements = [self.taskScheme.elements select:^BOOL(SchemeElement *element) {
        return ![element.isFilled boolValue];
    }];
    
    NSSet *notFilledElementTypes = [notFilledElements  valueForKey:@"typeNumber"];
    
    NSArray *selectedTools = [self.movableViewsForTask select:^BOOL(MTMovableView *view) {
        return [notFilledElementTypes containsObject:@(view.tag)];
    }];
    
    return selectedTools;
}

- (NSArray *)movableViewsForBoard
{
    NSSet *notFilledElementTypes = [self.taskScheme.elements  valueForKey:@"typeNumber"];
    
    NSArray *selectedTools = [self.movableViewsForTask select:^BOOL(MTMovableView *view) {
        return [notFilledElementTypes containsObject:@(view.tag)];
    }];
    
    return selectedTools;
}

- (BOOL)isTaskTrening
{
    return [[[[[self.task.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
            isEqualToString:@"1"];
}

- (NSArray *)sortedElements
{
    return [[[self taskScheme].elements allObjects] sortedArrayUsingComparator:^NSComparisonResult(SchemeElement *obj1, SchemeElement *obj2) {
        return [obj1.identifier integerValue] > [obj2.identifier integerValue];
    }];
}

#pragma mark - SolvingSolutionViewControllerDelegate

- (void)didChangeComponent
{
    if ([self.task.helpIndex integerValue] == kTaskTutorial2) {
        self.task.helpIndex = @([self.task.helpIndex integerValue] + 1);
        [[AnimationManager sharedInstance] showHelpAnimatiosIfNeededWithTask:self.task onView:self.view];
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
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
                                        NSArray *actions = [self.task.actions allObjects];
                                        for (Action *action in actions) {
                                            action.error = kActionErrorTypeNone;
                                        }
                                        [self saveAndExit];
                                    }
                                }];
}

- (void)saveAndExit
{
    [self saveTimeForTask];
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [super goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}


#pragma mark - Alert Methods

- (void)showPopUpWithInfoWithOkButton:(BOOL)needOkButton
{
    Task *currentTask = self.task;
    
    BOOL taskHasErrorAction = NO;
    for (Action *action in [currentTask.actions allObjects]) {
        if ([action.errorNumber integerValue] != kActionErrorTypeNone) {
            taskHasErrorAction = YES;
            break;
        }
    }
    
    if (self.isCalledFromStatistic && !needOkButton) {
        [self saveAndExit];
        return;
    }

    
    if (taskHasErrorAction == YES) {
        
        [self.errorsButton setHidden:NO];
        self.popUpController = [[PopUpController alloc] initWithNibName:@"PopUpFail" bundle:nil];
        self.popUpController.delegate = self;
        self.popUpController.needShowOk = needOkButton;
        
        self.popUpController.errorActions = [currentTask.taskErrors allObjects];
        [self.view addSubview:self.popUpController.view];
        
        return;
    }
    
    if ([[currentTask.actions allObjects] count] > 0) {
        [self showSuccessPopupForTask:currentTask];
    }
}

- (void)showSuccessPopupForTask:(Task *)task
{
    // All answers are correct.
    [self.errorsButton setHidden:YES];
    
    //update flags to play animations if needed
    [self needToDisplayNextLevelsAnimation];
    [self needToDisplayTestLevelCompletionAnimation];
    
    self.popUpController = [[PopUpController alloc] initWithNibName:@"PopUpNextSolved" bundle:nil];
    self.popUpController.delegate = self;
    [self.view addSubview:self.popUpController.view];
    
    NSInteger totalScore = [task.currentScore integerValue];
    
    self.popUpController.pointsLabel.text = [NSString stringWithFormat:NSLocalizedString(@"%@ points from %@", @"Exercises page"),
                                        @(totalScore), self.task.score];
    
    NSInteger expressionsCount = [task.expressions count];
    
    if ([task.solutions isEqualToString:kBothSolutionsType]) {
        expressionsCount = expressionsCount * 2;
    }
    
    NSString *text = @"";
    
    if (expressionsCount == [task.countSolvedActions integerValue]) {
        text = NSLocalizedString(@"you have solved this problem with all possible solution and expression", @"Exercises page");
        self.popUpController.refreshButton.hidden = YES;
        
        self.popUpController.backButton.frame = CGRectMake(474, 591, self.popUpController.backButton.frame.size.width,
                                                      self.popUpController.backButton.frame.size.height);
        self.popUpController.nextButton.frame = CGRectMake(418, 449, self.popUpController.nextButton.frame.size.width,
                                                      self.popUpController.nextButton.frame.size.height);
        self.popUpController.nextLabel.frame = CGRectMake(430, 465, self.popUpController.nextLabel.frame.size.width,
                                                     self.popUpController.nextLabel.frame.size.height);
    } else {
        text = [NSString stringWithFormat:NSLocalizedString(@"find one more way to solve this problem and earn %d points more", @"Exercises page"),
                [task.score intValue] - totalScore];
    }
    
    self.popUpController.textViewDescription.text = text;
}

- (BOOL)needToDisplayNextLevelsAnimation
{
    Level *taskLevel = (Level *)self.task.level;
    
    BOOL needToDisplayNextLevelsAnimation = [self.task.taskType integerValue] == kTaskTypeTraining &&
    [[AnimationManager sharedInstance] needToDisplayNextLevelsForPath:taskLevel.path];
    
    if (needToDisplayNextLevelsAnimation) {
        taskLevel.path.isGrowingAnimated = @NO;
    }
    
    return needToDisplayNextLevelsAnimation;
}


- (BOOL)needToDisplayTestLevelCompletionAnimation
{
    Level *taskLevel = (Level *)self.task.level;
    
    BOOL needToDisplayTestLevelCompletionAnimation = [taskLevel.isTest boolValue] && [[AnimationManager sharedInstance] needToDisplayAnimationForTestLevel:taskLevel];
    
    if (needToDisplayTestLevelCompletionAnimation) {
        taskLevel.path.isStarAnimated = @NO;
    }
    
    return needToDisplayTestLevelCompletionAnimation;
}

#pragma mark - Setters&Getters

- (Scheme *)taskScheme
{
    Scheme *scheme = [self.task.child.schemes match:^BOOL(Scheme *obj) {
        return [obj.identifier isEqualToString:self.task.identifier];
    }];
    
    return scheme;
}

- (NSMutableArray *)movableViewsForTask
{
    if (!_movableViewsForTask) {
        NSSet *schemeElementTypes = [self.taskScheme.elements valueForKey:@"typeNumber"];
        
        NSArray *selectedTools = [self.schemeToolsView.displayedTools select:^BOOL(MTMovableView *view) {
            return [schemeElementTypes containsObject:@(view.tag)];
        }];
        
        _movableViewsForTask = [[NSMutableArray alloc] initWithArray:selectedTools
                                                           copyItems:YES];
        
        //sort by width
        [_movableViewsForTask sortUsingComparator:^NSComparisonResult(MTMovableView *obj1, MTMovableView *obj2) {
            return obj1.frame.size.width > obj2.frame.size.width;
        }];
    }

    return _movableViewsForTask;
}

- (BOOL)isSchemesCompleted
{
    BOOL isCompleted = NO;
    
    NSArray *completedSchem = [[[self taskScheme].elements allObjects] select:^BOOL(SchemeElement *obj) {
        return [obj.isFilled boolValue];
    }];
    
    isCompleted = [[self taskScheme].elements count] == [completedSchem count] && [[self taskScheme].elements count] > 0;
    
    return isCompleted;
}

- (NSArray *)toolsViewForCurrentTask
{
    NSMutableArray *additionalLabels = [NSMutableArray new];
    
    for (NSString *letter in self.task.letters) {
        UILabel *lblToCopy = (UILabel *)[self.numberToolsView.tools.lastObject carriedView];
        UILabel *letterLbl = [NSKeyedUnarchiver unarchiveObjectWithData:[NSKeyedArchiver archivedDataWithRootObject:lblToCopy]];
        
        letterLbl.frame    = CGRectMake(0, 0, 0, 0);
        letterLbl.text     = letter;
        [letterLbl sizeToFit];
        [additionalLabels addObject:letterLbl];
    }
    
    return additionalLabels;
}

@end
