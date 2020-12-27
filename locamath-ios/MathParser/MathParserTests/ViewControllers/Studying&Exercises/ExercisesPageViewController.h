//
//  ExercisesPage.h
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"

@class Level;
@class Task;

@interface ExercisesPageViewController : BaseViewController

@property (strong, nonatomic)        Level *level;
@property (unsafe_unretained, nonatomic) NSInteger levelNumber;
@property (strong, nonatomic)        NSString *numberTask;
@property (strong, nonatomic)        Task *task;
@property (strong, nonatomic)        UIImage *image;

@end
