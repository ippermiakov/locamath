//
//  UIViewController+AnimatedTouches.h
//  Mathematic
//
//  Created by alexbutenko on 8/16/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@class DWFParticleView;

@interface UIViewController (AnimatedTouches)

@property (strong, nonatomic) DWFParticleView *particleViewForTouches;

- (void)addParticleViewForTouchesIfNeeded;

@end
