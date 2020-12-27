//
//  AnimationManager.m
//  Mathematic
//
//  Created by alexbutenko on 8/12/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AnimationManager.h"
#import "MTLevelView.h"
#import "UIView+Animation.h"
#import "LevelsPath.h"
#import "TransitionsManager.h"
#import "Level.h"
#import "DataUtils.h"
#import "MTTaskButton.h"
#import "Task.h"
#import "LevelMapViewController.h"
#import "DWFParticleView.h"
#import "MTStarView.h"
#import "BlocksKit.h"
#import "GameManager.h"
#import "ChildManager.h"
#import "OlympiadLevel.h"
#import "OlympiadTask.h"
#import "MTScoreView.h"
#import "MTOlympiadCupButton.h"

typedef NS_ENUM(NSUInteger, ExSolvingButtonType) {
    ExSolvingButtonTypeAnimation = 11,
    ExSolvingButtonTypeHelp      = 12,
    ExSolvingButtonTypeScheme    = 13,
    ExSolvingButtonTypePencil    = 14,
    ExSolvingButtonTypeNext      = 15,
    ExSolvingButtonTypeError     = 16
};

@interface AnimationManager ()

@property (nonatomic, unsafe_unretained) BOOL isLevelsDisplayingAnimating;
@property (nonatomic, unsafe_unretained) BOOL isStarAnimating;

@property (nonatomic, strong) NSArray *levelsViews;
@property (nonatomic, strong) NSArray *starsViews;
@property (strong, nonatomic) DWFParticleView *olympiadParticleView;
@property (strong, nonatomic) NSMutableArray *olympiadTasksParticleViews;

@end

@implementation AnimationManager

+ (AnimationManager *)sharedInstance
{
    static dispatch_once_t pred;
    static AnimationManager *sharedInstance = nil;
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

- (void)prepareLevelViews:(NSArray *)views
               starsViews:(NSArray *)starsViews
              levelNumber:(NSNumber *)levelNumber
{
    self.levelsViews = views;
    self.starsViews = starsViews;
    
    //growing animation
    if (!self.isLevelsDisplayingAnimating) {
        [self hideIfNeededViews:views forLevelNumber:levelNumber];
    }
    
    //star animation
    if (!self.isStarAnimating) {
        [self markStars];
    }
}

- (void)playSEAnimationsIfNeededWithLevelViews:(NSArray *)views
                                    starsViews:(NSArray *)starsViews
                                   levelNumber:(NSNumber *)levelNumber
{
    self.levelsViews = views;
    self.starsViews = starsViews;
    
    [views each:^(MTLevelView *levelView) {
        [levelView stopPlayBellAnimation];
    }];
    
    //growing animation
    if (!self.isLevelsDisplayingAnimating) {
        [self hideIfNeededViews:views forLevelNumber:levelNumber];
        [self showIfNeededViews:views forLevelNumber:levelNumber];
    }
    
    //bell animation
    NSArray *unsolvedLevels = [DataUtils unsolvedLevelsFromCurrentChild];
    unsolvedLevels = [unsolvedLevels select:^BOOL(Level *level) {
        return [level.path.levelNumber isEqualToNumber:levelNumber];
    }];
    
    if (!self.isLevelsDisplayingAnimating) {
        [unsolvedLevels each:^(Level *level) {
            MTLevelView *levelView = [views match:^BOOL(MTLevelView *levelView) {
                return [levelView.level.identifier isEqualToString:level.identifier];
            }];
            
            if (levelView.frame.size.height > 0) {
                [levelView startPlayBellAnimation];
            }
        }];
    }
    
    //star animation
    if (!self.isStarAnimating) {
        [self markStars];
        [self showStarAnimationIfNeeded];
    }
}

- (void)playExercisesAnimationsIfNeededWithViews:(NSArray *)views
                                           level:(Level *)level
{
    Task *unsolvedTrainingTask = [DataUtils firstUnsolvedTrainingTaskForLevel:level];
    
    [views each:^(MTTaskButton *taskButton) {
        [taskButton stopPlayBellAnimation];
    }];
    
    if (unsolvedTrainingTask) {
        MTTaskButton *unsolvedTaskButton = [views match:^BOOL(MTTaskButton *taskButton) {
            return [taskButton.task.identifier isEqualToString:unsolvedTrainingTask.identifier];
        }];
        
        [unsolvedTaskButton startPlayBellAnimation];
    }
}

- (void)playExSolvingAnimationsIfNeededWithViews:(NSArray *)views
                                            task:(Task *)task
{
    NSArray *viewsToAnimate = nil;
    
    [views each:^(UIView *view) {
        [view stopPlayBellAnimation];
    }];
    
    CGFloat rotation = 0.06;
    
    if (task.status == kTaskStatusSolved) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypeNext;
        }];
        
        rotation = 0.15;
    } else if (task.status == kTaskStatusSolvedNotAll) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypePencil;
        }];
    } else if (task.status == kTaskStatusError) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypeHelp ||
                   view.tag == ExSolvingButtonTypeError;
        }];
    } else if (![task.animation isEqualToString:@""] && ![task.isAnimationSelected boolValue]) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypeAnimation;
        }];
        
        rotation = 0.2;
    } else if (![task.isHelpSelected boolValue]) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypeHelp;
        }];
    } else if (![task.isSchemeSelected boolValue]) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypeScheme;
        }];
    } else if (![task.isPencilSelected boolValue]) {
        viewsToAnimate = [views select:^BOOL(UIView *view) {
            return view.tag == ExSolvingButtonTypePencil;
        }];
    }
    
    [viewsToAnimate each:^(UIView *view) {
        [view startPlayBellAnimationWithRotation:rotation];
    }];
}

- (void)playSolvingAnimationsIfNeededWithViews:(NSArray *)views
                                          task:(Task *)task
{
    [views each:^(UIView *view) {
        [view stopPlayBellAnimation];
    }];
    
    if ([task.solutions isEqualToString:@"Both"]) {
        
        [self animateIfNeededViewWithActionType:kActionTypeSolution
                                        forTask:task
                                      fromViews:views];
        [self animateIfNeededViewWithActionType:kActionTypeExpression
                                        forTask:task
                                      fromViews:views];
    }
}

- (void)animateIfNeededViewWithActionType:(ActionType)actionType
                                  forTask:(Task *)task
                                fromViews:(NSArray *)views
{
    NSArray *actionsOfType = [DataUtils actionsOfType:actionType
                                          fromActions:[task.actions allObjects]];
    
    if ([actionsOfType count] < [task.expressions count]) {
        UIButton *actionButton = [views match:^BOOL(UIButton *view) {
            return view.tag == actionType;
        }];
        
        [actionButton startPlayBellAnimation];
    }
}

- (void)playRegistrationAnimationsIfNeededWithViews:(NSArray *)views
{
    [views each:^(UIView *view) {
        [view startPlayBellAnimation];
    }];
}

- (void)playOlympiadLevelAnimationsIfNeededWithViews:(NSArray *)views level:(OlympiadLevel *)level
{
    if (!self.olympiadTasksParticleViews) {
        self.olympiadTasksParticleViews = [NSMutableArray new];
    } else {
        [self.olympiadTasksParticleViews each:^(DWFParticleView *view) {
            [view removeFromSuperview];
        }];
        [self.olympiadTasksParticleViews removeAllObjects];
    }
    
    NSArray *unsolvedTasks = [DataUtils unsolvedTasksFromTasks:[level sortedArrayOfTasks]];
    
    [unsolvedTasks each:^(OlympiadTask *task) {
        MTScoreView *scoreView = [views match:^BOOL(MTScoreView *view) {
            return [view.task.identifier isEqualToNumber:task.identifier];
        }];
        
        //add particle view on parent (Task view)
        UIView *parentView = [scoreView superview];
        
        DWFParticleView *particleView = [[DWFParticleView alloc] initWithFrame:(CGRect){CGPointZero, parentView.frame.size}
                                                                         image:[UIImage imageNamed:@"Particles_fire1.png"]
                                                               emitterPosition:(CGPoint){170, 170}
                                                                   emitterSize:(CGSize){10, 10}
                                                                     birthRate:15
                                                             emittingBirthRate:0
                                                                 emissionRange:2*M_PI];
        [parentView addSubview:particleView];
        [self.olympiadTasksParticleViews addObject:particleView];
    }];
}

- (void)playOlympiadAnimationsIfNeededWithViews:(NSArray *)views levels:(NSArray *)levels
{
    //stop animations for all views
    [views each:^(MTOlympiadCupButton *button) {
        [button stopScaleRoundUp];
    }];
        
    NSArray *openOlympiadLevels = [DataUtils openOlympiadLevels];
    
    NSArray *unsolvedOlympiadLevels = [DataUtils unsolvedLevelsFromLevels:openOlympiadLevels];
    
    [unsolvedOlympiadLevels each:^(OlympiadLevel *level) {
        MTOlympiadCupButton *cupButton = [views match:^BOOL(MTOlympiadCupButton *button) {
            return [button.level.identifier isEqualToString:level.identifier];
        }];
        
        [cupButton scaleRoundUpWithDuration:0.4f
                                      scale:1.1f
                                      delay:0.0f
                                     repeat:YES];
    }];
}

- (void)playOlympiadTaskAnimationsIfNeededWithViews:(NSArray *)views task:(OlympiadTask *)task
{
    [views each:^(UIView *view) {
        [view stopPlayBellAnimation];
    }];
    
    if (task.status == kTaskStatusSolved) {
        [views each:^(UIView *view) {
            [view startPlayBellAnimationWithRotation:0.12];
        }];
    }
}

- (void)showAnimationIfNeededOnCharactersView:(UIView *)view
{
    if ([ChildManager sharedInstance].currentChild != nil && ![[ChildManager sharedInstance].currentChild.isTrainingComplete boolValue]) {
        
        UIButton *chactersButton = [view.subviews match:^BOOL(id obj) {
            return [obj isKindOfClass:[UIButton class]];
        }];
        
        self.olympiadParticleView = [[DWFParticleView alloc] initWithFrame:(CGRect){CGPointZero, 768, 1024}
                                                                     image:[UIImage imageNamed:@"Particles_fire1.png"]
                                                           emitterPosition:chactersButton.center
                                                               emitterSize:(CGSize){view.frame.size.width - 50,
                                                                                    view.frame.size.height - 50}
                                                                 birthRate:100
                                                         emittingBirthRate:0
                                                             emissionRange:M_PI_2];
        
        self.olympiadParticleView.fireEmitter.emitterMode = kCAEmitterLayerOutline;
        self.olympiadParticleView.fireEmitter.emitterShape = kCAEmitterLayerRectangle;
        self.olympiadParticleView.fireEmitter.renderMode = kCAEmitterLayerAdditive;
        
        [view insertSubview:self.olympiadParticleView belowSubview:chactersButton];
        
    } else {
        [self.olympiadParticleView removeFromSuperview];
        self.olympiadParticleView = nil;
    }
}

#pragma mark - Helper

#pragma mark - Growing animation

- (void)hideIfNeededViews:(NSArray *)views forLevelNumber:(NSNumber *)levelNumber
{
    //find path with unsolved training tasks for required level
    NSArray *notOpenedPaths = [DataUtils notOpenedLevelsPathsForLevelNumber:levelNumber];
    
    //hide all not opened paths dependent levels views
    [notOpenedPaths enumerateObjectsUsingBlock:^(LevelsPath *path, NSUInteger idx, BOOL *stop) {
        NSArray *levelsToHide = [path.levels allObjects];
        
        if (idx == 0) {
            levelsToHide = [levelsToHide reject:^BOOL(Level *level) {
                return [DataUtils isRequiredLevel:level] &&
                        [[ChildManager sharedInstance].currentChild.isTrainingComplete boolValue];
            }];
        }
        
        //mark to force animation playback later
//        path.isGrowingAnimated = @NO;
    
//        NSLog(@"levels to hide: %@ growingAnimated: %@ isOpened: %@", [levelsToHide valueForKey:@"identifier"], path.isGrowingAnimated, [path isOpened] ? @"YES":@"NO");
        
        [levelsToHide each:^(Level *level) {
            MTLevelView *levelView = [views match:^BOOL(MTLevelView *levelView) {
                return [level.identifier isEqualToString:levelView.level.identifier];
            }];
            
            if (levelView.bounds.size.height > 0) {
                levelView.clipsToBounds = YES;
                levelView.originalSize = levelView.frame.size;
                
                CGRect frame = levelView.frame;
                frame.origin.y = frame.origin.y + frame.size.height/2;
                levelView.frame = frame;
                
                levelView.bounds = (CGRect){CGPointZero, frame.size.width, 0};
            }
        }];
    }];
    
    //display already animated views
    NSArray *openedPaths = [DataUtils openedLevelsPathsForLevelNumber:levelNumber];

//    NSLog(@"openedPaths: %@", [openedPaths valueForKey:@"color"]);
    
    //display level views of opened paths
    [openedPaths each:^(LevelsPath *path) {
        [path.levels each:^(Level *level) {
            MTLevelView *levelViewToDisplay = [views match:^BOOL(MTLevelView *levelView) {
                return [levelView.level.identifier isEqualToString:level.identifier];
            }];
            
            if (levelViewToDisplay.frame.size.height == 0) {
                NSLog(@"displaying %@", level.identifier);
                levelViewToDisplay.frame = (CGRect){levelViewToDisplay.frame.origin.x, levelViewToDisplay.frame.origin.y - levelViewToDisplay.originalSize.height,
                                                    levelViewToDisplay.frame.size.width, levelViewToDisplay.originalSize.height};
            }
        }];
    }];
    
    LevelsPath *firstNotOpenedLevelsPath = nil;
    
    if ([notOpenedPaths count]) {
        firstNotOpenedLevelsPath = notOpenedPaths[0];
        
        //display required level of not opened path following last opened path
        if ([firstNotOpenedLevelsPath.identifier integerValue] > 1 ||
            //red path first level should be shown just after training is completed
            ([firstNotOpenedLevelsPath.identifier integerValue] == 1 &&
            [[ChildManager sharedInstance].currentChild.isTrainingComplete boolValue])) {
            Level *requiredLevelOfFirstNotOpenedPath = firstNotOpenedLevelsPath.requiredLevel;
            
            MTLevelView *levelViewToDisplay = [views match:^BOOL(MTLevelView *levelView) {
                return [levelView.level.identifier isEqualToString:requiredLevelOfFirstNotOpenedPath.identifier];
            }];
            
            if (levelViewToDisplay.frame.size.height == 0) {
                NSLog(@"displaying %@", requiredLevelOfFirstNotOpenedPath.identifier);
                levelViewToDisplay.frame = (CGRect){levelViewToDisplay.frame.origin.x, levelViewToDisplay.frame.origin.y - levelViewToDisplay.originalSize.height,
                                                    levelViewToDisplay.frame.size.width, levelViewToDisplay.originalSize.height};
            }
        }
    }
}

- (void)showIfNeededViews:(NSArray *)views
           forLevelNumber:(NSNumber *)levelNumber
{
    //find path with unsolved training tasks for required level
    NSArray *notOpenedLevelsPaths = [DataUtils notOpenedLevelsPathsForLevelNumber:levelNumber];
    
    LevelsPath *lastOpenedLevelsPath = [DataUtils lastOpenedLevelsPathForLevelNumber:levelNumber];
        
    //check that last opened path is displayed already
    NSArray *lastOpenedLevelsWithoutRequiredLevel = [[lastOpenedLevelsPath.levels allObjects] reject:^BOOL(Level *level) {
        return [DataUtils isRequiredLevel:level];
    }];
        
    NSArray *viewsToDisplay = [views select:^BOOL(MTLevelView *levelView) {
        BOOL isLevelJustOpened = [lastOpenedLevelsWithoutRequiredLevel any:^BOOL(Level *level) {
            return [level.identifier isEqualToString:levelView.level.identifier];
        }];
        
        return levelView.bounds.size.height == 0 && isLevelJustOpened;
    }];
        
    if ([viewsToDisplay count]) {

        //animate if not animated yet!
        BOOL shouldAnimate = ![lastOpenedLevelsPath.isGrowingAnimated boolValue];

        LevelsPath *firstNotOpenedPath = nil;
        
        //first not opened path after animation completion
        if (shouldAnimate) {
            firstNotOpenedPath = [notOpenedLevelsPaths count] > 1 ? notOpenedLevelsPaths[1] : nil;
        }
        
        if (firstNotOpenedPath) {
            //add required level view from first unopened path
            MTLevelView *nextRequiredLevelView = [views match:^BOOL(MTLevelView *requiredLevelView) {
                return [requiredLevelView.level.identifier isEqualToString:firstNotOpenedPath.requiredLevel.identifier];
            }];
            
            viewsToDisplay = [viewsToDisplay arrayByAddingObject:nextRequiredLevelView];
        }
        
        if (shouldAnimate) {
        
            static CGFloat const kChangeHeightDuration = 1.0f;
            static CGFloat const kScaleDuration = 0.2f;
            
            __block CGFloat totalDuration = 0.0f;
            
            self.isLevelsDisplayingAnimating = YES;
            lastOpenedLevelsPath.isGrowingAnimated = @YES;

            [GameManager.levelMap.theScrollView setZoomScale:GameManager.levelMap.theScrollView.minimumZoomScale
                                        animated:NO];
            
            [viewsToDisplay enumerateObjectsUsingBlock:^(MTLevelView *view, NSUInteger idx, BOOL *stop) {
                [view animatedChangeHeight:view.originalSize.height
                                  duration:kChangeHeightDuration
                                     delay:totalDuration];
                totalDuration += kChangeHeightDuration;
                
                //beginTime for animation doesn't work in some cases
                double delayInSeconds = totalDuration;
                dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
                dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                    [view scaleRoundUpWithDuration:kScaleDuration delay:0];
                });
            }];
            
            //additional delay by duration of change height for smooth effect
            double delayInSeconds = totalDuration + kChangeHeightDuration;
            dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
            dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                self.isLevelsDisplayingAnimating = NO;
                [GameManager.levelMap.theScrollView setZoomScale:GameManager.levelMap.theScrollView.maximumZoomScale
                                                        animated:YES];
                [self playSEAnimationsIfNeededWithLevelViews:self.levelsViews
                                                  starsViews:self.starsViews
                                                 levelNumber:@1];
            });
        }
    }
}

- (BOOL)needToDisplayNextLevelsForPath:(LevelsPath *)path
{
    if (path) {
        //get following path and check that its views are not displayed yet
        
        LevelsPath *lastOpenedLevelsPath = [DataUtils lastOpenedLevelsPathForLevelNumber:path.levelNumber];
        
        if ([lastOpenedLevelsPath.identifier isEqualToNumber:path.identifier]) {
        
            //check that last opened path is displayed already
            NSArray *lastOpenedLevelsWithoutRequiredLevel = [[lastOpenedLevelsPath.levels allObjects] reject:^BOOL(Level *level) {
                return [DataUtils isRequiredLevel:level];
            }];
        
            BOOL needsToDisplay = [self.levelsViews any:^BOOL(MTLevelView *levelView) {
                BOOL isLevelOpened = [lastOpenedLevelsWithoutRequiredLevel any:^BOOL(Level *level) {
                    return [level.identifier isEqualToString:levelView.level.identifier];
                }];
                
                return levelView.bounds.size.height == 0 && isLevelOpened;
            }];
        
            NSLog(@"needToDisplay levels appearance animation: %@", needsToDisplay ? @"YES":@"NO");
                
            return needsToDisplay;
        }
    }
    
    return NO;
}

#pragma mark - Star Animation

- (void)markStars
{
    //to avoid multiple completion animations, we mark as incomplete just stars for not solved levels
    //and then redraw them all according to isCompleted flag
    NSArray *unsolvedTestLevels = [DataUtils unsolvedTestLevelsFromCurrentChild];
    
//    NSLog(@"unsolvedTestLevels: %@", [unsolvedTestLevels valueForKey:@"identifier"]);
    
    [unsolvedTestLevels each:^(Level *testLevel) {
        MTStarView *starViewToComplete = [self.starsViews match:^BOOL(MTStarView *starView) {
            return [starView.level.identifier isEqualToString:testLevel.identifier];
        }];

        starViewToComplete.isCompleted = NO;
    }];
    
    NSArray *solvedTestLevels = [DataUtils solvedTestLevelsFromCurrentChild];
    
    //    NSLog(@"unsolvedTestLevels: %@", [unsolvedTestLevels valueForKey:@"identifier"]);
    
    [solvedTestLevels each:^(Level *testLevel) {
        MTStarView *starViewToComplete = [self.starsViews match:^BOOL(MTStarView *starView) {
            return [starView.level.identifier isEqualToString:testLevel.identifier];
        }];
        
        if ([testLevel.path.isStarAnimated boolValue]) {
            starViewToComplete.isCompleted = YES;
        }
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    [self.starsViews makeObjectsPerformSelector:@selector(updateView)];
}

- (void)showStarAnimationIfNeeded
{
    NSArray *solvedTestLevels = [DataUtils solvedTestLevelsFromCurrentChild];
    
    [solvedTestLevels each:^(Level *testLevel) {
        MTStarView *starViewToComplete = [self.starsViews match:^BOOL(MTStarView *starView) {
            return [starView.level.identifier isEqualToString:testLevel.identifier] &&
            !starView.isCompleted;
        }];
                
        //animate
        if (starViewToComplete && !self.isStarAnimating) {
//            NSLog(@"play star animation for level: %@", starViewToComplete.level.identifier);
            
            BOOL shouldAnimate = ![testLevel.path.isStarAnimated boolValue];
            
            if (shouldAnimate) {
                self.isStarAnimating = YES;
                testLevel.path.isStarAnimated = @YES;
                        
                [GameManager.levelMap.theScrollView setZoomScale:GameManager.levelMap.theScrollView.minimumZoomScale
                                                        animated:NO];
                
                //add particle view on parent (Level screen)
                UIView *parentView = [starViewToComplete superview];
                
                DWFParticleView *particleView = [[DWFParticleView alloc] initWithFrame:parentView.frame];
                [parentView addSubview:particleView];
                
                MTLevelView *levelView = [self.levelsViews match:^BOOL(MTLevelView *levelView) {
                    return [levelView.level.identifier isEqualToString:testLevel.identifier];
                }];
                
                CGFloat kStarAnimationDuration = 3.0f;
                CGFloat kScaleDuration = 0.2f;
                
                //convert from levelViewController to levelMap
                CGPoint levelViewOriginConvertedToParentView = [parentView convertPoint:levelView.frame.origin
                                                                               fromView:levelView.superview];
                
                CGPoint starCenterOffsetAtLevelView = (CGPoint){31, 36};
                CGPoint startPoint = (CGPoint){levelViewOriginConvertedToParentView.x + starCenterOffsetAtLevelView.x,
                                               levelViewOriginConvertedToParentView.y + starCenterOffsetAtLevelView.y};
                
                CGPoint endPoint = starViewToComplete.center;

                [particleView moveAnimatedFromPoint:startPoint
                                            toPoint:endPoint
                                           duration:kStarAnimationDuration];
                
                double delayInSeconds = kStarAnimationDuration + kScaleDuration;
                dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
                
                dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                    starViewToComplete.isCompleted = YES;
                    [starViewToComplete updateView];
                    [starViewToComplete scaleRoundUpWithDuration:kScaleDuration delay:0];
                    
                    double delayInSeconds = 1.5 + kScaleDuration;
                    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
                    dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                        [particleView removeFromSuperview];
                        [GameManager.levelMap.theScrollView setZoomScale:GameManager.levelMap.theScrollView.maximumZoomScale
                                                                animated:YES];
                        self.isStarAnimating = NO;
                    });
                });
            } else {
                starViewToComplete.isCompleted = YES;
                [starViewToComplete updateView];
            }
        }
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }];
}

- (BOOL)needToDisplayAnimationForTestLevel:(Level *)testLevel
{
    BOOL needToDisplay = [testLevel.isAllTasksSolved boolValue];
    
    if (needToDisplay) {
        needToDisplay = [self.starsViews any:^BOOL(MTStarView *starView) {
            return [starView.level.identifier isEqualToString:testLevel.identifier] && !starView.isCompleted;
        }];
    }

    return needToDisplay;
}

@end
