//
//  OlympiadLevel.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 27.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractLevel.h"

@class OlympiadTask, Child;

@interface OlympiadLevel : NSManagedObject<AbstractLevel>

@property (readonly, nonatomic) NSUInteger index;
@property (readonly, nonatomic) NSUInteger levelNumber;

@end

@interface OlympiadLevel (CoreDataGeneratedAccessors)

- (void)addTasksObject:(OlympiadTask *)value;
- (void)removeTasksObject:(OlympiadTask *)value;
- (void)addTasks:(NSSet *)values;
- (void)removeTasks:(NSSet *)values;

@end
