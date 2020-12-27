//
//  NSManagedObject+Tasks.h
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <CoreData/CoreData.h>

@interface NSManagedObject (Tasks)

@property (unsafe_unretained, nonatomic) TaskStatus status;

- (NSString *)stringTaskStatus:(TaskStatus)status;
- (NSString *)taskStatisticDescription;

@end
