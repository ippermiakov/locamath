//
//  TaskError.m
//  Mathematic
//
//  Created by SanyaIOS on 30.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TaskError.h"
#import "Action.h"
#import "Task.h"
#import "AbstractTask.h"
#import "SolvingViewController.h"


@implementation TaskError

@dynamic identifier;
@dynamic lastChangeDate;
@dynamic errorType;
@dynamic task;
@dynamic actions;

- (NSString *)statisticDescription
{
    return [self taskStatisticDescription];
}

- (NSString *)taskStatisticDescription
{
    id<AbstractTask>task = (id<AbstractTask>)self.task;
    
    
    NSDateComponents *components = [[NSCalendar currentCalendar] components:NSDayCalendarUnit | NSMonthCalendarUnit | NSYearCalendarUnit fromDate:self.lastChangeDate];
    
    NSInteger day = [components day];
    NSInteger month = [components month];
    
    
    NSString *description = [NSString stringWithFormat:@"%02i.%02i %@ - %@ (%@)", day, month, task.level.name, task.numberTask, NSLocalizedString(@"error", nil)];
    
    return description;
}

- (NSString *)taskStatisticFixOrErrorDescription
{
    id<AbstractTask>task = (id<AbstractTask>)self.task;
    
    NSDateFormatter *df = [[NSDateFormatter alloc] init];
    [df setDateStyle:NSDateFormatterLongStyle];
    [df setTimeStyle:NSDateFormatterNoStyle];
    NSString *dateString = [df stringFromDate:self.lastChangeDate];
    
    NSString *description = [NSString stringWithFormat:@"%@; %@; №%@ (%@)", dateString, task.level.name, task.numberTask, NSLocalizedString(@"Error", nil)];
    
    return description;
}


- (Class)controllerClass
{
    return [SolvingViewController class];
}

#pragma mark - Helper

- (BOOL)isTaskErrorEqualToTaskError:(TaskError *)taskError
{
    __block BOOL isEqual = YES;
    
    if (self.actions.count != taskError.actions.count) {
        return NO;
    }
    
    NSArray *sortedActionObj1 = [[self.actions allObjects] sortedArrayUsingComparator:^NSComparisonResult(Action *obj1, Action *obj2) {
        return [obj1.identifier compare:obj2.identifier];
    }];
    
    NSArray *sortedActionObj2 = [[taskError.actions allObjects] sortedArrayUsingComparator:^NSComparisonResult(Action *obj1, Action *obj2) {
        return [obj1.identifier compare:obj2.identifier];
    }];
    
    [sortedActionObj1 enumerateObjectsUsingBlock:^(Action *obj1, NSUInteger idx, BOOL *stop1) {
        
        if ([sortedActionObj2 count] <= idx) {
            isEqual = NO;
            *stop1 = YES;
            return;
        }
        
        Action *obj2 = [sortedActionObj2 objectAtIndex:idx];
        
        if (obj1.subActions.count != obj2.subActions.count) {
            isEqual = NO;
            *stop1 = YES;
            return;
        }
        
        NSArray *sortedSubActionObj1 =[obj1.subActions sortedArrayUsingComparator:^NSComparisonResult(Action *obj1, Action *obj2) {
            return [obj1.identifier compare:obj2.identifier];
        }];
        
        NSArray *sortedSubActionObj2 =[obj2.subActions sortedArrayUsingComparator:^NSComparisonResult(Action *obj1, Action *obj2) {
            return [obj1.identifier compare:obj2.identifier];
        }];
        
        [sortedSubActionObj1 enumerateObjectsUsingBlock:^(Action *subActionObj1, NSUInteger idx, BOOL *stop2) {
            Action *subActionObj2 = [sortedSubActionObj2 objectAtIndex:idx];
            if (![subActionObj1.string isEqualToString:subActionObj2.string]) {
                isEqual = NO;
                *stop2 = YES;
                return;
            }
        }];
        
        if (isEqual == NO) {
            *stop1 = YES;
        }
    }];
    
    return isEqual;
}

@end
