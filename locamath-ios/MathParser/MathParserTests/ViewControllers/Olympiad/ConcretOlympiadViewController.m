//
//  ConcretOlympiadViewController.m
//  Mathematic
//
//  Created by Developer on 12.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ConcretOlympiadViewController.h"
#import "OlympiadExercisesPageViewController.h"
#import "OlympiadLevel.h"
#import "OlympiadTask.h"
#import "LWRatingView.h"
#import "MTScoreView.h"
#import "DebugMode.h"
#import "MBProgressHUD+Mathematic.h"
#import "AnimationManager.h"

@interface ConcretOlympiadViewController ()

@property (weak, nonatomic) IBOutlet UIImageView *cupImageView;
@property (strong, nonatomic) IBOutlet UIButton *solvingButton;
- (IBAction)onSolving:(id)sender;

@end

@implementation ConcretOlympiadViewController

- (id)initWithAchievement:(OlympiadLevel *)level
{
    self = [super init];
    
    if (self) {
        self.level = level;
    }
    
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.stars.tasks = self.level.sortedArrayOfTasks;
    
    self.scores = [self.scores sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 tag] > [obj2 tag];
    }];
        
    [self.cupImageView setImage:[UIImage imageNamed:self.level.image]];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self updateMe];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

- (void)viewDidUnload
{
    [self setStars:nil];
    [self setScores:nil];
    [self setSolvingButton:nil];
    [super viewDidUnload];
}

- (void)updateMe
{
#ifdef DEBUG
    self.solvingButton.hidden = NO;
#endif
    self.stars.maxRating = self.level.tasks.count > 0 ? self.level.tasks.count : kDefaultStarsCount;
    
    self.stars.rating = [[self.level.tasks select:^BOOL(OlympiadTask *task) {
         return [task isCorrect];
    }] count];
    
    NSArray *tasks = [self.level sortedArrayOfTasks];
        
    for (NSInteger i = 0, end = tasks.count; i < end; ++i) {
        OlympiadTask *task = tasks[i];
        
        MTScoreView *scoreView = self.scores[i];

        scoreView.task = task;
    }
    
    [[AnimationManager sharedInstance] playOlympiadLevelAnimationsIfNeededWithViews:self.scores
                                                                              level:self.level];
}

#pragma mark Main IBActions

- (IBAction)onTapProblemButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    OlympiadExercisesPageViewController *exercisesPageViewController = [[OlympiadExercisesPageViewController alloc] init];
    
    exercisesPageViewController.level = self.level;
    exercisesPageViewController.task  = [self taskWithIndex:[sender tag]];
              
    [self presentModalViewController:exercisesPageViewController animated:YES];
}

- (IBAction)onTapBackHome:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onSolving:(id)sender
{
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];
    
    [DebugMode solveOlympiadLevel:self.level
               withUpdateToServer:YES
                         progress:^(CGFloat progress) {
//                             [HUD updateWithProgress:progress];
    }
                           finish:^{
                               [self updateMe];
//                               [HUD hideOnSyncCompletion];
    }
                          failure:^(NSError *error) {
                              [self updateMe];
//                              [HUD hideOnSyncFailure];
    }];
}

#pragma mark - Helper

- (OlympiadTask *)taskWithIndex:(NSUInteger)idx
{
    return [self.level sortedArrayOfTasks][idx - 1];
}

@end
