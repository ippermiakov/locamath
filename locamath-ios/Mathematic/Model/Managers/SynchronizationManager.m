//
//  SynchronizationManager.m
//  Mathematic
//
//  Created by alexbutenko on 7/9/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SynchronizationManager.h"
#import "Child.h"
#import "MTFileParser.h"
#import "MTHTTPClient.h"
#import "NSManagedObject+Serialization.h"
#import "MBProgressHUD.h"
#import "AccountMail.h"
#import "LevelsPath.h"
#import "OlympiadLevel.h"
#import "Level.h"
#import "Task.h"
#import "GameManager.h"
#import "Game.h"
#import "OlympiadLevel.h"
#import "OlympiadTask.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"
#import "Action.h"
#import "ChildManager.h"
#import "DataUtils.h"
#import "BlocksKit.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"
#import "TaskError.h"
#import "Child.h"

static NSInteger const kNumberOfSyncWave = 2;
static NSString * const kTaskActionsKey = @"actions";
//static NSString * const kActionEtalonKey = @"etalon";
static NSString * const kTaskErrorKey = @"taskErrors";

NSString * const kSynchronizationFinishedNotification = @"kSynchronizationFinished";

@interface SynchronizationManager ()

@end

@implementation SynchronizationManager

+ (SynchronizationManager *)sharedInstance
{
    static dispatch_once_t pred;
    static SynchronizationManager *sharedInstance = nil;
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

#pragma mark - Requests to back-end

- (void)setChildLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                              failure:(SyncManagerFailureBlock)failureBlock
                             progress:(SyncManagerProgressBlock)progressBlock
{
    [self setChildLevelsDataWithSuccess:successBlock
                                failure:failureBlock
                               progress:progressBlock
                shouldUpdateActiveChild:YES];
}

- (void)setChildLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                              failure:(SyncManagerFailureBlock)failureBlock
                             progress:(SyncManagerProgressBlock)progressBlock
              shouldUpdateActiveChild:(BOOL)shouldUpdateActiveChild
{
    NSLog(@"!!! start sending S&E data");
    
    
    
    
    NSMutableArray *arrayLevels = [NSMutableArray new];
    
    NSSortDescriptor *sortDescriptor = [NSSortDescriptor sortDescriptorWithKey:@"identifier" ascending:YES];
    NSArray *sortedLevelPaths = [[ChildManager sharedInstance].currentChild.levelsPaths sortedArrayUsingDescriptors:@[sortDescriptor]];
    
    [sortedLevelPaths each:^(LevelsPath *levelPath) {
        NSMutableDictionary *dictForArray = [NSMutableDictionary new];
        dictForArray = (NSMutableDictionary *)[levelPath toDictionary];
        [arrayLevels addObject:dictForArray];
    }];

    void(^setLevelsBlock)() = ^() {
        NSLog(@"!!! sending S&E data");
        [[MTHTTPClient sharedMTHTTPClient] setLevelsData:arrayLevels
                                                progress:progressBlock
                                                 success:^(BOOL finished, NSError *error) {
                                                     [ChildManager sharedInstance].currentChild.lastLevelsSyncTimeInterval = @([[NSDate date] timeIntervalSince1970GMT]);
                                                     [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];

                                                     [self unblockSyncIfNeeded:shouldUpdateActiveChild];
                                                     
                                                     if (successBlock) {
                                                         successBlock();
                                                     }
                                                 } failure:^(BOOL finished, NSError *error) {
                                                     self.isSyncInProgress = NO;
                                                     
                                                     if (failureBlock) {
                                                         failureBlock(error);
                                                     }
                                                 }];
    };
    
    if (shouldUpdateActiveChild && !self.isSyncInProgress) {
        self.isSyncInProgress = YES;
        
        //ensure that child is active before sending data
        [[ChildManager sharedInstance] reloadChildDataIfNeededWithSuccess:^ {
            
            if  (![ChildManager sharedInstance].isReloadingChildData) {
                NSLog(@"reactivated %@", [ChildManager sharedInstance].currentChild.name);
                setLevelsBlock();
            }
        } failure:^(NSError *error) {
            self.isSyncInProgress = NO;
            
            NSLog(@"failed to reactivate %@", [ChildManager sharedInstance].currentChild.name);
        }];
    } else {
        setLevelsBlock();
    }
}

//static double const kDelayForUnblock = 5.0;

- (void)unblockSyncIfNeeded:(BOOL)shouldUnblock
{
    if (shouldUnblock) {
//        double delayInSeconds = kDelayForUnblock;
//        dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
//        dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
            self.isSyncInProgress = NO;
//        });
    }
}

- (void)getChildLevelsDataWithFinishBlock:(SyncManagerFinishBlock)finishBlock
                                  failure:(SyncManagerFailureBlock)failureBlock
                                 progress:(SyncManagerProgressBlock)progressBlock
{
    [[MTHTTPClient sharedMTHTTPClient] getLevelsWithSuccess:^(NSDictionary *successResponseData) {
        if (finishBlock) {
            finishBlock(successResponseData);
        }
    }
                                                   progress:progressBlock
                                                    failure:^(BOOL finished, NSError *error) {
        if (failureBlock) {
            failureBlock(error);
        }
    }];
}

- (void)setChildOlympiadLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                                      failure:(SyncManagerFailureBlock)failureBlock
                                     progress:(SyncManagerProgressBlock)progressBlock
{
    [self setChildOlympiadLevelsDataWithSuccess:successBlock
                                        failure:failureBlock
                                       progress:progressBlock
                        shouldUpdateActiveChild:YES];
}

- (void)setChildOlympiadLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                                      failure:(SyncManagerFailureBlock)failureBlock
                                     progress:(SyncManagerProgressBlock)progressBlock
                      shouldUpdateActiveChild:(BOOL)shouldUpdateActiveChild
{
    NSLog(@"!!! start sending olympiad data");
    
    NSMutableArray *arrayLevels = [NSMutableArray new];
    NSArray *olympiadLevels = [[ChildManager sharedInstance].currentChild.olympiadLevels allObjects];
    
    [olympiadLevels each:^(OlympiadLevel *olympiadLevel) {
        NSMutableDictionary *dictForArray = [NSMutableDictionary new];
        if (olympiadLevel.tasks.count) {
            dictForArray = (NSMutableDictionary *)[olympiadLevel toDictionary];
            [arrayLevels addObject:dictForArray];
        }
    }];
    
    
    void(^setOlympiadsBlock)() = ^() {
        [[MTHTTPClient sharedMTHTTPClient] setOlympiadLevelsData:arrayLevels
                                                        progress:progressBlock
                                                         success:^(BOOL finished, NSError *error) {
                                                             [ChildManager sharedInstance].currentChild.lastOlympiadLevelsSyncTimeInterval = @([[NSDate date] timeIntervalSince1970GMT]);
                                                             [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                                                             
                                                             [self unblockSyncIfNeeded:shouldUpdateActiveChild];
                                                             
                                                             if (successBlock) {
                                                                 successBlock();
                                                             }
                                                         } failure:^(BOOL finished, NSError *error) {
                                                             self.isSyncInProgress = NO;

                                                             if (failureBlock) {
                                                                 failureBlock(error);
                                                             }
                                                         }];
    };
    
    
    if (shouldUpdateActiveChild && !self.isSyncInProgress) {
        self.isSyncInProgress = YES;

        [[ChildManager sharedInstance] reloadChildDataIfNeededWithSuccess:^ {
            
            if  (![ChildManager sharedInstance].isReloadingChildData) {
                NSLog(@"reactivated %@", [ChildManager sharedInstance].currentChild.name);
                setOlympiadsBlock();
            }
        }
                                                                  failure:^(NSError *error) {
            self.isSyncInProgress = NO;
            NSLog(@"failed to reactivate %@", [ChildManager sharedInstance].currentChild.name);
        }];
    } else {
        setOlympiadsBlock();
    }
}

- (void)getChildOlympiadLevelsDataWithFinishBlock:(SyncManagerFinishBlock)finishBlock
                                          failure:(SyncManagerFailureBlock)failureBlock
                                         progress:(SyncManagerProgressBlock)progressBlock
{
    [[MTHTTPClient sharedMTHTTPClient] getOlympiadLevelsWithSuccess:^(NSDictionary *successResponseData) {
        if (finishBlock) {
            finishBlock(successResponseData);
        }
    }
                                                           progress:progressBlock
                                                            failure:^(BOOL finished, NSError *error) {
        if (failureBlock) {
            failureBlock(error);
        }
    }];
}

#pragma mark - Importing

- (void)getChildDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                        failure:(SyncManagerFailureBlock)failureBlock
                       progress:(SyncManagerProgressBlock)progressBlock
{
    failureBlock = failureBlock ?: ^(NSError *error){};
    
    __block NSInteger syncObjCount = 0;
    
    __block CGFloat levelsProgress = 0;
    __block CGFloat olympiadsProgress = 0;
    
    void(^finishSyncBlock)() = ^{
        syncObjCount++;
        if (syncObjCount == kNumberOfSyncWave) {
            if (successBlock) dispatch_async(dispatch_get_main_queue(), successBlock);
        }
    };
    
    void(^overallProgressBlock)() = ^{
        CGFloat overallProgress = (levelsProgress + olympiadsProgress)/2;
        progressBlock(overallProgress);
    };
    
    [self getChildLevelsDataWithFinishBlock:^(NSDictionary *obj) {
        NSLog(@"Success get S&E Levels");
            
        NSArray *levels = obj[@"levels"];
        
        if ([levels count]) {
//            levels = [[obj[@"levels"] objectAtIndex:0] objectForKey:@"levels"];
//            
//            NSLog(@"received data for Red path: %@", levels);

//            NSDictionary *firstLevelDictionary = [levels match:^BOOL(NSDictionary *dictionary) {
//                return [dictionary[@"identifierString"] isEqualToString:@"1-A-1"];
//            }];
//            
//            NSLog(@"received data for Red path Level 1: %@", firstLevelDictionary);
        }

        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, NULL), ^{
            
            Child *child = [[ChildManager sharedInstance].currentChild inThreadContext];

            NSArray *paths = obj[@"levels"];
            
            [child.levelsPaths each:^(LevelsPath *levelsPath) {
                    
                    NSDictionary *levelsPathDictionary = [paths match:^BOOL(NSDictionary *pathDictionary) {
                        return [pathDictionary[@"identifierNumber"] isEqualToNumber:levelsPath.identifier];
                    }];
                    
                    NSArray *levelsDictionaries = levelsPathDictionary[@"levels"];
                    
                    [levelsDictionaries each:^(NSDictionary *levelDictionary) {
                        
                        Level *level = [levelsPath.levels match:^BOOL(Level *level) {
                            return [level.identifier isEqualToString:levelDictionary[@"identifierString"]];
                        }];
                        
                        NSMutableDictionary *levelDictionaryWithoutTasks = [levelDictionary mutableCopy];
                        [levelDictionaryWithoutTasks removeObjectForKey:@"tasks"];
                        
                        [self importTasksFromDictionary:levelDictionary withLevel:level];
                    }];
                
                    NSLog(@"save S&E levels data to persistence");
                    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                }];
        
            finishSyncBlock();
        });
    }
                                    failure:^(NSError *error) {
        failureBlock(error);
    }
                                   progress:^(CGFloat progress) {
                                       levelsProgress = progress;
                                       overallProgressBlock();
                                   }];
    
    [self getChildOlympiadLevelsDataWithFinishBlock:^(NSDictionary *obj) {
        NSLog(@"Success get Olympiad Levels");
        
        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, NULL), ^{

            Child *child = [[ChildManager sharedInstance].currentChild inThreadContext];

            [child.olympiadLevels each:^(OlympiadLevel *level) {
                NSArray *olympyadLevelsData = obj[@"olymplevels"];
                
                NSDictionary * olympiadLevel = [olympyadLevelsData match:^BOOL(NSDictionary *levelDictionary) {
                    return [levelDictionary[@"identifierString"] isEqualToString:level.identifier];
                }];
                
                level.lastChangeDate = [NSDate dateWithTimeIntervalSince1970:[olympiadLevel[@"lastChangeDate"] doubleValue]];
            
                NSArray *olympiadTasks = olympiadLevel[@"tasks"];
                
                [self importOlympiadTasksFromDictionary:olympiadTasks withLevel:level];
                
                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
            }];
            
            finishSyncBlock();
        });

    } failure:^(NSError *error) {
        failureBlock(error);
    } progress:^(CGFloat progress) {
        olympiadsProgress = progress;
        overallProgressBlock();
    }];
}

- (void)putChildDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                        failure:(SyncManagerFailureBlock)failureBlock
                       progress:(SyncManagerProgressBlock)progressBlock
{
    failureBlock = failureBlock ?: ^(NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};
    
    BOOL isOlympiadLevelsNeedToPut = [self isOlympiadLevelsNeedToPut];
    BOOL isLevelsNeedToPut = [self isLevelsNeedToPut];
    
    NSUInteger numberOfSyncItems = isOlympiadLevelsNeedToPut + isLevelsNeedToPut;
    
    NSLog(@"isOlympiadLevelsNeedToPut: %@", isOlympiadLevelsNeedToPut ? @"YES":@"NO");
    NSLog(@"isLevelsNeedToPut: %@", isLevelsNeedToPut ? @"YES":@"NO");
    
    if (numberOfSyncItems == 0) {
        if (successBlock) successBlock();
        return;
    }
    
    __block NSInteger putObjCount = 0;
    __block CGFloat levelsProgress = 0;
    __block CGFloat olympiadsProgress = 0;
    
    void(^finishSyncBlock)() = ^{
        putObjCount++;
        if (putObjCount == numberOfSyncItems) {
            if (successBlock) successBlock();
        }
    };
    
    void(^overallProgressBlock)() = ^{
        CGFloat overallProgress = 0;
        
        if (isLevelsNeedToPut) {
            overallProgress = levelsProgress;
            
            if (isOlympiadLevelsNeedToPut) {
                overallProgress = (overallProgress + olympiadsProgress) / 2;
            }
        } else if (isOlympiadLevelsNeedToPut) {
            overallProgress = olympiadsProgress;
        }
        
        progressBlock(overallProgress);
    };
    
    if (isOlympiadLevelsNeedToPut) {
        [self setChildOlympiadLevelsDataWithSuccess:^{
            finishSyncBlock();
        } failure:^(NSError *error) {
            failureBlock(error);
        } progress:^(CGFloat progress) {
            olympiadsProgress = progress;
            overallProgressBlock();
        } shouldUpdateActiveChild:NO];
    }
    
    if (isLevelsNeedToPut) {
        [self setChildLevelsDataWithSuccess:^{
            finishSyncBlock();
        } failure:^(NSError *error) {
            failureBlock(error);
        } progress:^(CGFloat progress) {
            levelsProgress = progress;
            overallProgressBlock();
        } shouldUpdateActiveChild:NO];
    }
}

- (void)syncDataIfNeededWithSuccess:(SyncManagerSuccessBlock)successBlock
                            failure:(SyncManagerFailureBlock)failureBlock
                    gettingProgress:(SyncManagerProgressBlock)gettingProgressBlock
                    sendingProgress:(SyncManagerProgressBlock)sendingProgressBlock
{
    void(^onSuccess)(void) = ^() {
        if (successBlock) {
            successBlock();
        }
    };
    
    void(^onFailure)(NSError *) = ^(NSError *error) {
        if (failureBlock) {
            failureBlock(error);
        }
    };
    
    void(^onGettingProgress)(CGFloat) = ^(CGFloat progress) {
        if (gettingProgressBlock) {
            gettingProgressBlock(progress);
        }
    };
    
    void(^onSendingProgress)(CGFloat) = ^(CGFloat progress) {
        if (sendingProgressBlock) {
            sendingProgressBlock(progress);
        }
    };
    
    if ([[MTHTTPClient sharedMTHTTPClient] canSyncChildData]) {
        
//       NSLog(@"<<< isSyncNeeded: %@ isSyncInProgress: %@", [currentChild.isSyncNeeded stringValue], self.isSyncInProgress ? @"YES":@"NO");
        if ([[ChildManager sharedInstance].currentChild.isSyncNeeded boolValue] && !self.isSyncInProgress) {
            self.isSyncInProgress = YES;
            
            NSLog(@"<<<<<< SYNC STARTED");
            
            [self getChildDataWithSuccess:^{
                
                [ChildManager sharedInstance].currentChild.isSyncCompleted = @YES;
                [ChildManager sharedInstance].currentChild.isSyncNeeded = @NO;
                
                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];

                [[NSNotificationCenter defaultCenter] postNotificationName:kSynchronizationFinishedNotification object:nil];
                
                NSLog(@"<<<<<<< SYNC COMPLETED");
                
                onSuccess();
                
                [self putChildDataWithSuccess:^{
                    [self unblockSyncIfNeeded:YES];
                }
                                      failure:^(NSError *error) {
                                          self.isSyncInProgress = NO;

                                          NSLog(@"!!! PUT child data FAILED because of %@", [error localizedDescription]);

                                          onFailure(error);
                                      }
                                     progress:onSendingProgress];
                
            } failure:^(NSError *error) {
                NSLog(@"!!! GET child data FAILED because of %@", [error localizedDescription]);
                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                [[NSNotificationCenter defaultCenter] postNotificationName:kSynchronizationFinishedNotification object:nil];
                self.isSyncInProgress = NO;
                
                onFailure(error);
                
            } progress:onGettingProgress];
        } else {
            onSuccess();
        }
        
    } else {
        [ChildManager sharedInstance].currentChild.isSyncNeeded = @YES;
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        
        onSuccess();
    }
}

- (void)cancelSynchronization
{
    [[MTHTTPClient sharedMTHTTPClient] cancelCurrentRequest];
}

#pragma mark - Helper

- (void)importTasksFromDictionary:(NSDictionary *)dictionary withLevel:(Level *)level
{
    NSLog(@"importing level %@ ", level.identifier);
    
    NSArray *tasksDictionaries = dictionary[@"tasks"];
    
    [tasksDictionaries each:^(NSDictionary *taskDictionary) {
        
        Task *task = [level.tasks match:^BOOL(Task *task) {
            return [task.identifier isEqualToString:taskDictionary[@"identifierString"]];
        }];
        
        NSArray *tasksErrorDictionaries = taskDictionary[kTaskErrorKey];
        
        NSMutableDictionary *dictionaryWithoutTaskErrors = [taskDictionary mutableCopy];
        [dictionaryWithoutTaskErrors removeObjectForKey:kTaskErrorKey];
        
        if ([taskDictionary[@"statusNumber"] integerValue] != kTaskStatusUndefined) {
            task.status = [taskDictionary[@"statusNumber"] integerValue];
        }
        
        //sync if needed
        if ([taskDictionary[@"statusNumber"] integerValue] == kTaskStatusSolved ||
            [taskDictionary[@"statusNumber"] integerValue] == kTaskStatusSolvedNotAll) {
            
            [self importActionsFromDictionary:dictionaryWithoutTaskErrors withTask:task];
            
        } else if ((task.status == kTaskStatusError &&
                    [taskDictionary[@"statusNumber"] integerValue] == kTaskStatusError) ||
                   (task.status == kTaskStatusStarted &&
                    [taskDictionary[@"statusNumber"] integerValue] == kTaskStatusStarted)) {
                       
                       NSTimeInterval time1 = [task.lastChangeDate timeIntervalSince1970GMT];
                       NSTimeInterval time2 = [taskDictionary[@"lastChangeDate"] doubleValue];
                       
                       if (time1 <= time2) {
                           [self importActionsFromDictionary:dictionaryWithoutTaskErrors withTask:task];
                       }
        } else if (task.status != kTaskStatusSolved &&
                  [taskDictionary[@"statusNumber"] integerValue] != kTaskStatusUndefined) {
           [self importActionsFromDictionary:dictionaryWithoutTaskErrors withTask:task];
        }
        
        if (tasksErrorDictionaries.count > 0) {
            [self importTaskErrorsFromArray:tasksErrorDictionaries withTask:task];
        }
    }];
}

- (void)importActionsFromDictionary:(NSDictionary *)dictionary withTask:(Task *)task
{
    NSLog(@"import actions for task %@", task.identifier);

    NSMutableDictionary *dictionaryWithoutActions = [dictionary mutableCopy];
    [dictionaryWithoutActions removeObjectForKey:kTaskActionsKey];
    [dictionaryWithoutActions removeObjectForKey:kTaskErrorKey];
    
    [task importValuesForKeysWithObject:dictionaryWithoutActions];
    
    NSArray *actionsDictionaries = dictionary[kTaskActionsKey];
    
    //remove duplicates from dictionary, we get them from back-end for unknown reasons
//    NSMutableSet *uniqueEtalons = [NSMutableSet setWithArray:[dictionary[kTaskActionsKey] valueForKey:kActionEtalonKey]];
//    NSLog(@"action etalons: %@", uniqueEtalons);
    
    [actionsDictionaries each:^(NSDictionary *actionDictionary) {
        
//        NSNumber *actionEtalon = actionDictionary[kActionEtalonKey];
        
//        if (![uniqueEtalons containsObject:actionEtalon]) {
//            NSLog(@"attempt to add duplicate action: %@", actionDictionary);
//            return;
//        }
        
        // action from correct solution/expression with proper etalon
        Action *action = [task.actions match:^BOOL(Action *action) {
            return /*![action.etalon isEqualToNumber:@0] &&
                    [action.etalon isEqualToNumber:actionEtalon] &&
                    [action.typeNumber isEqualToNumber:actionDictionary[@"typeNumber"]]*/
                [action.identifier isEqualToString:actionDictionary[@"identifierString"]];
        }];
        
        NSMutableDictionary *dictionaryWithoutSubActions = [actionDictionary mutableCopy];
        [dictionaryWithoutSubActions removeObjectForKey:@"subActions"];
        
        if (!action) {
            action = [Action importFromObject:dictionaryWithoutSubActions];
            action.task = /*[task inThreadContext]*/task;
        } else {
            [action importValuesForKeysWithObject:dictionaryWithoutSubActions];
        }
        
        [self importSubActionsFromArrayOfDictionaries:actionDictionary[@"subActions"]
                                           withAction:action];
        
//        [uniqueEtalons removeObject:actionEtalon];
    }];
    
    //remove excessive error actions
    
    NSArray *expressions = [DataUtils actionsOfType:kActionTypeExpression
                                        fromActions:[task.actions allObjects]];
    
    NSArray *solutions = [DataUtils actionsOfType:kActionTypeSolution
                                      fromActions:[task.actions allObjects]];
    
    NSInteger numberOfSolutionsToRemove = [solutions count] - [task.expressions count];
    NSInteger numberOfExpressionsToRemove = [expressions count] - [task.expressions count];
    
    if (numberOfSolutionsToRemove > 0) {
        [self removeIncorrectActionsFromActions:solutions numberOfActionsToRemove:numberOfSolutionsToRemove];
    }
    
    if (numberOfExpressionsToRemove > 0) {
        [self removeIncorrectActionsFromActions:expressions numberOfActionsToRemove:numberOfExpressionsToRemove];
    }
}

- (void)importTaskErrorsFromArray:(NSArray *)arrayDictionaris withTask:(Task *)task
{
    NSLog(@"import task errors for task %@", task.identifier);
    
     //static NSString * const kTaskActionsKey = @"actions";
    [arrayDictionaris each:^(NSDictionary *taskErrorDictionary) {
        
        NSArray *actionsDict = taskErrorDictionary[kTaskActionsKey];
        
        NSMutableDictionary *taskErrorWithoutActions = [taskErrorDictionary mutableCopy];
        [taskErrorWithoutActions removeObjectForKey:kTaskActionsKey];
        
        TaskError *taskError = [task.taskErrors match:^BOOL(TaskError *taskError) {
            return [taskError.identifier isEqualToNumber:taskErrorWithoutActions[@"identifier"]];
        }];
        
        if (taskError) {
            [taskError importValuesForKeysWithObject:taskErrorWithoutActions];
            [self importActionsFromArray:actionsDict withTaskError:taskError];
        } else {
            taskError = [TaskError importFromObject:taskErrorWithoutActions];
            taskError.task = /*[task inThreadContext]*/task;
            [self importActionsFromArray:actionsDict withTaskError:taskError];
        }
    }];
}

- (void)importActionsFromArray:(NSArray *)arrayActions withTaskError:(TaskError *)taskError
{
    NSLog(@"import actions for taskerror of task %@", taskError.task.identifier);

    [arrayActions each:^(NSDictionary *actionDictionary) {
        
        //NSNumber *actionEtalon = actionDictionary[kActionEtalonKey];
        NSString *actionId = actionDictionary[@"identifierString"];
        
        // action from correct solution/expression with proper etalon
        Action *action = [taskError.actions match:^BOOL(Action *action) {
            return   [action.identifier isEqualToString:actionId] &&
            [action.typeNumber isEqualToNumber:actionDictionary[@"typeNumber"]];
        }];
        
        NSMutableDictionary *dictionaryWithoutSubActions = [actionDictionary mutableCopy];
        [dictionaryWithoutSubActions removeObjectForKey:@"subActions"];
        
        if (!action) {
            action = [Action importFromObject:dictionaryWithoutSubActions];
            action.taskError = taskError/*[taskError inThreadContext]*/;
        } else {
            [action importValuesForKeysWithObject:dictionaryWithoutSubActions];
        }

        [self importSubActionsFromArrayOfDictionaries:actionDictionary[@"subActions"]
                                           withAction:action];
    }];
    
    //for removing same errors except most recent
    NSMutableArray *sameErrors = [[[taskError.task.taskErrors allObjects] select:^BOOL(TaskError *obj) {
        return [taskError isTaskErrorEqualToTaskError:obj];
    }] mutableCopy];
    
    if (sameErrors.count > 1) {
        [sameErrors sortedArrayUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1, id<AbstractAchievement> obj2) {
            return [obj1.lastChangeDate timeIntervalSince1970GMT] < [obj2.lastChangeDate timeIntervalSince1970GMT];
        }];
        [sameErrors removeLastObject];
        [sameErrors makeObjectsPerformSelector:@selector(deleteEntity)];
    }
}

- (void)removeIncorrectActionsFromActions:(NSArray *)actions
                  numberOfActionsToRemove:(NSInteger)numberOfActionsToRemove
{
    NSArray *incorrectExpressions = [actions reject:^BOOL(Action *action) {
        return [action.isCorrect boolValue];
    }];
    
    [incorrectExpressions enumerateObjectsUsingBlock:^(Action *action, NSUInteger idx, BOOL *stop) {
        if (idx < numberOfActionsToRemove) {
            [action deleteEntity];
        } else {
            *stop = YES;
        }
    }];
}

- (void)importSubActionsFromArrayOfDictionaries:(NSArray *)arrayOfDictionaries withAction:(Action *)action
{
    [arrayOfDictionaries each:^(NSDictionary *actionDictionary) {
        Action *subAction = [action.subActions match:^BOOL(Action *action) {
            return [action.identifier isEqualToString:actionDictionary[@"identifierString"]];
        }];
        
        if (subAction) {
//            if ([action.isCorrect isEqualToNumber:subAction.isCorrect]) {
            [subAction importValuesForKeysWithObject:actionDictionary];
//            } else {
//                [subAction deleteEntity];
//            }
        } else {
            subAction = [Action importFromObject:actionDictionary];
            subAction.parentAction = action/*[action inThreadContext]*/;
        }
    }];
}

- (void)importOlympiadTasksFromDictionary:(NSArray *)taskArray withLevel:(OlympiadLevel *)level
{
    [level.tasks each:^(OlympiadTask *olympiadTask) {
        NSDictionary *olympiadTaskDict = [taskArray match:^BOOL(NSDictionary *objTask) {
            return [objTask[@"identifierNumber"] isEqualToNumber:olympiadTask.identifier];
        }];
        
        NSMutableDictionary *olympiadTaskDictionary = [olympiadTaskDict mutableCopy];
        [olympiadTaskDictionary removeObjectForKey:@"actions"];
        
        //sync if needed
        if ([olympiadTaskDictionary[@"statusNumber"] integerValue] == kTaskStatusSolved) {
            [self importOlympiadActionsFromDictionary:olympiadTaskDict withTask:olympiadTask];
            //update levels info
            olympiadTask.status = kTaskStatusSolved;
//            NSLog(@"olympid task level : %@", olympiadTask.level);
            
        } else if ((olympiadTask.status == kTaskStatusError &&
                    [olympiadTaskDictionary[@"statusNumber"] integerValue] == kTaskStatusError) ||
                   (olympiadTask.status == kTaskStatusStarted &&
                    [olympiadTaskDictionary[@"statusNumber"] integerValue] == kTaskStatusStarted)) {
                       
                       NSTimeInterval time1 = [olympiadTask.lastChangeDate timeIntervalSince1970GMT];
                       NSTimeInterval time2 = [olympiadTaskDictionary[@"lastChangeDate"] doubleValue];
                       
                       if (time1 < time2) {
                           [self importOlympiadActionsFromDictionary:olympiadTaskDict withTask:olympiadTask];
                       }
                   } else if (olympiadTask.status != kTaskStatusSolved &&
                              [olympiadTaskDictionary[@"statusNumber"] integerValue] != kTaskStatusUndefined) {
                       [self importOlympiadActionsFromDictionary:olympiadTaskDict withTask:olympiadTask];
                   }
    }];
}

- (void)importOlympiadActionsFromDictionary:(NSDictionary *)dictionary withTask:(OlympiadTask *)task
{
    NSArray *actionsArray = dictionary[@"actions"];
    
    NSMutableDictionary *olympiadTaskDictionary = [dictionary mutableCopy];
    [olympiadTaskDictionary removeObjectForKey:@"actions"];
    
    [task importValuesForKeysWithObject:olympiadTaskDictionary];
    
    [task.actions each:^(OlympiadAction *senderAction) {
        
        NSDictionary * action = [actionsArray match:^BOOL(NSDictionary *actionDictionary) {
            return [actionDictionary[@"identifierNumber"] isEqualToNumber:senderAction.identifier];
        }];
        
        NSArray *hintsArray = action[@"hints"];
        
        NSMutableDictionary *actionDict = [action mutableCopy];
        [actionDict removeObjectForKey:@"hints"];
        
        [senderAction importValuesForKeysWithObject:actionDict];
        
        [senderAction.hints each:^(OlympiadHint *senderHint) {
            
            NSDictionary *hint = [hintsArray match:^BOOL(NSDictionary *hintDictionary) {
                return [hintDictionary[@"identifierNumber"] isEqualToNumber:senderHint.identifier];
            }];
            
            [senderHint importValuesForKeysWithObject:hint];
            [senderHint updateUserInputIfNeeded];
        }];
    }];
}

- (BOOL)isOlympiadLevelsNeedToPut
{
    Child *child = [ChildManager sharedInstance].currentChild;
    NSArray *solvedOlympiadTasks = [DataUtils olympiadTasksFromCurrentChild];
    
    id<AbstractTask> lastSolvedTask = nil;
    
    if ([solvedOlympiadTasks count]) {
        lastSolvedTask = solvedOlympiadTasks[0];
    }
    
    NSTimeInterval lastSolvedOlympiadTaskTimeInterval = [lastSolvedTask.lastChangeDate timeIntervalSince1970GMT];
    
//    NSLog(@"!!! lastOlympiadLevelsSyncTimeInterval: %@ lastSolvedOlympiadTaskTimeInterval: %@",
//          [NSDate dateWithTimeIntervalSince1970:[child.lastOlympiadLevelsSyncTimeInterval doubleValue]],
//          [NSDate dateWithTimeIntervalSince1970:lastSolvedOlympiadTaskTimeInterval]);
    
    return [child.lastOlympiadLevelsSyncTimeInterval doubleValue] < lastSolvedOlympiadTaskTimeInterval;
}

- (BOOL)isLevelsNeedToPut
{
    Child *child = [ChildManager sharedInstance].currentChild;
    NSArray *solvedTasks = [DataUtils tasksFromCurrentChild];
    
    id<AbstractTask> lastSolvedTask = nil;
    
    if ([solvedTasks count]) {
        lastSolvedTask = solvedTasks[0];
    }
    
    NSTimeInterval lastSolvedTaskTimeInterval = [lastSolvedTask.lastChangeDate timeIntervalSince1970GMT];
    
    NSLog(@"!!! lastLevelsSyncTime=: %@ lastSolvedTaskTime: %@", [NSDate dateWithTimeIntervalSince1970GMT:[child.lastLevelsSyncTimeInterval doubleValue]], [NSDate dateWithTimeIntervalSince1970GMT:lastSolvedTaskTimeInterval]);
    
    return [child.lastLevelsSyncTimeInterval doubleValue] < lastSolvedTaskTimeInterval;
}

@end
