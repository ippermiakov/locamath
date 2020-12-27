//
//  AbstractTask.h
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AbstractLevel.h"
#import "AbstractAchievement.h"
#import "NSManagedObject+Tasks.h"

@class Child;

@protocol AbstractTask <AbstractAchievement>

@property (nonatomic, retain) NSString * objective;
@property (nonatomic, retain) NSNumber *numberTask;
@property (nonatomic, retain) NSNumber * statusNumber;
@property (nonatomic, retain) NSNumber *currentScore;

@property (nonatomic, retain) id<AbstractLevel> level;
@property (nonatomic, retain) Child *child;
@property (nonatomic, retain) NSSet *actions;
@property (unsafe_unretained, nonatomic) TaskStatus status;

- (NSString *)errorTaskDescription;

@end
