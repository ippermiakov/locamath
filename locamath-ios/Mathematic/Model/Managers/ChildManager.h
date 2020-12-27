//
//  ChildManager.h
//  Mathematic
//
//  Created by Developer on 25.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Child.h"

typedef void (^ChildManagerBlockType)(Child *);
typedef void (^ChildManagerFailureBlock)(NSError *error);
typedef void (^ChildManagerSuccessBlock)();
typedef void (^ChildManagerFinishBlock)(NSDictionary *obj);

@interface ChildManager : NSObject

@property (readonly, nonatomic) Child   *currentChild;
@property (nonatomic, copy)   ChildManagerBlockType addChildBlock;
@property (nonatomic, copy)   ChildManagerBlockType addChildCreateGameBlock;
@property (unsafe_unretained, nonatomic) BOOL isReloadingChildData;

+ (ChildManager *)sharedInstance;

- (void)createChildWithName:(NSString *)childname
                    success:(ChildManagerSuccessBlock)successBlock
                    failure:(ChildManagerFailureBlock)failureBlock;

- (void)setActiveChildWithID:(NSString *)identifier
                       success:(ChildManagerFinishBlock)successBlock
                       failure:(ChildManagerFailureBlock)failureBlock;

- (BOOL)removeChild:(Child *)child
            success:(ChildManagerSuccessBlock)successBlock
            failure:(ChildManagerFailureBlock)failureBlock;

- (void)loadChildsWithSuccess:(ChildManagerSuccessBlock)successBlock
                      failure:(ChildManagerFailureBlock)failureBlock;

- (void)updateChildWithSuccess:(ChildManagerSuccessBlock)successBlock
                       failure:(ChildManagerFailureBlock)failureBlock;

- (void)childsRateWithSuccess:(ChildManagerFinishBlock)finishBlock
                      failure:(ChildManagerFailureBlock)failureBlock;

- (void)logoutCurrentChild;

- (void)addAccountForChildWithName:(NSString *)name;

- (void)reloadChildDataIfNeededWithSuccess:(ChildManagerSuccessBlock)successBlock
                                   failure:(ChildManagerFailureBlock)failureBlock;

- (void)createFBAccountIfNeededWithEmail:(NSString *)email;

- (void)createDefaultChildWithCompletion:(ChildManagerSuccessBlock)completionBlock;

- (BOOL)isParentHaveChildWithName:(NSString *)name;

- (void)updateCurrentChildReference;

- (void)updateSpendTimeStatisticIfNeeded;
//- (void)addDefaultChildToParentIfNeeded;

@end
