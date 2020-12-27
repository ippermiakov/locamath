//
//  DebugMode.h
//  Mathematic
//
//  Created by SanyaIOS on 26.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef void(^DebugModeFinishBlock)();
typedef void(^DebugModeFailureBlock)(NSError *error);
typedef void(^DebugModeProgressBlock)(CGFloat progress);

@class OlympiadTask, Task, Level, OlympiadLevel, LevelsPath;

@interface DebugMode : NSObject

+ (BOOL)isCreatingChildsNeeded;
+ (void)createChildsIfNeeded;

+ (void)solveTaskOlympiad:(OlympiadTask *)olympiadTask
       withUpdateToServer:(BOOL)isUpdate
                 progress:(DebugModeProgressBlock)progressBlock
                   finish:(DebugModeFinishBlock)finishBlock
                  failure:(DebugModeFailureBlock)failureBlock;

+ (void)solveTask:(Task *)task
         progress:(DebugModeProgressBlock)progressBlock
           finish:(DebugModeFinishBlock)finishBlock
          failure:(DebugModeFailureBlock)failureBlock;

+ (void)solveTrainingTasksForLevel:(Level *)level
                          progress:(DebugModeProgressBlock)progressBlock
                            finish:(DebugModeFinishBlock)finishBlock
                           failure:(DebugModeFailureBlock)failureBlock;

+ (void)solveLevel:(Level *)level
          progress:(DebugModeProgressBlock)progressBlock
            finish:(DebugModeFinishBlock)finishBlock
           failure:(DebugModeFailureBlock)failureBlock;

+ (void)solveOlympiadLevel:(OlympiadLevel *)olimpiadLevel
        withUpdateToServer:(BOOL)isUpdate
                  progress:(DebugModeProgressBlock)progressBlock
                    finish:(DebugModeFinishBlock)finishBlock
                   failure:(DebugModeFailureBlock)failureBlock;

+ (void)solveLevelsPathWithColor:(NSString *)pathColor
                     levelNumber:(NSNumber *)levelNumber
                        progress:(DebugModeProgressBlock)progressBlock
                          finish:(DebugModeFinishBlock)finishBlock
                         failure:(DebugModeFailureBlock)failureBlock;

+ (void)autoLoginWithSuccess:(DebugModeFinishBlock)successBlock failure:(DebugModeFailureBlock)failureBlock;

@end
