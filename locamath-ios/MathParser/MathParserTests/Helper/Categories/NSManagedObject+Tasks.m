//
//  NSManagedObject+Tasks.m
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSManagedObject+Tasks.h"
#import "AbstractTask.h"
#import "AbstractLevel.h"
#import "DataUtils.h"

@implementation NSManagedObject (Tasks)

- (void)setStatus:(TaskStatus)status
{
    [self willChangeValueForKey:@"statusNumber"];
    [self setPrimitiveValue:@(status) forKey:@"statusNumber"];
    [self didChangeValueForKey:@"statusNumber"];
    
    if (status == kTaskStatusSolved || status == kTaskStatusSolvedNotAll) {
        id<AbstractTask> task = (id<AbstractTask>)self;
        
        task.level.isAllTasksSolved = @([DataUtils isAllTasksSolvedForLevelId:task.level.identifier]);
//        NSLog(@"solved task: %@ level solved: %@", [task statisticDescription], task.level.isAllTasksSolved);
    }
}

- (void)setLastChangeDate:(NSDate *)lastChangeDate
{
    [self willChangeValueForKey:@"lastChangeDate"];
    [self setPrimitiveValue:lastChangeDate forKey:@"lastChangeDate"];
    [self didChangeValueForKey:@"lastChangeDate"];
    
    id<AbstractTask> task = (id<AbstractTask>)self;
    
    if ([task respondsToSelector:@selector(level)]) {
        task.level.lastChangeDate = lastChangeDate;
    }
}

- (TaskStatus)status
{
    [self willAccessValueForKey:@"statusNumber"];
    TaskStatus result = [[self primitiveValueForKey:@"statusNumber"] integerValue];
    [self didAccessValueForKey:@"statusNumber"];
    return result;
}

- (NSString *)stringTaskStatus:(TaskStatus)status
{
    switch (status) {
        case kTaskStatusError:
            return NSLocalizedString(@"Error", @"Profile achievements event status");
            break;
        case kTaskStatusStarted:
            return NSLocalizedString(@"Started task", @"Profile achievements event status");
        case kTaskStatusSolved:
            return NSLocalizedString(@"Solved task", @"Profile achievements event status");
        case kTaskStatusSolvedNotAll:
            return NSLocalizedString(@"Solved task", @"Profile achievements event status");
        default:
            return @"No status";
            break;
    }
}

- (NSString *)taskStatisticDescription
{
    id<AbstractTask> task = (id<AbstractTask>)self;
    
    NSInteger day = 0;
    NSInteger month = 0;
    
    if (task.lastChangeDate) {
        NSDateComponents *components = [[NSCalendar currentCalendar] components:NSDayCalendarUnit | NSMonthCalendarUnit | NSYearCalendarUnit fromDate:task.lastChangeDate];
        
        day = [components day];
        month = [components month];
    }
    
    NSString *description = [NSString stringWithFormat:@"%02i.%02i %@ - %@ (%@)", day, month, task.level.name, task.numberTask, [self stringTaskStatus:self.status]];
    
    return description;
}

@end
