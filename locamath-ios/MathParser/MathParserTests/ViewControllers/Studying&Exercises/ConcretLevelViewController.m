//
//  ConcretLevelViewController.m
//  Mathematic
//
//  Created by Developer on 23.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ConcretLevelViewController.h"
#import "ExercisesPageViewController.h"
#import "MTLevelView.h"
#import "MTTaskButton.h"
#import "Level.h"
#import "Task.h"
#import "DataUtils.h"
#import "DebugMode.h"
#import "TransitionsManager.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"
#import "AnimationManager.h"

#define SIZE_BUTTON 200.0f
#define MARGIN_BUTTON 40.0f

@interface ConcretLevelViewController ()

@property (weak, nonatomic)   IBOutlet UIScrollView *theScrollView;
@property (strong, nonatomic) UIImageView *levelImageView;
@property (strong, nonatomic) Level *level;
@property (strong, nonatomic) MTLevelView *levelView;
@property (strong, nonatomic) NSArray *tasks;
@property (strong, nonatomic) ExercisesPageViewController *exercisesPageViewController;

@property (strong, nonatomic) IBOutlet UIButton *solvingButton;
@property (weak, nonatomic) IBOutlet UIButton *solvingTrainingButton;
@property (strong, nonatomic) IBOutlet UIImageView *backgroudImage;
@property (strong, nonatomic) IBOutlet UIImageView *borderImage;

- (IBAction)onTapBack:(id)sender;
- (IBAction)onSolving:(id)sender;
- (IBAction)onSolvingTraining:(id)sender;

@end

@implementation ConcretLevelViewController

- (id)initWithAchievement:(Level *)level
{
    self = [super init];
    if (self) {
        self.levelImageView = [[UIImageView alloc] init];
        self.level = level;
    }
    return self;
}

- (void)dealloc
{
    NSLog(@"clear ConcretLevel");
}

#pragma mark - UIViewController lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    [self configImagesForConcretLevel];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(goToNextTaskFromCurentTask:)
                                                 name:kNotificationGoToTheNextTask
                                               object:nil];
    
    self.tasks = [self.level sortedArrayOfTasks];
    [self createButtons];
    [self displayTotalCurrentScore];
    
    NSArray *taskButtons = [self.theScrollView.subviews select:^BOOL(UIView *view) {
        return [view isKindOfClass:[MTTaskButton class]];
    }];
    
    [[AnimationManager sharedInstance] playExercisesAnimationsIfNeededWithViews:taskButtons level:self.level];
    
#ifdef DEBUG
    self.solvingButton.hidden = NO;
    self.solvingTrainingButton.hidden = NO;
#endif
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [self createButtons];
    [self displayTotalCurrentScore];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setTheScrollView:nil];
    [self setSolvingButton:nil];
    [self setSolvingTrainingButton:nil];
    [self setBackgroudImage:nil];
    [self setBorderImage:nil];
    [super viewDidUnload];
}

#pragma mark - Actions Methods

- (IBAction)onTapBack:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [super goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}

- (IBAction)onSolving:(id)sender
{
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];
    
    [DebugMode solveLevel:self.level
                 progress:^(CGFloat progress) {
//                     [HUD updateWithProgress:progress];
    }
                   finish:^{
                       self.tasks = [self.level sortedArrayOfTasks];
                       [self createButtons];
                       [self displayTotalCurrentScore];
//                       [HUD hideOnSyncCompletion];
    }
                  failure:^(NSError *error) {
//                      [HUD hideOnSyncFailure];
    }];
}

- (IBAction)onSolvingTraining:(id)sender
{
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];
    
    [DebugMode solveTrainingTasksForLevel:self.level
                                 progress:^(CGFloat progress) {
//                                     [HUD updateWithProgress:progress];
    }
                                   finish:^{
                                       self.tasks = [self.level sortedArrayOfTasks];
                                       [self createButtons];
                                       [self displayTotalCurrentScore];
//                                       [HUD hideOnSyncCompletion];
    }
                                  failure:^(NSError *error) {
//                                      [HUD hideOnSyncFailure];
                                  }];
}

#pragma mark - Helper

- (void)configImagesForConcretLevel
{
    CGSize correctSize = CGSizeMake(200, 100);
    if (![[self.level isTest] boolValue]) {
        [self.levelImageView setImage:[UIImage imageNamed:self.level.image]];
        correctSize = CGSizeMake(100, 100);
    }
    
    CGSize sizeImage = self.levelImageView.image.size;
    [self.levelImageView setFrame:CGRectMake(correctSize.width, correctSize.height, sizeImage.width / 2.0f, sizeImage.height / 2.0f)];
    
    [self.theScrollView addSubview:self.levelImageView];
    [self.theScrollView sendSubviewToBack:self.levelImageView];
    
    CGRect rect = self.levelImageView.frame;
    self.levelView = [NSKeyedUnarchiver unarchiveObjectWithData:self.dataLevelView];
    
    [self.levelView setFrame:CGRectMake(rect.origin.x + rect.size.width - 50.0f, 50.0f,
                                        self.levelView.frame.size.width, self.levelView.frame.size.height)];
    self.levelView.userInteractionEnabled = NO;
    [self.view addSubview:self.levelView];
}

- (void)createButtons
{
    // Remove all buttons before drawing buttons agian.
    
    [self updateBackgroundImageForLevel];
    
    for (UIView *view in [self.theScrollView subviews]) {
        if ([view isKindOfClass:[MTTaskButton class]]) {
            [view removeFromSuperview];
        }
    }
    
    //TODO: replace magic numbers with consts
    CGPoint pointTopRow;
    pointTopRow.x = self.levelImageView.frame.origin.x + self.levelImageView.frame.size.width + 50;
    pointTopRow.y = 150.0f;
    
    CGPoint pointBottomRow;
    pointBottomRow.x = self.levelImageView.frame.origin.x + 100;
    pointBottomRow.y = 450.0f;
    
    NSInteger i = 0;
    CGFloat correctCoef = 1;
    if (self.levelType == kLevelType2) {
        correctCoef = 0.5f;
    }
    
    //TODO: move displaying logic to MTTaskButton
    for (Task *task in self.tasks) {
        
        MTTaskButton *button = [[MTTaskButton alloc] init];
        button.task = task;
        button.tag = i;
        [button addTarget:self action:@selector(onTapLevel:) forControlEvents:UIControlEventTouchUpInside];
        UIColor *color = [self checkForAColor:task];
        
        if ([[[[[task.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
             isEqualToString:@"1"]) {
            button.type = kTaskButtonTypeTraining;
            [button setBackgroundImage:[self currentTreningTaskImageForLevel] forState:UIControlStateNormal];
            UILabel *trainingLabel = [[UILabel alloc] initWithFrame:CGRectMake(69.0f* correctCoef, 125.0f * correctCoef, 93.0f, 21.0f)];
            [trainingLabel setBackgroundColor:[UIColor clearColor]];
            [trainingLabel setTextColor:color];
            [trainingLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:13.0f]];
            [trainingLabel setTextAlignment:UITextAlignmentLeft];
            [trainingLabel setText:NSLocalizedString(@"Training", @"S&E level page label")];
            [trainingLabel setAdjustsFontSizeToFitWidth:YES];
            [button addSubview:trainingLabel];
            UILabel *problemLabel = [[UILabel alloc] initWithFrame:CGRectMake(64.0f* correctCoef, 125.0f* correctCoef + 15, 75.0f, 21.0f)];
            [problemLabel setBackgroundColor:[UIColor clearColor]];
            [problemLabel setTextColor:color];
            [problemLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:13.0f]];
            [problemLabel setTextAlignment:UITextAlignmentLeft];
            [problemLabel setText:[NSString stringWithFormat:@"%@ %d", NSLocalizedString(@"problem", @"S&E level page label"),
                                   [task.numberTask integerValue]]];
            [button addSubview:problemLabel];
        }
        else {
            button.type = kTaskButtonTypeCommon;
            [button setBackgroundImage:[self currentTaskImageForLevel] forState:UIControlStateNormal];
            UILabel *problemLabel = [[UILabel alloc] initWithFrame:CGRectMake(75.0f * correctCoef, 125.0f * correctCoef, 75.0f, 21.0f)];
            [problemLabel setBackgroundColor:[UIColor clearColor]];
            [problemLabel setTextColor:color];
            [problemLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:13.0f]];
            [problemLabel setTextAlignment:UITextAlignmentLeft];
            [problemLabel setText:[NSString stringWithFormat:@"%@", NSLocalizedString(@"Problem", @"S&E level page label")]];
            [button addSubview:problemLabel];
            UILabel *numberLabel = [[UILabel alloc] initWithFrame:CGRectMake(87.0f* correctCoef, 125.0f* correctCoef + 15, 75.0f, 21.0f)];
            [numberLabel setBackgroundColor:[UIColor clearColor]];
            [numberLabel setTextColor:color];
            [numberLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:13.0f]];
            [numberLabel setTextAlignment:UITextAlignmentLeft];
            [numberLabel setText:[NSString stringWithFormat:@"%d", [task.numberTask integerValue]]];
            [button addSubview:numberLabel];
        }
        
        if ([task.numberTask integerValue]%2 == 0) {
            pointTopRow.y = 200.0f + (int)(arc4random() % 60) - 40;
            [button setFrame:CGRectMake(pointTopRow.x, pointTopRow.y, SIZE_BUTTON, SIZE_BUTTON)];
            pointTopRow.x += SIZE_BUTTON + MARGIN_BUTTON  + (int)(arc4random() % 30) - 10;
        } else {
            pointBottomRow.y = 450.0f + (int)(arc4random() % 60) - 40;
            [button setFrame:CGRectMake(pointBottomRow.x, pointBottomRow.y, SIZE_BUTTON, SIZE_BUTTON)];
            pointBottomRow.x += SIZE_BUTTON + MARGIN_BUTTON  + (int)(arc4random() % 30) - 10;
        }
        
        [self correctRectForTaskButton:button];
        
        CGRect rect1 = CGRectMake(132.0f, 77.0f, 45.0f, 21.0f);
        CGRect rect2 = CGRectMake(130.0f, 97.0f, 45.0f, 21.0f);
        
        if (self.levelType == kLevelType2) {
            if (button.type == kTaskButtonTypeCommon) {
                rect1 = CGRectMake(87.0f, 15.0f, 45.0f, 21.0f);
                rect2 = CGRectMake(85.0f, 35.0f, 45.0f, 21.0f);
            } else {
                rect1 = CGRectMake(142.0f, 20.0f, 45.0f, 21.0f);
                rect2 = CGRectMake(140.0f, 40.0f, 45.0f, 21.0f);
            }
        }
        UILabel *currentScoreLabel = [[UILabel alloc] initWithFrame:rect1];
        [currentScoreLabel setBackgroundColor:[UIColor clearColor]];
        [currentScoreLabel setTextColor:color];
        [currentScoreLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:15.0f]];
        [currentScoreLabel setTextAlignment:UITextAlignmentCenter];
        [currentScoreLabel setText:[NSString stringWithFormat:@"%@", task.currentScore]];
        
        
        [button addSubview:currentScoreLabel];
        
        UILabel *totalScoreLabel = [[UILabel alloc] initWithFrame:rect2];
        [totalScoreLabel setBackgroundColor:[UIColor clearColor]];
        [totalScoreLabel setTextColor:color];
        [totalScoreLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:15.0f]];
        [totalScoreLabel setTextAlignment:UITextAlignmentCenter];
        [totalScoreLabel setText:[NSString stringWithFormat:@"%@", task.score]];
        [button addSubview:totalScoreLabel];
        
        [self.theScrollView addSubview:button];
        
        i++;
        
    }
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    [self.theScrollView setContentSize:CGSizeMake(MAX(pointTopRow.x, pointBottomRow.x), 0.0f)];
}

- (UIColor *)checkForAColor:(Task *)task
{
    if (task.status == kTaskStatusSolved) {
        return [UIColor whiteColor];
    }
    
    if (task.status == kTaskStatusStarted || task.status == kTaskStatusError || task.status == kTaskStatusSolvedNotAll) {
        return [UIColor colorWithRed:246.0f/255.0f green:235.0f/255.0f blue:1.0f/255.0f alpha:1.0f];
    }
    
    return [UIColor colorWithRed:44.0f/255.0f green:191.0f/255.0f blue:191.0f/255.0f alpha:1.0f];
    
}

- (void)onTapLevel:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[4] loop:NO];
 
    NSError *error = nil;
    Task *task = [self.tasks objectAtIndex:[sender tag]];
                  
    if ([[TransitionsManager sharedInstance] canOpenTask:task error:&error]) {

        if (self.exercisesPageViewController) self.exercisesPageViewController = nil;
        
        self.exercisesPageViewController = [[ExercisesPageViewController alloc] init];
        
        self.exercisesPageViewController.level = self.level;
        self.exercisesPageViewController.numberTask = [NSString stringWithFormat:@"%d", [sender tag]];
        
        self.exercisesPageViewController.task = task;
        self.exercisesPageViewController.levelType = self.levelType;
        self.exercisesPageViewController.backDelegate = self;
        
        [self presentViewController:self.exercisesPageViewController animated:YES completion:nil];
    } else {
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
    }
}

- (void)displayTotalCurrentScore
{
    self.levelView.solvedTasksLabel.text = [NSString stringWithFormat:@"%@", self.level.countSolvedTasks];
    self.levelView.startedTasksLabel.text = [NSString stringWithFormat:@"%@", self.level.countStartedTasks];
    [self.levelView.currentScoreLabel setText:[NSString stringWithFormat:@"%d", [self.level.currentScore integerValue]]];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

- (void)goToNextTaskFromCurentTask:(NSNotification *)notification
{
    Task *currentTask = [notification object];
    NSInteger idx = 0;
    for (NSInteger i = 0; i < [self.tasks count]; i++) {
        if ([self.tasks objectAtIndex:i] == currentTask) {
            idx = i;
            break;
        }
    }
    
    idx+=1;
    
    if (idx >= [self.tasks count]) idx = 0;
    
    NSError *error = nil;
    Task *task = [self.tasks objectAtIndex:idx];
    
    if ([[TransitionsManager sharedInstance] canOpenTask:task error:&error]) {
    
        if (self.exercisesPageViewController) self.exercisesPageViewController = nil;
        
        self.exercisesPageViewController = [[ExercisesPageViewController alloc] init];
        
        self.exercisesPageViewController.level = self.level;
        
        self.exercisesPageViewController.numberTask = [NSString stringWithFormat:@"%d", idx];
        self.exercisesPageViewController.backDelegate = self;
        
        self.exercisesPageViewController.task = task;
        [self presentViewController:self.exercisesPageViewController animated:YES completion:nil];
    } else {
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
    }
}

- (void)updateBackgroundImageForLevel
{
    switch (self.levelType) {
        case kLevelType1:
            self.backgroudImage.image = [UIImage imageNamed:@"concret_level_BACKGROUND_FILL.png"];
            self.borderImage.image = [UIImage imageNamed:@"!_1st_LEVEL_BACKGROUND_FRAME@2x.png"];
            break;
            
        case kLevelType2:
            self.backgroudImage.image = [UIImage imageNamed:@"Back_Gray@2x.png"];
            self.borderImage.image = [UIImage imageNamed:@"!_2nd_LEVEL_BACKGROUND_FRAME@2x.png"];
            break;
            
        default:
            break;
    }
}

- (UIImage *)currentTreningTaskImageForLevel
{
    UIImage *image = nil;
    
    switch (self.levelType) {
        case kLevelType1:
            image = [UIImage imageNamed:@"TrainingProblem.png"];
            break;
            
        case kLevelType2:
            image = [UIImage imageNamed:@"TrainingProblem_lev2@2x.png"];
            break;
            
        default:
            break;
    }
    
    return image;
}

- (UIImage *)currentTaskImageForLevel
{
    UIImage *image = nil;
    
    switch (self.levelType) {
        case kLevelType1:
            image = [UIImage imageNamed:@"Problem.png"];
            break;
            
        case kLevelType2:
            image = [UIImage imageNamed:@"Problem_lev2@2x.png"];
            break;
            
        default:
            break;
    }
    
    return image;
}

- (CGRect)correctRectForTaskButton:(MTTaskButton *)taskButton
{
    if (self.levelType == kLevelType2) {
        if (taskButton.type == kTaskButtonTypeCommon) {
        UIImage *imageForConcretLevel = [UIImage imageNamed:@"Problem_lev2@2x.png"];
        
        taskButton.frame = CGRectMake(taskButton.frame.origin.x,
                                      taskButton.frame.origin.y,
                                      imageForConcretLevel.size.width * 0.5,
                                      imageForConcretLevel.size.height * 0.5);
        } else {
            UIImage *imageForConcretLevel = [UIImage imageNamed:@"TrainingProblem_lev2@2x.png"];
            
            taskButton.frame = CGRectMake(taskButton.frame.origin.x,
                                          taskButton.frame.origin.y,
                                          imageForConcretLevel.size.width * 0.5,
                                          imageForConcretLevel.size.height * 0.5);
        }
    }

    return CGRectNull;
}

#pragma mark - BaseViewControllerDelegate

- (void)didFinishBackWithOption:(BOOL)option
{
    self.exercisesPageViewController = nil;
}

@end

