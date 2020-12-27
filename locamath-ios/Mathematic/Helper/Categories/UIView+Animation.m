//
//  UIView+Animation.m
//  Mathematic
//
//  Created by alexbutenko on 8/9/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UIView+Animation.h"
#import <QuartzCore/QuartzCore.h>
#import <objc/runtime.h>

static char * const kIsBellAnimating = "isBellAnimating";
static char * const kBellRestartAnimationTimerKey = "bellAnimationRestartTimer";
static char * const kRotationKey = "rotation";

@implementation UIView (Animation)

#pragma mark - Bell

#pragma mark - Setters&Getters

- (void)setIsBellAnimating:(NSNumber *)isBellAnimating
{
    objc_setAssociatedObject(self, kIsBellAnimating, isBellAnimating, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

- (NSNumber *)isBellAnimating
{
    NSNumber *isBellAnimationEnabled = objc_getAssociatedObject(self, kIsBellAnimating);
    
    return isBellAnimationEnabled;
}

- (void)setBellAnimationRestartTimer:(NSTimer *)bellAnimationRestartTimer
{
    objc_setAssociatedObject(self, kBellRestartAnimationTimerKey, bellAnimationRestartTimer, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

- (NSTimer *)bellAnimationRestartTimer
{
    NSTimer *timer = objc_getAssociatedObject(self, kBellRestartAnimationTimerKey);
    
    return timer;
}

- (NSNumber *)currentRotation
{
    NSNumber *currentRotation = objc_getAssociatedObject(self, kRotationKey);
    
    if (!currentRotation) {
        return @(0.06);
    }
    
    return currentRotation;
}

- (void)setCurrentRotation:(NSNumber *)currentRotation
{
    objc_setAssociatedObject(self, kRotationKey, currentRotation, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

- (void)startPlayBellAnimation
{
    [self startPlayBellAnimationWithRotation:[self.currentRotation floatValue]];
}

- (void)startPlayBellAnimationWithRotation:(CGFloat)rotation
{
    if (![self.isBellAnimating boolValue]) {
        self.isBellAnimating = @YES;

        self.currentRotation = @(rotation);
        CGFloat duration = 0.13;
        CGFloat repeatCount = 10;
        CGFloat pauseDuration = 2;
        CGFloat totalDuration = (duration * repeatCount) * 2;
        
        CGFloat nextAnimationDelay = pauseDuration + totalDuration;
        
        CABasicAnimation *shake = [CABasicAnimation animationWithKeyPath:@"transform"];
        shake.duration = duration;
        shake.autoreverses = YES;
        shake.repeatCount  = 10;
        shake.removedOnCompletion = YES;
        shake.fromValue = [NSValue valueWithCATransform3D:CATransform3DRotate(self.layer.transform,-rotation, 0.0 ,0.0 ,1.0)];
        shake.toValue   = [NSValue valueWithCATransform3D:CATransform3DRotate(self.layer.transform, rotation, 0.0 ,0.0 ,1.0)];
        
        shake.delegate = self;
        
        [self.layer addAnimation:shake forKey:@"shakeAnimation"];
        
        if (!self.bellAnimationRestartTimer) {
            self.bellAnimationRestartTimer = [NSTimer scheduledTimerWithTimeInterval:nextAnimationDelay
                                                                              target:self
                                                                            selector:@selector(startPlayBellAnimation)
                                                                            userInfo:nil
                                                                             repeats:YES];
        }
    }
}

- (void)stopPlayBellAnimation
{
    self.isBellAnimating = @NO;
    [self.layer removeAnimationForKey:@"shakeAnimation"];
    
    [self.bellAnimationRestartTimer invalidate];
    self.bellAnimationRestartTimer = nil;
}

#pragma mark - CAAnimationDelegate

- (void)animationDidStop:(CAAnimation *)anim finished:(BOOL)flag
{
    self.isBellAnimating = @NO;
}

#pragma mark - Change size

- (void)changeSizeToSize:(CGSize)size
                duration:(float)secs
                   delay:(float)delay
{
    CGFloat newCenterY = self.center.y - (size.height/2 - self.frame.size.height);

    CABasicAnimation *positionAnimation = [CABasicAnimation animationWithKeyPath:@"position"];
    positionAnimation.removedOnCompletion = NO;
    positionAnimation.fromValue = [NSValue valueWithCGPoint:self.center];
    positionAnimation.toValue = [NSValue valueWithCGPoint:CGPointMake(self.center.x, newCenterY)];
    
    CABasicAnimation *boundsAnimation = [CABasicAnimation animationWithKeyPath:@"bounds"];

    boundsAnimation.removedOnCompletion = NO;
    boundsAnimation.fromValue = [NSValue valueWithCGRect:self.bounds];
    boundsAnimation.toValue = [NSValue valueWithCGRect:(CGRect){CGPointZero, self.bounds.size.width, size.height}];
    
    CAAnimationGroup *theGroup = [CAAnimationGroup animation];

    theGroup.animations = @[positionAnimation, boundsAnimation];
    theGroup.timingFunction = [CAMediaTimingFunction functionWithName:kCAMediaTimingFunctionEaseInEaseOut];
    
    theGroup.duration = secs;
    theGroup.beginTime = CACurrentMediaTime() + delay;
    theGroup.removedOnCompletion = NO;
    theGroup.delegate = self;
    
    [self.layer addAnimation:theGroup forKey:@"frameChange"];
    
    double delayInSeconds = delay;
        
    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
    dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
        //apply frame to view
        self.frame = (CGRect){self.frame.origin, [boundsAnimation.toValue CGRectValue].size};
        self.center = [positionAnimation.toValue CGPointValue];
    });
}

- (void)animatedChangeHeight:(CGFloat)height
                    duration:(CGFloat)duration
                       delay:(CGFloat)delay
{
    [self changeSizeToSize:(CGSize){self.bounds.size.width, height}
                  duration:duration
                     delay:delay];
}

- (void)scaleRoundUpWithDuration:(CGFloat)duration delay:(CGFloat)delay
{
    [self scaleRoundUpWithDuration:duration scale:1.3 delay:delay repeat:NO];
}

- (void)scaleRoundUpWithDuration:(CGFloat)duration
                           scale:(CGFloat)scale
                           delay:(CGFloat)delay
                          repeat:(BOOL)shouldRepeat
{
    CABasicAnimation *anim = [CABasicAnimation animationWithKeyPath:@"transform"];
    anim.timingFunction = [CAMediaTimingFunction functionWithName:kCAMediaTimingFunctionEaseInEaseOut];
    anim.duration = duration;
    anim.beginTime = CACurrentMediaTime() + delay;
    anim.repeatCount = shouldRepeat ? FLT_MAX : 1;
    anim.autoreverses = YES;
    anim.removedOnCompletion = YES;
    
    CATransform3D scaleTrans3D = CATransform3DMakeScale(scale, scale, 1.0);
    CGAffineTransform scaleTrans2D = CATransform3DGetAffineTransform(scaleTrans3D);
    CGAffineTransform transform2D = CGAffineTransformConcat(self.transform, scaleTrans2D);
    CATransform3D transform3D = CATransform3DMakeAffineTransform(transform2D);
    anim.toValue = [NSValue valueWithCATransform3D:transform3D];
//    anim.delegate = self;
    
    [self.layer addAnimation:anim forKey:@"transform"];
}

- (void)stopScaleRoundUp
{
    [self.layer removeAnimationForKey:@"transform"];
}

- (void)startHelpGrowAnimationWithOffset:(CGPoint)offset
{
    CAKeyframeAnimation *positionAnimation = [CAKeyframeAnimation animationWithKeyPath:@"position"];

    //full size
    NSValue *fromValue = [NSValue valueWithCGPoint:self.center];
    NSValue *toValue = [NSValue valueWithCGPoint:CGPointMake(self.center.x - offset.x, self.center.y - offset.y)];

    [positionAnimation setValues:@[toValue, fromValue, fromValue]];
    [positionAnimation setKeyTimes:@[@.0, @.33, @.77]]; // 0.33 = 0.5 / 2; 0.77 = 1.5 / 2;

    CAKeyframeAnimation *anim = [CAKeyframeAnimation animationWithKeyPath:@"transform"];
    
    CATransform3D scaleTrans3D = CATransform3DMakeScale (0.01, 0.01, 1.0);
    CGAffineTransform scaleTrans2D = CATransform3DGetAffineTransform(scaleTrans3D);
    CGAffineTransform transform2D = CGAffineTransformConcat(self.transform, scaleTrans2D);
    CATransform3D transform3D = CATransform3DMakeAffineTransform(transform2D);
    
    fromValue = [NSValue valueWithCATransform3D:self.layer.transform];
    toValue = [NSValue valueWithCATransform3D:transform3D];
    
    [anim setValues:@[toValue, fromValue, fromValue]];
    [anim setKeyTimes:@[@.0, @.33, @.77]]; // 0.33 = 0.5 / 2; 0.77 = 1.5 / 2;
    anim.duration = 2;
    anim.repeatCount = FLT_MAX;
    CAAnimationGroup *theGroup = [CAAnimationGroup animation];
    
    theGroup.animations = @[anim, positionAnimation];
    theGroup.timingFunction = [CAMediaTimingFunction functionWithName:kCAMediaTimingFunctionEaseInEaseOut];
    theGroup.repeatCount = FLT_MAX;
    theGroup.duration = 2;
    theGroup.removedOnCompletion = YES;

    [self.layer addAnimation:theGroup forKey:nil];
}

@end
