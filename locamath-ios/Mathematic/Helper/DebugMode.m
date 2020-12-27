//
//  DebugMode.m
//  Mathematic
//
//  Created by SanyaIOS on 26.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DebugMode.h"
#import "Task.h"
#import "OlympiadTask.h"
#import "AbstractTask.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"
#import "Action.h"
#import "Level.h"
#import "OlympiadLevel.h"
#import "LevelsPath.h"
#import "DataUtils.h"
#import "ChildManager.h"
#import "MTHTTPClient.h"
#include <mach/mach_time.h>
#import "SynchronizationManager.h"
#import "ExpressionParser.h"
#import "Node.h"

@implementation DebugMode

+ (BOOL)isCreatingChildsNeeded
{
    BOOL isNeeded = (0 == [[Child numberOfEntities] integerValue]);
    
    if (!isNeeded && [[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [Child deleteAllMatchingPredicate:[NSPredicate predicateWithFormat:@"name beginswith 'Debug'"]];
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];        
    } else if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        isNeeded = NO;
    }
    
    return isNeeded;
}

+ (void)createChildsIfNeeded
{
    if ([self isCreatingChildsNeeded]) {
        Child *newChild = [Child createEntity];
        newChild.name = @"Debug";
        
        Child *newChild2 = [Child createEntity];
        newChild2.name = @"Debug2";
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }
}

+ (void)solveTaskOlympiad:(OlympiadTask *)olympiadTask
       withUpdateToServer:(BOOL)isUpdate
                   finish:(DebugModeFinishBlock)finishBlock
                  failure:(DebugModeFailureBlock)failureBlock
{
    [DebugMode solveTaskOlympiad:olympiadTask
              withUpdateToServer:isUpdate
                        progress:nil
                          finish:finishBlock
                         failure:failureBlock];
}

+ (void)solveTaskOlympiad:(OlympiadTask *)olympiadTask
       withUpdateToServer:(BOOL)isUpdate
                 progress:(DebugModeProgressBlock)progressBlock
                   finish:(DebugModeFinishBlock)finishBlock
                  failure:(DebugModeFailureBlock)failureBlock
{
    NSArray * sortedActions = [[olympiadTask.actions allObjects]sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];

    [sortedActions enumerateObjectsUsingBlock:^(OlympiadAction *action, NSUInteger idx, BOOL *stop) {
    
        NSArray *sortedHints = [[action.hints allObjects] sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
            return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
        }];

        NSArray *hintsToInput = [sortedHints select:^BOOL(OlympiadHint *hint) {
            return [hint.hasUserInput isEqualToNumber:@YES];
        }];
        
        NSArray *solution = [olympiadTask.solutionHint allObjects][idx];
                
        [hintsToInput enumerateObjectsUsingBlock:^(OlympiadHint *hint, NSUInteger idx, BOOL *stop) {
            hint.userInput = solution[idx];
        }];
    }];
    
    olympiadTask.lastChangeDate = [NSDate date];
    olympiadTask.tryCounter = @([[olympiadTask tryCounter] integerValue] + 1);
    olympiadTask.status = kTaskStatusSolved;
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    if (isUpdate) {
        [DebugMode sendUpdateToServerOlympiadWithProgressBlock:progressBlock
                                                   finishBlock:finishBlock
                                                  failureBlock:failureBlock];
    } else {
        if (finishBlock) finishBlock();
    }
}

+ (void)solveTask:(Task *)task
         progress:(DebugModeProgressBlock)progressBlock
           finish:(DebugModeFinishBlock)finishBlock
          failure:(DebugModeFailureBlock)failureBlock
{
    [DebugMode solveTask:task
      withUpdateToServer:YES
                progress:progressBlock
                  finish:finishBlock
                 failure:failureBlock];
}

+ (void)solveTask:(Task *)task
withUpdateToServer:(BOOL)isUpdate
           finish:(DebugModeFinishBlock)finishBlock
          failure:(DebugModeFailureBlock)failureBlock
{
    [DebugMode solveTask:task
      withUpdateToServer:isUpdate
                progress:nil
                  finish:finishBlock
                 failure:failureBlock];
}

+ (void)solveTask:(Task *)task
withUpdateToServer:(BOOL)isUpdate
         progress:(DebugModeProgressBlock)progressBlock
           finish:(DebugModeFinishBlock)finishBlock
          failure:(DebugModeFailureBlock)failureBlock
{
    if (task.status != kTaskStatusSolved) {
        [Action deleteAllMatchingPredicate:[NSPredicate predicateWithFormat:@"task == %@", task]];
        
        Action *action = [Action createEntity];
        
        action.identifier =  [NSString stringWithFormat:@"%llu", mach_absolute_time()]; //timestamp
        action.task = task;
        action.type = kActionTypeExpression;
        action.answer = task.answer.length > 0 ? task.answer : [NSString stringWithFormat:@"%@",task.expressions[0]];
        action.isCorrect = @YES;
        
        NSString *subActionSolution = task.answer.length > 0 ?
                                    [NSString stringWithFormat:@"%@=%@",task.expressions[0], task.answer] :
                                    [NSString stringWithFormat:@"%@",task.expressions[0]];
        
        ExpressionParser *expParser = [ExpressionParser new];
        Node *etalon = [expParser parse:task.expressions[0]];
        action.etalon = @(etalon.hash);
        
        Action *subAction = [action addSubActionWithString:subActionSolution];
        subAction.isCorrect = @YES;
        
        task.lastChangeDate = [NSDate date];
        task.status = kTaskStatusSolved;
        task.currentScore = [DataUtils scoreForTask:task withActions:[NSArray arrayWithObject:action]];
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        
        if (isUpdate) {
            [DebugMode sendUpdateToServerWithProgressBlock:progressBlock
                                               finishBlock:finishBlock
                                              failureBlock:failureBlock];
        } else {
            if (finishBlock) finishBlock();
        }
    } else {
        if (finishBlock) finishBlock();
    }
}

+ (void)solveTrainingTasksForLevel:(Level *)level
                          progress:(DebugModeProgressBlock)progressBlock
                            finish:(DebugModeFinishBlock)finishBlock
                           failure:(DebugModeFailureBlock)failureBlock
{
    NSArray *trainingTasks = [DataUtils tasksOfType:kTaskTypeTraining forLevel:level];
    
    [trainingTasks each:^(Task *task) {
        [DebugMode solveTask:task withUpdateToServer:NO finish:nil failure:nil];
    }];
    
    [DebugMode sendUpdateToServerWithProgressBlock:progressBlock
                                       finishBlock:finishBlock
                                      failureBlock:failureBlock];
}

+ (void)solveLevel:(Level *)level
          progress:(DebugModeProgressBlock)progressBlock
            finish:(DebugModeFinishBlock)finishBlock
           failure:(DebugModeFailureBlock)failureBlock
{
    [DebugMode solveLevel:level
       withUpdateToServer:YES
                 progress:progressBlock
                   finish:finishBlock
                  failure:failureBlock];
}

+ (void)solveLevel:(Level *)level
withUpdateToServer:(BOOL)isUpdate
            finish:(DebugModeFinishBlock)finishBlock
           failure:(DebugModeFailureBlock)failureBlock
{
    [DebugMode solveLevel:level
       withUpdateToServer:isUpdate
                 progress:nil
                   finish:finishBlock
                  failure:failureBlock];
}

+ (void)solveLevel:(Level *)level
withUpdateToServer:(BOOL)isUpdate
          progress:(DebugModeProgressBlock)progressBlock
            finish:(DebugModeFinishBlock)finishBlock
           failure:(DebugModeFailureBlock)failureBlock
{
    [level.tasks each:^(Task *task) {
        [DebugMode solveTask:task withUpdateToServer:NO finish:nil failure:nil];
    }];
    
    if (isUpdate) {
        [DebugMode sendUpdateToServerWithProgressBlock:progressBlock
                                           finishBlock:finishBlock
                                          failureBlock:failureBlock];
    } else {
        if (finishBlock) finishBlock();
    }
}

+ (void)solveOlympiadLevel:(OlympiadLevel *)olimpiadLevel
        withUpdateToServer:(BOOL)isUpdate
                  progress:(DebugModeProgressBlock)progressBlock
                    finish:(DebugModeFinishBlock)finishBlock
                   failure:(DebugModeFailureBlock)failureBlock
{
    [olimpiadLevel.tasks each:^(OlympiadTask *task) {
        [DebugMode solveTaskOlympiad:task withUpdateToServer:NO finish:finishBlock failure:nil];
    }];
    
    if (isUpdate) {
        [DebugMode sendUpdateToServerOlympiadWithProgressBlock:progressBlock
                                                   finishBlock:finishBlock
                                                  failureBlock:failureBlock];
    } else {
        if (finishBlock) finishBlock();
    }
}

+ (void)solveLevelsPathWithColor:(NSString *)pathColor levelNumber:(NSNumber *)levelNumber
{
    LevelsPath *levelPath = [DataUtils pathWithColorName:pathColor levelNumber:levelNumber];
        
    [levelPath.levels each:^(Level *level) {
        [DebugMode solveLevel:level withUpdateToServer:NO finish:nil failure:nil];
    }];
}

+ (void)solveLevelsPathWithColor:(NSString *)pathColor
                     levelNumber:(NSNumber *)levelNumber
                        progress:(DebugModeProgressBlock)progressBlock
                          finish:(DebugModeFinishBlock)finishBlock
                         failure:(DebugModeFailureBlock)failureBlock
{    
    dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, NULL), ^{
        [DebugMode solveLevelsPathWithColor:pathColor levelNumber:levelNumber];
        
        [DebugMode sendUpdateToServerWithProgressBlock:progressBlock finishBlock:^{
            if (finishBlock) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    finishBlock();
                });
            }
        } failureBlock:^(NSError *error) {
            if (failureBlock) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    failureBlock(error);
                });
            }
        }];
    });
}

+ (void)autoLoginWithSuccess:(DebugModeFinishBlock)successBlock
                     failure:(DebugModeFailureBlock)failureBlock
{    
    NSString * const kMail = @"alexandr.butenko@gmail.com";
    NSString * const kPassword = @"111111";
    
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [[MTHTTPClient sharedMTHTTPClient] loginUserWithEmail:kMail
                                                     password:kPassword
                                        shouldSaveAccessToken:YES
                                                      success:^(NSDictionary *successResponseData) {
                                                         if (successBlock) {
                                                             successBlock();
                                                         }
                                                      }
                                                      failure:^(BOOL finished, NSError *error) {
                                                          if (failureBlock) {
                                                              failureBlock(error);
                                                          }
                                                      }];
    } else if (successBlock) {
            successBlock();
    }
}

#pragma mark - Helper

+ (void)sendUpdateToServerWithFinishBlock:(DebugModeFinishBlock)finishBlock
                             failureBlock:(DebugModeFailureBlock)failureBlock
{
    [DebugMode sendUpdateToServerWithProgressBlock:nil
                                       finishBlock:finishBlock
                                      failureBlock:failureBlock];
}

+ (void)sendUpdateToServerWithProgressBlock:(DebugModeProgressBlock)progressBlock
                                finishBlock:(DebugModeFinishBlock)finishBlock
                               failureBlock:(DebugModeFailureBlock)failureBlock
{
    [[SynchronizationManager sharedInstance] setChildLevelsDataWithSuccess:^{
        NSLog(@"Success set Levels");
        if (finishBlock) finishBlock();
        
    } failure:^(NSError *error) {
        NSLog(@"Failure set Levels with error: %@", [error localizedDescription]);
        if (failureBlock) failureBlock(error);
    } progress:progressBlock];
}

+ (void)sendUpdateToServerOlympiadWithFinishBlock:(DebugModeFinishBlock)finishBlock
                                     failureBlock:(DebugModeFailureBlock)failureBlock
{
    [DebugMode sendUpdateToServerOlympiadWithProgressBlock:nil
                                               finishBlock:finishBlock
                                              failureBlock:failureBlock];
}

+ (void)sendUpdateToServerOlympiadWithProgressBlock:(DebugModeProgressBlock)progressBlock
                                        finishBlock:(DebugModeFinishBlock)finishBlock
                                       failureBlock:(DebugModeFailureBlock)failureBlock
{
    [[SynchronizationManager sharedInstance] setChildOlympiadLevelsDataWithSuccess:^{
        NSLog(@"Success set olympiad Levels");
        if (finishBlock) finishBlock();
        
    } failure:^(NSError *error) {
        NSLog(@"Failure set olympiad Levels: %@", [error localizedDescription]);
        if (failureBlock) failureBlock(error);
    } progress:progressBlock];
}

@end
