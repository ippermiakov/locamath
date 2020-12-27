//
//  Action.h
//  Mathematic
//
//  Created by alexbutenko on 4/1/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "ParselableActionProtocol.h"

@class Action, Task, TaskError;

@interface Action : NSManagedObject<ParselableActionProtocol>

@property (nonatomic, retain) NSString * answer;
@property (nonatomic, retain) NSNumber * errorNumber;
@property (nonatomic, retain) NSString * identifier;
@property (nonatomic, retain) NSNumber * typeNumber;
@property (nonatomic, retain) NSNumber * isCorrect;
@property (nonatomic, retain) Task *task;
@property (nonatomic, retain) Action *parentAction;
@property (unsafe_unretained, nonatomic) ActionType type;
@property (nonatomic, retain) TaskError *taskError;

- (Action *)addSubActionWithString:(NSString *)string;
+ (id)actionWithString:(NSString *)string;
- (void)updateWithString:(NSString *)string;

- (BOOL)importSubActions:(id)data;
- (BOOL)isActionEqualToAction:(Action *)action;

@end

@interface Action (CoreDataGeneratedAccessors)

- (void)insertObject:(Action *)value inSubActionsAtIndex:(NSUInteger)idx;
- (void)removeObjectFromSubActionsAtIndex:(NSUInteger)idx;
- (void)insertSubActions:(NSArray *)value atIndexes:(NSIndexSet *)indexes;
- (void)removeSubActionsAtIndexes:(NSIndexSet *)indexes;
- (void)replaceObjectInSubActionsAtIndex:(NSUInteger)idx withObject:(Action *)value;
- (void)replaceSubActionsAtIndexes:(NSIndexSet *)indexes withSubActions:(NSArray *)values;
- (void)addSubActionsObject:(Action *)value;
- (void)removeSubActionsObject:(Action *)value;
- (void)addSubActions:(NSOrderedSet *)values;
- (void)removeSubActions:(NSOrderedSet *)values;

@end
