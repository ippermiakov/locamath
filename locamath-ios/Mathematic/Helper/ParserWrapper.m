//
//  ParserWrapper.m
//  Mathematic
//
//  Created by alexbutenko on 11/19/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ParserWrapper.h"
#import "Parser.h"
#import "Task.h"
#import "TaskError.h"
#import "NSManagedObject+Clone.h"
#include <mach/mach_time.h>

@interface ParserWrapper()<ParserDelegate>

@end

@implementation ParserWrapper

- (void)parseWithActions:(NSArray *)actions withEtalons:(NSArray *)expressions
{
    Parser *parser = [Parser new];
    parser.delegate = self;
    
    [parser parseWithActions:actions withEtalons:expressions];
}

#pragma mark - ParserDelegate methods

- (void)didFailedParsingAction:(Action *)action
{
    __block BOOL needToRemove = NO;
    //create error for statistic
    TaskError *taskError = [TaskError createEntity];
    taskError.lastChangeDate = action.task.lastChangeDate;
    //set actions
    Action *clonedAction = (Action *)[action cloneInContext:[NSManagedObjectContext contextForCurrentThread] exludeEntities:@[@"Task"]];
    
    clonedAction.taskError = taskError;
    taskError.errorType = action.errorNumber;
    
    if (action.task.taskErrors.count == 0) {
        taskError.task = action.task;
    } else {
        needToRemove = [action.task.taskErrors any:^BOOL(TaskError *existingTaskError) {
            if ([existingTaskError isTaskErrorEqualToTaskError:taskError]) {
                existingTaskError.lastChangeDate = taskError.lastChangeDate;
            }
            
            return [existingTaskError isTaskErrorEqualToTaskError:taskError];
        }];
    }
    
    if (needToRemove) {
        [taskError deleteEntity];
    } else {
        if (nil == taskError.task) {
            taskError.task = action.task;
        }
    }
    
    clonedAction.identifier = [NSString stringWithFormat:@"%llu", mach_absolute_time()];
    
    [clonedAction.subActions each:^(Action *action) {
        action.identifier = [NSString stringWithFormat:@"%llu", mach_absolute_time()];
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

@end
