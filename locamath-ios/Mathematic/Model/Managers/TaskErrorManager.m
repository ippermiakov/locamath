//
//  ErrorManager.m
//  Mathematic
//
//  Created by Developer on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TaskErrorManager.h"
#import "Action.h"
#import "Task.h"
#import "Level.h"
#import "DataUtils.h"

@implementation TaskErrorManager

+ (NSString *)errorDescriptionOnAnswerForActions:(NSArray *)actions withTask:(Task *)task
{
    NSMutableDictionary *info;
    
    // Find empty answer.
    info = [self errorInfoOnEmptyAnswerWithActions:actions];
    if ([[info valueForKey:kTaskErrorInfoStatus] isEqualToString:@"Error"]) return [info valueForKey:kTaskErrorInfoDescription];
    
    // Find if last subaction answer is equal to answer in its action.
    info = [self errorInfoOnAnswerEqualToLastSubActionAnswerWithActions:actions];
    if ([[info valueForKey:kTaskErrorInfoStatus] isEqualToString:@"Error Answer"]) return @"Error Answer"; //[info valueForKey:kTaskErrorInfoDescription];
    
    return @"No Error";
}

+ (NSMutableDictionary *)errorInfoOnEmptyAnswerWithActions:(NSArray *)actions
{
    BOOL isHaveNotEmptyAction = [actions any:^BOOL(Action *obj) {
        return  obj.task.status == kTaskStatusSolved
                || obj.task.status == kTaskStatusSolvedNotAll
                || obj.answer.length > 0;
    }];
    
    //reset all erros if needed
    if (!isHaveNotEmptyAction) {
        [actions each:^(Action *obj) {
            obj.error = kActionErrorTypeNone;
        }];
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }
    
    for (Action *action in actions) {
        Level *taskLevel = action.task.level;
        if ([action.answer isEqualToString:@""] && ![taskLevel.isTest boolValue] && !isHaveNotEmptyAction) {
            return [self errorInfoWithStatus:@"Error" description:
                    NSLocalizedString(@"Answer field is empty", @"")];
        }
    }
    
    return [self errorInfoWithStatus:@"No Error" description:@"All actions have an answer"];
}

+ (NSMutableDictionary *)errorInfoOnIdenticalAnswersWithActions:(NSArray *)actions
{
    /* Check this at first. If answers are different show message about it. */
    for (NSInteger i = 0; i < [actions count]; i++) {
        if (i > 0) {
            NSString *currentAnswer     = [[actions objectAtIndex:i]     answer];
            NSString *previousAnswer    = [[actions objectAtIndex:i - 1] answer];
            if ([currentAnswer isEqualToString:previousAnswer] == NO) {
                return [self errorInfoWithStatus:@"Error" description:
                        NSLocalizedString(@"Answers are not identical to each other.", @"")];
            }
        }
    }
    /* If all answers are the same. */
    return [self errorInfoWithStatus:@"No Error" description:@"Answers are identical to each other."];
}

+ (NSMutableDictionary *)errorInfoOnTaskSolvingWithActions:(NSArray *)actions withTask:(Task *)task
{
    NSInteger expressionsCount = [task.expressions count];
    
    if ([task.solutions isEqualToString:kBothSolutionsType]) {
        expressionsCount = expressionsCount * 2;
    }
    
    NSUInteger countOfSolvedActions = [[DataUtils countOfSolvedActionsFromActions:actions] integerValue];
    
    if (countOfSolvedActions == expressionsCount) {
        return [self errorInfoWithStatus:@"No Error" description:NSLocalizedString(@"Task is solved with all possible solutions and expressions", nil)];
    } else if (countOfSolvedActions){
        return [self errorInfoWithStatus:@"No Error not all solv" description:NSLocalizedString(@"Task is not solved with all possible variants.", nil)];
    } else {
         return [self errorInfoWithStatus:@"Error" description:NSLocalizedString(@"Task is not solved with all possible variants.", nil)];
    }
}

+ (NSMutableDictionary *)errorInfoOnAnswerEqualToLastSubActionAnswerWithActions:(NSArray *)actions
{
    for (Action *action in actions) {
        Action *lastSubAction = [action.subActions lastObject];
        
        Level *taskLevel = action.task.level;

        if (![lastSubAction.answer isEqualToString:action.answer] && ![taskLevel.isTest boolValue]) {
            return [self errorInfoWithStatus:@"Error Answer" description:@""];
        }
    }
    
    return nil;
}

#pragma mark - Helper

+ (NSMutableDictionary *)errorInfoWithStatus:(NSString *)status description:(NSString *)description
{
    NSMutableDictionary *info = [[NSMutableDictionary alloc] init];
    [info setValue:status forKey:kTaskErrorInfoStatus];
    [info setValue:description forKey:kTaskErrorInfoDescription];
    
    return info;
}

@end
