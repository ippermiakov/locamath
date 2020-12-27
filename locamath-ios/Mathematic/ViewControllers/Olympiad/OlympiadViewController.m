//
//  OlympiadViewController.m
//  Mathematic
//
//  Created by Developer on 11.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadViewController.h"
#import "ConcretOlympiadViewController.h"
#import "LWRatingView.h"
#import "OlympiadLevel.h"
#import "DataUtils.h"
#import "TransitionsManager.h"
#import "GameAlertViewController.h"
#import "MTOlympiadCupButton.h"
#import "AnimationManager.h"
#import "PopupForDefaultChildViewController.h"

@interface OlympiadViewController ()
@property (strong, nonatomic) IBOutletCollection(MTOlympiadCupButton) NSArray *olympiadCupButtons;

@end

@implementation OlympiadViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    for (LWRatingView *ratingView in self.stars) {
        ratingView.maxRating = 4;
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setStars:nil];
    [self setOlympiadCupButtons:nil];
    [super viewDidUnload];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self updateView];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [self playAnimationIfNeeded];
}

#pragma mark - Actions

- (IBAction)onTapHomeButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onTapCupButton:(id)sender
{    
    NSArray *levels = DataUtils.olympiadLevelsFromCurrentChild;
    
    NSUInteger selectedLevelIndex = [sender tag] - 1;
    
    OlympiadLevel *olympiadLevel = levels[selectedLevelIndex];
    
    if (selectedLevelIndex < [levels count] && [olympiadLevel.tasks count]) {
        
        NSError *error = nil;
        
        if ([[TransitionsManager sharedInstance] canOpenOlympiadLevel:olympiadLevel
                                                                error:&error]) {
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[4] loop:NO];
            
            ConcretOlympiadViewController *concretOlympiadViewController = [[ConcretOlympiadViewController alloc] initWithAchievement:olympiadLevel];
                   
            [self presentModalViewController:concretOlympiadViewController animated:YES];
        } else {
            [GameAlertViewController showGameAlertWithMessageError:error withPresenter:self.view];
        }
    }
    else {
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[5] loop:NO];
        
        [GameAlertViewController showGameAlertWithMessage:NSLocalizedString(@"Coming soon", nil) withPresenter:self.view];
    }
}

- (void)updateView
{
    NSArray *sortedLevels = DataUtils.olympiadLevelsWithTasksFromCurrentChild;
    
    NSArray *sortedStars  = [self.stars sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 tag] > [obj2 tag];
    }];
    
    NSArray *sortedCups = [self sortedCups];
    
    for (NSUInteger i = 0, end = sortedLevels.count; i < end; ++i) {
        
        OlympiadLevel *level = sortedLevels[i];
        LWRatingView *ratingView = sortedStars[i];
        MTOlympiadCupButton *cupButton = sortedCups[i];
        
        ratingView.maxRating = level.sortedArrayOfTasks.count > 0 ? level.tasks.count : kDefaultStarsCount;
        ratingView.tasks = level.sortedArrayOfTasks;
        [sortedStars[i] setRating:[[level.sortedArrayOfTasks select:^BOOL(OlympiadTask *obj) {
            return [obj isCorrect];
        }] count] ];
        
        cupButton.level = level;
    }
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [self updateViewAndPlayAnimationIfNeeded];
}

- (void)playAnimationIfNeeded
{
    [[AnimationManager sharedInstance] playOlympiadAnimationsIfNeededWithViews:[self sortedCups]
                                                                        levels:DataUtils.olympiadLevelsWithTasksFromCurrentChild];
}

- (void)updateViewAndPlayAnimationIfNeeded
{
    [self updateView];
    [self playAnimationIfNeeded];
}

#pragma mark - Helper

- (NSArray *)sortedCups
{
    return [self.olympiadCupButtons sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 tag] > [obj2 tag];
    }];
}

@end
