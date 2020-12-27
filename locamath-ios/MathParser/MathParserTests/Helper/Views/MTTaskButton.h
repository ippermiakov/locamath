//
//  MTTaskButton.h
//  Mathematic
//
//  Created by Developer on 04.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@class Task;

@interface MTTaskButton : UIButton

@property (unsafe_unretained, nonatomic) NSInteger numberButton;
@property (unsafe_unretained, nonatomic) TaskButtonType type;
@property (strong, nonatomic) Task *task;

@end
