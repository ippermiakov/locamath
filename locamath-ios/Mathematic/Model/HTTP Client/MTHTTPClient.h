//
//  MTHTTPClient.h
//  Mathematic
//
//  Created by alexbutenko on 3/29/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AFHTTPClient.h"

typedef void (^MTHTTPCompletionBlock)(BOOL finished, NSError *error);
typedef void (^MTHTTPSuccessResponseExecutionBlock)(NSDictionary *successResponseData);
typedef void (^MTHTTPProgressBlock)(CGFloat progress);

extern NSString * const kAuthorizationFailureNotification;
extern NSString * const kChildSelectionFailureNotification;
extern NSString * const kChildSelectionSuccessNotification;
extern NSString * const kParentExistsFailureNotification;

extern NSString * const kCity;
extern NSString * const kCountry;
extern NSString * const kWorld;

extern NSString * const kName;
extern NSString * const kPoints;
extern NSString * const kUndefined;
extern NSString * const kRate;
extern NSString * const kChildID;

extern NSString * const kSocialIDKey;
extern NSString * const kUserPasswordKey;
extern NSString * const kUserEmailKey;

extern NSUInteger const kAuthorizationFailureCode;
extern NSUInteger const kChildSelectionFailureCode;

extern NSInteger const kParentAlreadyExistsErrorCode;

@interface MTHTTPClient : AFHTTPClient

@property (unsafe_unretained, atomic) BOOL isRequestsIgnored;
@property (unsafe_unretained, readwrite, nonatomic) BOOL isChildActivated;

+ (MTHTTPClient *)sharedMTHTTPClient;

- (BOOL)isReachable;
- (BOOL)isParentAuthentificated;
- (BOOL)canSyncChildData;
//invoke to add token to parent, when offline
- (void)addDefaultAccessToken;

- (void)cancelCurrentRequest;
+ (BOOL)validateEmail:(NSString *)email withError:(NSError * __autoreleasing *)error;
+ (BOOL)validateNickname:(NSString *)nickname withError:(NSError * __autoreleasing *)error;

#pragma mark - Login&Register

- (void)loginUserWithEmail:(NSString *)email
                  password:(NSString *)pass
     shouldSaveAccessToken:(BOOL)shouldSaveAccessToken
                   success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                   failure:(MTHTTPCompletionBlock)failureBlock;

- (void)registerUserWithEmail:(NSString *)email
                     password:(NSString *)pass
                      success:(MTHTTPCompletionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock;

- (void)loginUserWithSocialID:(NSString *)sociadID
                      success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock;

- (void)registerWithSocialID:(NSString *)socialID
                       email:(NSString *)email
                     success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                     failure:(MTHTTPCompletionBlock)failureBlock;

- (void)deleteAccountWithSuccess:(MTHTTPCompletionBlock)successBlock
                         failure:(MTHTTPCompletionBlock)failureBlock;

- (void)deleteAccountWithEmail:(NSString *)email
                       success:(MTHTTPCompletionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock;

- (void)logout;

- (void)resetPasswordWithEmail:(NSString *)email
                       success:(MTHTTPCompletionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock;

- (void)changePasswordWithEmail:(NSString *)email
                currentPassword:(NSString *)currentPassword
                    newPassword:(NSString *)newPassword
                        success:(MTHTTPCompletionBlock)successBlock
                        failure:(MTHTTPCompletionBlock)failureBlock;

#pragma mark - Child

- (void)addChildWithName:(NSString *)name
                 success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                 failure:(MTHTTPCompletionBlock)failureBlock;

- (void)deleteChildWithName:(NSString *)name
                 success:(MTHTTPCompletionBlock)successBlock
                 failure:(MTHTTPCompletionBlock)failureBlock;

- (void)getChildsSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                 failure:(MTHTTPCompletionBlock)failureBlock;

- (void)setActiveChildWithID:(NSString *)identifier
                       success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock;

- (void)setChildDetails:(NSDictionary *)data
                success:(MTHTTPCompletionBlock)successBlock
                failure:(MTHTTPCompletionBlock)failureBlock;

- (void)getChildsRateSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                     failure:(MTHTTPCompletionBlock)failureBlock;

#pragma mark - Update Levels

- (void)setLevelsData:(NSArray *)data
             progress:(MTHTTPProgressBlock)progressBlock
              success:(MTHTTPCompletionBlock)successBlock
              failure:(MTHTTPCompletionBlock)failureBlock;

- (void)getLevelsWithSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                    progress:(MTHTTPProgressBlock)progressBlock
                     failure:(MTHTTPCompletionBlock)failureBlock;

#pragma mark - Update Olympiad Levels

- (void)setOlympiadLevelsData:(NSArray *)data
                     progress:(MTHTTPProgressBlock)progressBlock
                      success:(MTHTTPCompletionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock;

- (void)getOlympiadLevelsWithSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                            progress:(MTHTTPProgressBlock)progressBlock
                             failure:(MTHTTPCompletionBlock)failureBlock;

#pragma mark - Tasks/Helps Objectives Data

- (void)getGameDataWithObject:(NSString *)objectString
                  levelNumber:(NSNumber *)levelNumber
                   isOlympiad:(NSNumber *)isOlympiad
                      success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock;

#pragma mark - Parent

- (void)parentUpdateLocationWithSuccess:(MTHTTPCompletionBlock)successBlock
                            failure:(MTHTTPCompletionBlock)failureBlock;

- (void)updateSpendTimes:(NSArray *)spendTimeArray withSuccess:(MTHTTPCompletionBlock)successBlock
                           failure:(MTHTTPCompletionBlock)failureBlock;

@end
