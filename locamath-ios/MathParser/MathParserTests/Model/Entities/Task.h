//
//  Task.h
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractTask.h"

@class Action;
@class Level, Child, TaskError;

@interface Task : NSManagedObject<AbstractTask>

@property (nonatomic, retain) NSString * identifier;
@property (nonatomic, retain) NSString * score;
@property (nonatomic, retain) NSString * animation;
@property (nonatomic, retain) NSString * hint;
@property (nonatomic, retain) NSString * solutions;
@property (nonatomic, retain) NSString * formula;
@property (nonatomic, retain) id expressions;
@property (nonatomic, retain) NSString * answer;
@property (nonatomic, retain) NSNumber * literal;
@property (nonatomic, retain) NSArray  * letters;
@property (nonatomic, retain) NSNumber *countSolvedActions;
@property (nonatomic, retain) NSNumber *taskType;
@property (nonatomic, readonly) NSArray *actionsWithError;
//TODO: move to abstract if needed to count time for olympiads
@property (nonatomic, retain) NSNumber *secondsPerTask;
@property (nonatomic, retain) NSNumber *isAnimationSelected;
@property (nonatomic, retain) NSNumber *isHelpSelected;
@property (nonatomic, retain) NSNumber *isSchemeSelected;
@property (nonatomic, retain) NSNumber *isPencilSelected;
@property (nonatomic, readonly) NSString *schemeImageName;

@property (nonatomic, retain) NSSet *taskErrors;

@end

@interface Task (CoreDataGeneratedAccessors)

- (void)addActionsObject:(Action *)value;
- (void)removeActionsObject:(Action *)value;
- (void)addActions:(NSSet *)values;
- (void)removeActions:(NSSet *)values;

- (void)addTeskErrorsObject:(TaskError *)value;
- (void)removeTaskErrorsObject:(TaskError *)value;
- (void)addTaskErrors:(NSSet *)values;
- (void)removeTaskErrors:(NSSet *)values;

@end
