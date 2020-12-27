//
//  SocialHTTPClient.h
//  Flixa
//
//  Created by alexbutenko on 5/21/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "AFNetworking.h"
#import "MTHTTPClient.h"

typedef NS_ENUM(NSUInteger, SocialType) {
    FacebookType
};

typedef void (^SocialRegistrationCompletionBlock)(NSString *userEmail, NSString *socialID, SocialType idType);
typedef void (^SocialCompletionResponse) (SocialType sType, NSNumber *socialId, NSError *err);

typedef void (^SocialHTTPImageURLRetrievingBlock)(NSString *stringURL);

@interface SocialHTTPClient : AFHTTPClient

+ (SocialHTTPClient *)sharedSocialHTTPClient;

+ (void)registerViaFBWithSuccess:(SocialRegistrationCompletionBlock)successBlock
                         failure:(MTHTTPCompletionBlock)failureBlock;

+ (void)postMessageToFB:(NSString *)updateMessage
  withAdditionalMessage:(NSString *)additionalMessage
                success:(MTHTTPCompletionBlock)successBlock
                failure:(MTHTTPCompletionBlock)failureBlock;

@end
