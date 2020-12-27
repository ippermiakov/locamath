//
//  AbstractLevel.h
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AbstractAchievement.h"

@protocol AbstractLevel <AbstractAchievement>

@property (nonatomic, retain) NSString * identifier;
@property (nonatomic, retain) NSString * image;
@property (nonatomic, retain) NSString * name;
@property (nonatomic, retain) NSNumber * isAllTasksSolved;
@property (nonatomic, retain) Child *child;
@property (nonatomic, retain) NSSet *tasks;

- (NSArray *)sortedArrayOfTasks;

@end