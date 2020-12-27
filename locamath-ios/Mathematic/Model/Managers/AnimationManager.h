//
//  AnimationManager.h
//  Mathematic
//
//  Created by alexbutenko on 8/12/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class Level, Task, OlympiadLevel, OlympiadTask;

static NSInteger const kSchemeTutorial1 = 1;
static NSInteger const kSchemeTutorial2 = 2;
static NSInteger const kTaskTutorial1 = 3;
static NSInteger const kTaskTutorial2 = 4;

@interface AnimationManager : NSObject

+ (AnimationManager *)sharedInstance;

- (void)prepareLevelViews:(NSArray *)views
               starsViews:(NSArray *)starsViews
              levelNumber:(NSNumber *)levelNumber;

- (void)playSEAnimationsIfNeededWithLevelViews:(NSArray *)views
                                    starsViews:(NSArray *)starsViews
                                   levelNumber:(NSNumber *)levelNumber;

- (void)playExercisesAnimationsIfNeededWithViews:(NSArray *)views
                                           level:(Level *)level;

- (void)playExSolvingAnimationsIfNeededWithViews:(NSArray *)views
                                            task:(Task *)task;

- (void)playSolvingAnimationsIfNeededWithViews:(NSArray *)views
                                          task:(Task *)task;

- (void)playRegistrationAnimationsIfNeededWithViews:(NSArray *)views;
- (void)playOlympiadLevelAnimationsIfNeededWithViews:(NSArray *)views level:(OlympiadLevel *)level;
- (void)playOlympiadAnimationsIfNeededWithViews:(NSArray *)views levels:(NSArray *)levels;
- (void)playOlympiadTaskAnimationsIfNeededWithViews:(NSArray *)views task:(OlympiadTask *)task;

- (BOOL)needToDisplayNextLevelsForPath:(LevelsPath *)path;
- (BOOL)needToDisplayAnimationForTestLevel:(Level *)testLevel;
- (void)showAnimationIfNeededOnCharactersView:(UIView *)view;

//help animations
- (void)showHelpAnimatiosIfNeededWithTask:(Task *)tesk onView:(UIView *)view;
- (void)hideHelpAnimatios;
- (void)showErrorTutorialIfNeededOnView:(UIView *)view forTask:(Task *)task;
- (void)hideErrorHelpAnimatios;
@end
