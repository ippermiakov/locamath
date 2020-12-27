//
//  TaskError.h
//  Mathematic
//
//  Created by SanyaIOS on 30.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractAchievement.h"

@class Action, Task;

@interface TaskError : NSManagedObject <AbstractAchievement>

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSDate * lastChangeDate;
@property (nonatomic, retain) NSNumber * errorType;

@property (nonatomic, retain) Task *task;

@property (nonatomic, retain) NSSet *actions;

- (BOOL)isTaskErrorEqualToTaskError:(TaskError *)teskError;

@end

@interface TaskError (CoreDataGeneratedAccessors)

- (void)addActionsObject:(Action *)value;
- (void)removeActionsObject:(Action *)value;
- (void)addActions:(NSSet *)values;
- (void)removeActions:(NSSet *)values;

@end
