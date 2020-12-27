//
//  Objective.h
//  Mathematic
//
//  Created by Alexander on 10/30/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Task.h"

@interface ObjectiveView : UIScrollView

@property (strong, nonatomic) Task *task;

- (id)initWithTask:(id<AbstractTask>)task;
- (id)initWithTask:(id<AbstractTask>)task andColor:(UIColor *)textColor;
- (id)initWithTask:(id<AbstractTask>)task frame:(CGRect)rect;

@end
