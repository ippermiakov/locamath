//
//  UIView+Animation.h
//  Mathematic
//
//  Created by alexbutenko on 8/9/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UIView (Animation)

@property (nonatomic, strong) NSNumber *isBellAnimating;
@property (nonatomic, strong) NSTimer *bellAnimationRestartTimer;
@property (nonatomic, strong) NSNumber *currentRotation;

- (void)startPlayBellAnimation;
- (void)startPlayBellAnimationWithRotation:(CGFloat)rotation;
- (void)stopPlayBellAnimation;

- (void)animatedChangeHeight:(CGFloat)height
                    duration:(CGFloat)duration
                       delay:(CGFloat)delay;
- (void)scaleRoundUpWithDuration:(CGFloat)duration delay:(CGFloat)delay;
- (void)scaleRoundUpWithDuration:(CGFloat)duration
                           scale:(CGFloat)scale
                           delay:(CGFloat)delay
                          repeat:(BOOL)shouldRepeat;
- (void)stopScaleRoundUp;

- (void)startHelpGrowAnimationWithOffset:(CGPoint)offset;


@end
