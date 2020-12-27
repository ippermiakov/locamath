//
//  UIViewController+AnimatedTouches.m
//  Mathematic
//
//  Created by alexbutenko on 8/16/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UIViewController+AnimatedTouches.h"
#import "DWFParticleView.h"
#import <objc/runtime.h>

static char * const kParticleViewKey = "kParticleView";

@implementation UIViewController (AnimatedTouches)

- (void)addParticleViewForTouchesIfNeeded
{
    if (!self.particleViewForTouches) { 
        self.particleViewForTouches = [[DWFParticleView alloc] initWithFrame:self.view.frame
                                                                       image:[UIImage imageNamed:@"Particles_fire1.png"]
                                                             emitterPosition:CGPointZero
                                                                 emitterSize:(CGSize){10, 10}
                                                                   birthRate:0
                                                           emittingBirthRate:70
                                                               emissionRange:2*M_PI];
        [self.view addSubview:self.particleViewForTouches];
    }
}

#pragma mark - Setters & Getters

- (DWFParticleView *)particleViewForTouches
{
    return objc_getAssociatedObject(self, kParticleViewKey);
}

- (void)setParticleViewForTouches:(DWFParticleView *)particleViewForTouches
{
    objc_setAssociatedObject(self, kParticleViewKey, particleViewForTouches, OBJC_ASSOCIATION_RETAIN);
}

#pragma mark - Touches handling

- (void)touchesMoved:(NSSet *)touches withEvent:(UIEvent *)event
{
    [self.particleViewForTouches setEmitterPositionFromTouch:[touches anyObject]];
}

- (void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event
{
    [self.particleViewForTouches setEmitterPositionFromTouch:[touches anyObject]];
    [self.particleViewForTouches setIsEmitting:YES];
}

- (void)touchesEnded:(NSSet *)touches withEvent:(UIEvent *)event
{
    [self.particleViewForTouches setIsEmitting:NO];
}

- (void)touchesCancelled:(NSSet *)touches withEvent:(UIEvent *)event
{
    [self.particleViewForTouches setIsEmitting:NO];
}

@end
