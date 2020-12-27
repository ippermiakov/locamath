//
//  SynchronizationManager.h
//  Mathematic
//
//  Created by alexbutenko on 7/9/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef void (^SyncManagerFailureBlock)(NSError *error);
typedef void (^SyncManagerSuccessBlock)();
typedef void (^SyncManagerFinishBlock)(NSDictionary *obj);
typedef void (^SyncManagerProgressBlock)(CGFloat progress);

extern NSString * const kSynchronizationFinishedNotification;

@interface SynchronizationManager : NSObject

+ (SynchronizationManager *)sharedInstance;

@property (unsafe_unretained, nonatomic) BOOL isSyncInProgress;

- (void)setChildLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                              failure:(SyncManagerFailureBlock)failureBlock
                             progress:(SyncManagerProgressBlock)progressBlock;

- (void)getChildLevelsDataWithFinishBlock:(SyncManagerFinishBlock)finishBlock
                                  failure:(SyncManagerFailureBlock)failureBlock
                                 progress:(SyncManagerProgressBlock)progressBlock;

- (void)setChildOlympiadLevelsDataWithSuccess:(SyncManagerSuccessBlock)successBlock
                                      failure:(SyncManagerFailureBlock)failureBlock
                                     progress:(SyncManagerProgressBlock)progressBlock;

- (void)getChildOlympiadLevelsDataWithFinishBlock:(SyncManagerFinishBlock)finishBlock
                                          failure:(SyncManagerFailureBlock)failureBlock
                                         progress:(SyncManagerProgressBlock)progressBlock;

//network reachability status changing
- (void)syncDataIfNeededWithSuccess:(SyncManagerSuccessBlock)successBlock
                            failure:(SyncManagerFailureBlock)failureBlock
                    gettingProgress:(SyncManagerProgressBlock)gettingProgressBlock
                    sendingProgress:(SyncManagerProgressBlock)sendingProgressBlock;

- (void)cancelSynchronization;

@end
