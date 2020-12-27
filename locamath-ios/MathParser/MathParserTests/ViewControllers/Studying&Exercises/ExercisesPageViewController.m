//
//  ExercisesPage.m
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ExercisesPageViewController.h"
#import "SolvingViewController.h"
#import "PopUpController.h"
#import "SchemeViewController.h"
#import "AnimationViewController.h"

#import "Task.h"
#import "Level.h"
#import "LevelsPath.h"
#import "Action.h"
#import "HelpPage.h"

#import "PresentingSeguesStructure.h"
#import "PresentableViewController.h"
#import "HelpChainBuilder.h"
#import "ChildManager.h"
#import "DebugMode.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"
#import "PopUpControllerDelegate.h"
#import "AnimationManager.h"
#import "UIViewController+DismissViewController.h"
#import "TTTAttributedLabel+MTTextView.h"
#import "Scheme.h"
#import "SchemeElement.h"

@interface ExercisesPageViewController ()<UITableViewDelegate, UITableViewDataSource, PopUpControllerDelegate>


@property (strong, nonatomic) SchemeViewController *schemeViewController;
@property (strong, nonatomic) NSFetchedResultsController *tasksFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *solutionFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *expressionFetchedResultsController;
- (IBAction)onSolving:(id)sender;
@property (strong, nonatomic) IBOutlet UIButton *solvingButton;
@property (strong, nonatomic) IBOutlet UIImageView *backgroundImage;
@property (strong, nonatomic) IBOutlet UILabel *schemeLabelComingSoon;

@property (weak, nonatomic) IBOutlet TTTAttributedLabel *textViewObjective;
@property (weak, nonatomic) IBOutlet UIImageView *imageView;
@property (weak, nonatomic) IBOutlet UITableView *theTableView;

@property (weak, nonatomic) IBOutlet UILabel *labelSolutionCount;
@property (weak, nonatomic) IBOutlet UILabel *labelExpressionCount;
@property (weak, nonatomic) IBOutlet UILabel *labelExpression;
@property (weak, nonatomic) IBOutlet UILabel *labelAnswer;
@property (weak, nonatomic) IBOutlet UILabel *levelLabel;
@property (weak, nonatomic) IBOutlet UIButton *errorButton;

@property (weak, nonatomic) IBOutlet UIButton *buttonAnimation;
@property (weak, nonatomic) IBOutlet UILabel *labelNumberTask;
@property (weak, nonatomic) IBOutlet UIButton *helpButton;
@property (weak, nonatomic) IBOutlet UIButton *schemeButton;
@property (weak, nonatomic) IBOutlet UIButton *pencilButton;
@property (weak, nonatomic) IBOutlet UIView *nextButtonView;
@property (weak, nonatomic) IBOutlet UILabel *testLabel;
@property (weak, nonatomic) IBOutlet UIImageView *schemeImageView;
@property (unsafe_unretained, nonatomic) BOOL isHaveScheme;
@property (strong, nonatomic) SolvingViewController   *solvingViewController;


- (IBAction)onTapStartSolve:(id)sender;
- (IBAction)onTapBackHome:(id)sender;
- (IBAction)onTapNextSolutionButton:(id)sender;
- (IBAction)onTapPrevSolutionButton:(id)sender;
- (IBAction)onTapPrevExpButton:(id)sender;
- (IBAction)onTapNextExpButton:(id)sender;
- (IBAction)onTapSolutionButton:(id)sender;
- (IBAction)onTapExpressionButton:(id)sender;
- (IBAction)onTapErrorButton:(id)sender;
- (IBAction)onTapNextTask:(id)sender;
- (IBAction)onTapScheme:(id)sender;
- (IBAction)onTapPlayAnimation:(id)sender;
- (IBAction)onTapHelp:(id)sender;
@property (weak, nonatomic) IBOutlet UIImageView *schemeWindowImageView;

@end

@implementation ExercisesPageViewController {
    
    PopUpController         *popUpController;
    NSInteger               solutionIterator;
    NSInteger               expressionIterator;
    NSMutableDictionary     *alertInfo;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)dealloc
{
    NSLog(@"clear Exercise Page");
}

#pragma mark - Main View Method

- (void)viewDidLoad
{
    Scheme *scheme = [self.task.child.schemes match:^BOOL(Scheme *obj) {
        return [obj.identifier isEqualToString:self.task.identifier];
    }];
    
    self.isHaveScheme = YES;
    
    if (nil == scheme) {
        self.schemeLabelComingSoon.text =  NSLocalizedString(@"Coming soon", nil);
        self.isHaveScheme = NO;
    }
    
    [self updateLevelBackgroundImage:self.backgroundImage];
    
    [self.labelNumberTask setText:[NSString stringWithFormat:@"%@", self.task.numberTask]];

    [self.imageView setImage:[UIImage imageNamed:self.level.image]];
    self.levelLabel.text = self.level.name;
    
    if ([self.level.isTest boolValue]) {
        self.testLabel.hidden = NO;
        self.testLabel.text = self.level.name;
    }
    
    [self configurateAnimationForTaks];
    [self updateMe];
    
    [self.tasksFetchedResultsController performFetch:NULL];
    NSArray *tasks = [self.tasksFetchedResultsController fetchedObjects];
    
    Task *currentTask;
    
    for (Task *task in tasks) {
        currentTask = task;
    }
    
    BOOL taskHasErrorAction = NO;
    for (Action *action in [currentTask.actions allObjects]) {
        if ([action.errorNumber integerValue] != kActionErrorTypeNone) {
            taskHasErrorAction = YES;
            break;
        }
    }
    
    if (taskHasErrorAction == YES) {
        [self.errorButton setHidden:NO];
    }
    
    [super viewDidLoad];
    
    [self.textViewObjective MTTextViewWithLabel:self.textViewObjective withTask:self.task forView:self.view];

#ifdef DEBUG
    self.solvingButton.hidden = NO;
#endif
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    [self updateMe];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setTextViewObjective:nil];
    [self setImageView:nil];
    [self setTheTableView:nil];
    [self setLabelSolutionCount:nil];
    [self setLabelExpression:nil];
    [self setLabelExpressionCount:nil];
    [self setLevelLabel:nil];
    [self setLabelAnswer:nil];
    [self setErrorButton:nil];
    [self setErrorButton:nil];
    [self setButtonAnimation:nil];
    [self setLabelNumberTask:nil];

    [self setSolvingButton:nil];
    [self setBackgroundImage:nil];
    [self setHelpButton:nil];
    [self setSchemeButton:nil];
    [self setPencilButton:nil];
    [self setNextButtonView:nil];
    [self setTestLabel:nil];
    [self setSchemeImageView:nil];
    [self setSchemeWindowImageView:nil];
    [self setSchemeLabelComingSoon:nil];
    [super viewDidUnload];
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

#pragma mark - Animation Methods

- (void)configurateAnimationForTaks
{
    if ([self.task.animation length]) {
        // Animation exist
        [self.buttonAnimation setHidden:NO];
    }
}

#pragma mark - Alert Methods

- (void)showPopUpWithInfo
{
    [self.tasksFetchedResultsController performFetch:NULL];
    NSArray *tasks = [self.tasksFetchedResultsController fetchedObjects];
    
    Task *currentTask = [tasks lastObject];
    
    BOOL taskHasErrorAction = NO;
    for (Action *action in [currentTask.actions allObjects]) {
        if ([action.errorNumber integerValue] != kActionErrorTypeNone) {
            taskHasErrorAction = YES;
            break;
        }
    }
    
    if (taskHasErrorAction == YES) {
        
        [self.errorButton setHidden:NO];
        popUpController = [[PopUpController alloc] initWithNibName:@"PopUpFail" bundle:nil];
        popUpController.delegate = self;
        
        popUpController.errorActions = [currentTask.taskErrors allObjects];
        [self.view addSubview:popUpController.view];
        
        return;
    }
    
    if ([[currentTask.actions allObjects] count] > 0) {
        [self showSuccessPopupForTask:currentTask];
    }    
}

#pragma mark - PopUpControllerDelegate

- (void)popOverDidTapOkButton
{
    [popUpController.view removeFromSuperview];
    popUpController = nil;
}

- (void)popOverDidTapRestoreButton
{
    [popUpController.view removeFromSuperview];
    popUpController = nil;
    [self showSolvingPage];
}

- (void)popOverDidTapHomeButton
{
    [popUpController.view removeFromSuperview];
    popUpController = nil;
}

- (void)popOverDidTapNextButton
{
    [popUpController.view removeFromSuperview];
    popUpController = nil;
    
    Level *taskLevel = (Level *)self.task.level;
    
    Task *lastTaskForLevel = [[taskLevel sortedArrayOfTasks] lastObject];
    BOOL isLastTask = [lastTaskForLevel.identifier isEqualToString:self.task.identifier];
    
    BOOL needToDisplayNextLevelsAnimation = [self needToDisplayNextLevelsAnimation];
    BOOL needToDisplayTestLevelCompletionAnimation = [self needToDisplayTestLevelCompletionAnimation];
    
    NSLog(@"needToDisplayNextLevelsAnimation: %@ needToDisplayTestLevelCompletionAnimation: %@ isLastTask: %@", needToDisplayNextLevelsAnimation ? @"YES":@"NO", needToDisplayTestLevelCompletionAnimation ? @"YES":@"NO", isLastTask ? @"YES":@"NO");
    
    if (needToDisplayNextLevelsAnimation || needToDisplayTestLevelCompletionAnimation || isLastTask) {
        [self dismissGameFlowViewControllersWithViewController:self];
    } else {
        [self dismissViewControllerAnimated:NO completion:nil];
        [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationGoToTheNextTask object:self.task];
    }
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

#pragma mark - Main IBAction Methods

- (IBAction)onTapStartSolve:(id)sender
{
    self.task.isPencilSelected = @YES;
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self showSolvingPage];
}

- (IBAction)onTapBackHome:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];

    [self goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}

- (IBAction)onTapPrevSolutionButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.solutionFetchedResultsController performFetch:NULL];
    NSArray *solutions = [self.solutionFetchedResultsController fetchedObjects];
    
    solutionIterator = [solutions count];
    
    solutionIterator--;
    if (solutionIterator <= 1 && [solutions count]) {
        solutionIterator = 1;
    } else solutionIterator = [solutions count];
    
    self.labelSolutionCount.text = [NSString stringWithFormat:@"%d", solutionIterator];
    [self.theTableView reloadData];
    [self setActualFonts];
}

- (IBAction)onTapNextSolutionButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.solutionFetchedResultsController performFetch:NULL];
    NSArray *solutions = [self.solutionFetchedResultsController fetchedObjects];
    solutionIterator = [solutions count];
    solutionIterator++;
    
    if (solutionIterator >= [solutions count] && [solutions count]) {
        solutionIterator = MAX(1, [solutions count]);
    } else solutionIterator = [solutions count];
    
    self.labelSolutionCount.text = [NSString stringWithFormat:@"%d", solutionIterator];
    [self.theTableView reloadData];
    [self setActualFonts];
}

- (IBAction)onTapPrevExpButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.expressionFetchedResultsController performFetch:NULL];
    NSArray *expressions = [self.expressionFetchedResultsController fetchedObjects];
    expressionIterator = [expressions count];
    expressionIterator--;
    
    if (expressionIterator <= 1 && [expressions count]) {
        expressionIterator =  1;
    } else expressionIterator = [expressions count];
    
    self.labelExpressionCount.text = [NSString stringWithFormat:@"%d", expressionIterator];
    
    if (expressions.count > 0) {
        Action *action = [expressions objectAtIndex:expressionIterator - 1];
        if (action.subActions.count > 0) {
            self.labelExpression.text = [[action.subActions objectAtIndex:0] string];
        }
    }    
}

- (IBAction)onTapNextExpButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.expressionFetchedResultsController performFetch:NULL];
    NSArray *expressions = [self.expressionFetchedResultsController fetchedObjects];
    expressionIterator = [expressions count];
    expressionIterator++;
    
    if (expressionIterator >= [expressions count] && [expressions count]) {
        expressionIterator = MAX(1, [expressions count]);
    } else expressionIterator = [expressions count];
    
    self.labelExpressionCount.text = [NSString stringWithFormat:@"%d", expressionIterator];
    
    if (expressions.count > 0) {
        Action *action = [expressions objectAtIndex:expressionIterator - 1];
        if (action.subActions.count > 0) {
            self.labelExpression.text = [[action.subActions objectAtIndex:0] string];
        }
    }    
}

- (IBAction)onTapSolutionButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self showSolvingPage];
}

- (IBAction)onTapExpressionButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self showSolvingPage];
}

- (IBAction)onTapErrorButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self showPopUpWithInfo];
}

- (IBAction)onTapNextTask:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.tasksFetchedResultsController performFetch:NULL];
    NSArray *tasks = [self.tasksFetchedResultsController fetchedObjects];

    Task *currentTask = [tasks lastObject];

    if (currentTask.status == kTaskStatusSolved || currentTask.status == kTaskStatusSolvedNotAll) {
        [self showSuccessPopupForTask:currentTask];
    } else {
        // All answers are not correct.
        if(currentTask.status == kTaskStatusError) 
        [self.errorButton setHidden:NO];
        
        popUpController = [[PopUpController alloc] initWithNibName:@"PopUpNextFail" bundle:nil];
        popUpController.delegate = self;
        popUpController.errorActions = currentTask.actionsWithError;
        [self.view addSubview:popUpController.view];
    }
    
}

- (IBAction)onTapScheme:(id)sender
{
    if (self.isHaveScheme) {
        self.task.isSchemeSelected = @YES;
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
        
        SchemeViewController *schemeViewController = [[SchemeViewController alloc] initWithTask:self.task andTaskNumber:self.numberTask];
        schemeViewController.onDoneBlock = ^{
            [self updateSchemeImageIfNeeded];
        };
        
        [self presentModalViewController:schemeViewController animated:YES];
    }
}

- (IBAction)onTapPlayAnimation:(id)sender
{
    self.task.isAnimationSelected = @YES;
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    AnimationViewController *animationVC = [[AnimationViewController alloc] initWithAnimationFileName:self.task.animation];
    [self presentModalViewController:animationVC animated:YES];
}

- (IBAction)onTapHelp:(id)sender
{
    self.task.isHelpSelected = @YES;
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    PresentingSeguesStructure *seguesStructure = [HelpChainBuilder helpChainWithLevelID:self.task.level.identifier];
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (IBAction)onSolving:(id)sender
{
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];
    
    [DebugMode solveTask:self.task
                progress:^(CGFloat progress) {
//                    [HUD updateWithProgress:progress];
    }
                  finish:^{
                      [self updateMe];
//                      [HUD hideOnSyncCompletion];
    }
                 failure:^(NSError *error) {
                     [self updateMe];
//                     [HUD hideOnSyncFailure];
     }];
}

#pragma mark - UITableViewDelegate

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    [self.solutionFetchedResultsController performFetch:NULL];
    NSArray *solutions = [self.solutionFetchedResultsController fetchedObjects];
    NSInteger count = 0;
    
    if ([solutions count] && solutionIterator > 0) {
        solutionIterator--;
    }
    
    if ([solutions count] > 0) {
        Action *action = [solutions objectAtIndex:solutionIterator];
        count = [action.subActions count];
    }
    
    return count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    [self.solutionFetchedResultsController performFetch:NULL];
    NSArray *solutions = [self.solutionFetchedResultsController fetchedObjects];
    
    Action *action = [solutions objectAtIndex:solutionIterator];

    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellIdentifier];
    }
    cell.backgroundColor = [UIColor clearColor];
    [cell.textLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20.0f]];
    [cell.textLabel setTextColor:[UIColor colorWithRed:0.0/255.0 green:138.0/255.0 blue:131.0/255.0 alpha:1.0]];
    
    cell.textLabel.text = [NSString stringWithFormat:@"%d) %@", indexPath.row + 1, [[action.subActions objectAtIndex:indexPath.row] string]];
    return cell;
}

#pragma mark - Helper

- (void)updateMe
{
    if (self.view) {
    
        solutionIterator = 0;
        expressionIterator = 0;
        
        self.labelExpressionCount.text = [NSString stringWithFormat:@"%d", 0];
        
        self.labelSolutionCount.text = [NSString stringWithFormat:@"%d", 0];
        
        self.labelAnswer.text = @"";
        
        
        [self.expressionFetchedResultsController performFetch:NULL];
        NSArray *expressions = [self.expressionFetchedResultsController fetchedObjects];
        
        [self.solutionFetchedResultsController performFetch:NULL];
        NSArray *solutions = [self.solutionFetchedResultsController fetchedObjects];    
           
        [self.tasksFetchedResultsController performFetch:NULL];
        NSArray *tasks = [self.tasksFetchedResultsController fetchedObjects];

        Task *currentTask = [tasks lastObject];

        if (currentTask.status == kTaskStatusSolved || currentTask.status == kTaskStatusSolvedNotAll) {
            
            if ([[currentTask.actions allObjects] count] > 0) {
                Action *action = [[currentTask.actions allObjects] objectAtIndex:0];
                self.labelAnswer.text = [NSString stringWithFormat:@"%@: %@", NSLocalizedString(@"Answer", nil), action.answer];
            } else {
                self.labelAnswer.text = @"";
            }
        }
        
        if (currentTask.status == kTaskStatusError) {
            [self.errorButton setHidden:NO];
        }

        if ([expressions count] > 0) {
            self.labelExpressionCount.text = @"1";
        }
        
        if ([solutions count] > 0) {
            self.labelSolutionCount.text = @"1";
        }
        
       
        if ([expressions count] > 0) {
            Action *action = [expressions objectAtIndex:0];
            Action *subAction = nil;
            if (action.subActions.count > 0) {
                subAction = [action.subActions objectAtIndex:0];
                self.labelExpression.text = subAction.string;
            }
        } else {
            self.labelExpression.text = @"";
        }
        
        [self.theTableView reloadData];
        
        NSArray *buttonsArray = @[self.buttonAnimation, self.helpButton, self.schemeButton, self.pencilButton, self.nextButtonView, self.errorButton];
        [[AnimationManager sharedInstance] playExSolvingAnimationsIfNeededWithViews:buttonsArray
                                                                               task:self.task];
        [self updateSchemeImageIfNeeded];
    }
}

- (void)updateSchemeImageIfNeeded
{
    Scheme *scheme = [self.task.child.schemes match:^BOOL(Scheme *obj) {
        return [obj.identifier isEqualToString:self.task.identifier];
    }];
    
    BOOL isSchemeCompleted = [scheme.elements all:^BOOL(SchemeElement *obj) {
        return [[obj isFilled] boolValue];
    }];
    
    if (isSchemeCompleted) {
        
        UIImage *image = [self imageForScheme];
        self.schemeImageView.image = image;
        CGRect schemeImageViewFrame = self.schemeImageView.frame;
        //retina size images only for now
        schemeImageViewFrame.size = (CGSize){image.size.width/2, image.size.height/2};
        self.schemeImageView.frame = schemeImageViewFrame;

        CGPoint center = self.schemeImageView.center;
        center.x = self.schemeWindowImageView.center.x;
        self.schemeImageView.center = center;
    }
}

- (void)showSolvingPage
{
    self.solvingViewController = [[SolvingViewController alloc] initWithAchievement:self.task];
    self.solvingViewController.backDelegate = self;
    self.solvingViewController.levelType = self.levelType;
    [self presentViewController:self.solvingViewController animated:YES completion:nil];
}

- (void)showSuccessPopupForTask:(Task *)task
{
    // All answers are correct.
    [self.errorButton setHidden:YES];
    
    //update flags to play animations if needed
    [self needToDisplayNextLevelsAnimation];
    [self needToDisplayTestLevelCompletionAnimation];
    
    popUpController = [[PopUpController alloc] initWithNibName:@"PopUpNextSolved" bundle:nil];
    popUpController.delegate = self;
    [self.view addSubview:popUpController.view];
    
    NSInteger totalScore = [task.currentScore integerValue];
    
    popUpController.pointsLabel.text = [NSString stringWithFormat:NSLocalizedString(@"%@ points from %@", @"Exercises page"),
                                        @(totalScore), self.task.score];
    
    NSInteger expressionsCount = [task.expressions count];
    
    if ([task.solutions isEqualToString:kBothSolutionsType]) {
        expressionsCount = expressionsCount * 2;
    }
    
    NSString *text = @"";
    
    if (expressionsCount == [task.countSolvedActions integerValue]) {
        text = NSLocalizedString(@"you have solved this problem with all possible solution and expression", @"Exercises page");
        popUpController.refreshButton.hidden = YES;
        
        popUpController.backButton.frame = CGRectMake(474, 591, popUpController.backButton.frame.size.width,
                                                      popUpController.backButton.frame.size.height);
        popUpController.nextButton.frame = CGRectMake(418, 449, popUpController.nextButton.frame.size.width,
                                                      popUpController.nextButton.frame.size.height);
        popUpController.nextLabel.frame = CGRectMake(430, 465, popUpController.nextLabel.frame.size.width,
                                                     popUpController.nextLabel.frame.size.height);
    } else {
        text = [NSString stringWithFormat:NSLocalizedString(@"find one more way to solve this problem and earn %d points more", @"Exercises page"),
                [task.score intValue] - totalScore];
    }
    
    popUpController.textViewDescription.text = text;
}

#pragma mark - Setters&Getters

- (NSFetchedResultsController *)tasksFetchedResultsController
{
    if (!_tasksFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"identifier == %@ && child.identifier == %@", self.task.identifier, [ChildManager sharedInstance].currentChild.identifier];
        
        self.tasksFetchedResultsController = [Task fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _tasksFetchedResultsController;
}

- (NSFetchedResultsController *)solutionFetchedResultsController
{
    if (!_solutionFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"typeNumber == %@ AND task == %@", [NSNumber numberWithInteger:kActionTypeSolution], self.task];
        
        self.solutionFetchedResultsController = [Action fetchAllSortedBy:@"typeNumber" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];;
    }
    return _solutionFetchedResultsController;
}

- (NSFetchedResultsController *)expressionFetchedResultsController
{
    if (!_expressionFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"typeNumber == %@ AND task == %@", [NSNumber numberWithInteger:kActionTypeExpression], self.task];

        self.expressionFetchedResultsController = [Action fetchAllSortedBy:@"typeNumber" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _expressionFetchedResultsController;
}

- (UIImage *)imageForScheme
{
    NSArray  *documentPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
    NSString *documentsDir  = [documentPaths objectAtIndex:0];
    NSString *outputPath    = [documentsDir stringByAppendingPathComponent:@"/images"];
    NSString *imgPath = [outputPath stringByAppendingPathComponent:[NSString stringWithFormat:@"/image%@.png", self.task.identifier]];
    
    UIImage *imageTemp = [UIImage imageWithContentsOfFile:imgPath];
    
    if (nil == imageTemp) {
        imageTemp = [UIImage imageNamed:self.task.schemeImageName];
    }
    
    return imageTemp;
}

@end