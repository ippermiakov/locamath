//
//  SolvingAndExercisesViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 11.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Task.h"
#import "TaskError.h"
#import "AbstractAchievementViewController.h"

@interface SolvingAndExercisesViewController : BaseViewController<AbstractAchievementViewController>

@property (strong, nonatomic) Task *task;
@property (strong, nonatomic) TaskError *taskError;

- (id)initWithAchievement:(id)achievemen;

@end
