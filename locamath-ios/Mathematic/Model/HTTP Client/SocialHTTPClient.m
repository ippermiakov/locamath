//
//  SocialHTTPClient.m
//  Flixa
//
//  Created by alexbutenko on 5/21/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "SocialHTTPClient.h"
#import "FacebookSDK.h"

@interface SocialHTTPClient ()

@property (strong, nonatomic) FBRequest *pendingRequest;

@end

@implementation SocialHTTPClient

static NSUInteger const kTimeoutInterval = 10;
static NSString * const kFBPostFlixaIconURL = @"http://s8.postimg.org/wzq2tvuqp/Icon_72_2x.png";
static NSString * const kLocaMathURL = @"http://locamath.com";
static NSString * const kFBUserIDKey = @"id";
static NSString * const kFBPublishPermissionsString = @"publish_actions";
NSString * const kMTUserFB_IDKey = @"fb_id";

+ (SocialHTTPClient *)sharedSocialHTTPClient
{
    static dispatch_once_t pred;
    static SocialHTTPClient *_sharedSocialHTTPClient = nil;
    
    dispatch_once(&pred, ^{
        _sharedSocialHTTPClient = [SocialHTTPClient new];
    });
    
    return _sharedSocialHTTPClient;
}

- (id)init
{
    self = [super initWithBaseURL:[NSURL URLWithString:@""]];
    
    if (self) {
        [[AFNetworkActivityIndicatorManager sharedManager] setEnabled:YES];
        [self registerHTTPOperationClass:[AFJSONRequestOperation class]];
    }
    
    return self;
}

#pragma mark - Registration

+ (void)registerViaFBWithSuccess:(SocialRegistrationCompletionBlock)successBlock
                         failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kFBUserEmailKey = @"email";

    successBlock = successBlock ?: ^(NSString *userEmail, NSString *socialID, SocialType idType){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    void(^facebookAuthCompletionBlock)(void) = ^{
        
        [FBRequestConnection startForMeWithCompletionHandler:^(FBRequestConnection *connection, id result, NSError *error) {
            //NSLog(@"result %@ error %@", result, error);
            
            if (!error) {
                successBlock(result[kFBUserEmailKey], result[kFBUserIDKey], FacebookType);
//                [[FBSession activeSession] closeAndClearTokenInformation];
            }
            else {
                failureBlock(NO, error);
            }
        }];
    };
    
    //force logout to provide log in with another user
    [SocialHTTPClient resetFBSession];
    
    [FBSession openActiveSessionWithReadPermissions:@[kFBUserEmailKey]
                                       allowLoginUI:YES
                                  completionHandler:^(FBSession *session, FBSessionState status, NSError *error) {
                                      
                                      if (status == FBSessionStateOpen) {
                                          facebookAuthCompletionBlock();
                                      }
                                      else if (error && failureBlock) {
                                            error = [NSError errorWithString:NSLocalizedString(@"Check facebook permissions and try again later", nil)];
                                            failureBlock(NO, error);
                                      }
                                  }];
}

#pragma mark - Login

+ (void)resetFBSession
{
    static NSString * const kFBLoginHandlerKey = @"loginHandler";
    // loginHandler - nil, otherwise completionHandler will be called on session close, which is not desired behavior
    [FBSession.activeSession setValue:nil forKey:kFBLoginHandlerKey];
    [FBSession.activeSession close];
    [FBSession.activeSession closeAndClearTokenInformation];
    [FBSession setActiveSession:nil];
}

#pragma mark - Posting

+ (void)postMessageToFB:(NSString *)updateMessage
  withAdditionalMessage:(NSString *)additionalMessage
                success:(MTHTTPCompletionBlock)successBlock
                failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kFBPostPictureKey = @"picture";
    static NSString * const kFBPostNameKey = @"name";
    static NSString * const kFBPostNameString = @"Mathematic";
    static NSString * const kFBPostUpdateMessageKey = @"caption";
    static NSString * const kFBPostLinkKey = @"link";
    static NSString * const kFBPostGraphPath = @"me/feed";
    static NSString * const kFBPostHTTPMethod = @"POST";
    static NSString * const kFBPostDescriptionKey = @"description";
    static NSString * const kFBPostDescriptionString = @" ";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};

    NSMutableDictionary *params = [NSMutableDictionary dictionaryWithObjectsAndKeys:
                                   kFBPostFlixaIconURL, kFBPostPictureKey,
                                   kFBPostNameString, kFBPostNameKey,
                                   updateMessage, kFBPostDescriptionKey,
                                   kFBPostDescriptionString, kFBPostUpdateMessageKey,
                                   kLocaMathURL, kFBPostLinkKey,
                                   nil];
    
    [FBSession openActiveSessionWithPublishPermissions:@[kFBPublishPermissionsString]
                                       defaultAudience:FBSessionDefaultAudienceEveryone
                                          allowLoginUI:YES
                                     completionHandler:^(FBSession *session, FBSessionState status, NSError *error) {
                                         
                                         if (status == FBSessionStateOpen) {
                                             [FBRequestConnection startWithGraphPath:kFBPostGraphPath
                                                                          parameters:params
                                                                          HTTPMethod:kFBPostHTTPMethod
                                                                   completionHandler:^(FBRequestConnection *connection, id result, NSError *error) {
                                                                       if (!error) {
                                                                           successBlock(YES, nil);
                                                                       } else {
                                                                           failureBlock(NO, error);
                                                                       }
                                                                   }];
                                             
                                             //need this check to avoid redunant errors poping up,
                                             //because method in invoked few times: on session open/close
                                         } else if (error) {
                                             failureBlock(NO, error);
                                         }
                                     }];
}

@end
