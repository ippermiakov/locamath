//
//  Level.h
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractLevel.h"

@class Task, Child, Game, LevelsPath;

@interface Level : NSManagedObject<AbstractLevel>

@property (nonatomic, retain) NSNumber * levelScore;
@property (nonatomic, retain) NSNumber * countStartedTasks;
@property (nonatomic, retain) NSNumber * currentScore;
@property (nonatomic, retain) NSNumber * countSolvedTasks;
@property (nonatomic, retain) NSNumber * pointX;
@property (nonatomic, retain) NSNumber * pointY;
@property (nonatomic, retain) NSNumber * isTest;
@property (nonatomic, retain) NSNumber * isSelected;
@property (nonatomic, retain) Game *game;
@property (nonatomic, retain) LevelsPath *path;

@end

@interface Level (CoreDataGeneratedAccessors)

- (void)addTasksObject:(Task *)value;
- (void)removeTasksObject:(Task *)value;
- (void)addTasks:(NSSet *)values;
- (void)removeTasks:(NSSet *)values;

@end
