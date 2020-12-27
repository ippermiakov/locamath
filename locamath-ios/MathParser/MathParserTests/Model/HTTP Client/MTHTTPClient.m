//
//  MTHTTPClient.m
//  Mathematic
//
//  Created by alexbutenko on 3/29/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTHTTPClient.h"
#import "ChildManager.h"
#import "SSKeychain.h"
#import "NSError+String.h"
#import "Parent.h"
#import "DataUtils.h"

#ifdef DISTRIBUTION
    static NSString * const base_url = @"http://math.loca-app.com/request?";
#else
    static NSString * const base_url = @"http://math-dev.loca-app.com/request?";
#endif

static NSString * const kMTAccessTokenKey = @"authid";
static NSString * const kMTService = @"Mathematic";
static NSString * const kMTAccount = @"MathematicAccount";
static NSString * const kMTAPINameKey = @"ac";
NSString * const kUserPasswordKey = @"password";
NSString * const kUserEmailKey = @"email";
static NSString * const kLevelData = @"data";
static NSString * const kLocale = @"locale";
static NSString * const kDefaultAccessToken = @"kDefaltAccessToken";

NSString * const kSocialIDKey = @"social_id";

NSInteger const kParentAlreadyExistsErrorCode= 1003;
NSUInteger const kAuthorizationFailureCode = 1021;
NSUInteger const kChildSelectionFailureCode = 1011;
NSUInteger const kNoChildFailureCode = 1010;

NSString * const kAuthorizationFailureNotification = @"AuthorizationFailureNotification";
NSString * const kUserLoggedInNotification = @"UserLoggedInNotification";
NSString * const kChildSelectionFailureNotification = @"ChildSelectionFailureNotification";
NSString * const kChildSelectionSuccessNotification = @"ChildSelectionSuccessNotification";
NSString * const kParentExistsFailureNotification = @"ParentAlreadyExistsNotification";

typedef NS_ENUM(NSUInteger, RequestMethod)
{
	GET,
	POST
};

typedef NS_ENUM(NSUInteger, ExpressionType) {
    ExpPhoneNumber = 1,
    ExpName,
    ExpEmail,
    ExpPassword,
};

@interface MTHTTPClient ()

@property (copy, nonatomic) MTHTTPSuccessResponseExecutionBlock addAccessTokenBlock;
@property (copy, nonatomic) MTHTTPSuccessResponseExecutionBlock removeAccessTokenBlock;

@property (readwrite, unsafe_unretained, nonatomic) BOOL isChildActivated;

@property (strong, nonatomic) AFJSONRequestOperation *currentOperation;

@property (strong, nonatomic) NSArray *censoredWords;

@end

@implementation MTHTTPClient

+ (MTHTTPClient *)sharedMTHTTPClient
{    
    static dispatch_once_t pred;
    static MTHTTPClient *_sharedMTHTTPClient = nil;
    
    
    dispatch_once(&pred, ^{
        _sharedMTHTTPClient = [MTHTTPClient new];
        
        _sharedMTHTTPClient.addAccessTokenBlock = ^(NSDictionary *successResponseData) {
            // do necessary save AT here
            [MTHTTPClient saveMTAccessToken:successResponseData[kMTAccessTokenKey]];
            
            if (successResponseData[kMTAccessTokenKey]) {
                [[NSNotificationCenter defaultCenter] postNotificationName:kUserLoggedInNotification object:nil];
            }
        };
        _sharedMTHTTPClient.removeAccessTokenBlock = ^(NSDictionary *successResponseData) {
            [MTHTTPClient deleteMTAccessToken];
        };
    });
    
    return _sharedMTHTTPClient;
}

- (id)init
{
    self = [super initWithBaseURL:[NSURL URLWithString:base_url]];
    
    if (self) {
        [[AFNetworkActivityIndicatorManager sharedManager] setEnabled:YES];
        [self registerHTTPOperationClass:[AFJSONRequestOperation class]];
    }
    
    return self;
}

#pragma mark - Setter & Getters

- (BOOL)isReachable
{
    return self.networkReachabilityStatus != AFNetworkReachabilityStatusNotReachable;
}

- (BOOL)isParentAuthentificated
{
//#ifndef DEBUG
    //if AT is empty string or there is no parent yet => return AUTH error without passing request to server
    return !(([MTHTTPClient MTAccessTokenOrEmptyString] &&
             0 == [[MTHTTPClient MTAccessTokenOrEmptyString] length]) || ![DataUtils currentParent]);
//#else
//    if ([self isReachable]) {
//        return !(([MTHTTPClient MTAccessTokenOrEmptyString] &&
//                  0 == [[MTHTTPClient MTAccessTokenOrEmptyString] length]) || ![DataUtils currentParent]);
//    } else {
//        //to allow go further for debug
//        return YES;
//    }
//#endif
}

- (BOOL)canSyncChildData
{
//    NSLog(@"isReachable: %@", [self isReachable] ? @"YES":@"NO");
//    NSLog(@"isParentAuthentificated: %@", [self isParentAuthentificated] ? @"YES":@"NO");
//    NSLog(@"isChildActivated: %@", [self isChildActivated] ? @"YES":@"NO");
    
    return [self isReachable] &&
           [self isParentAuthentificated] &&
           [self isChildActivated] &&
            [[ChildManager sharedInstance].currentChild.isDataLoaded boolValue];
}

- (void)addDefaultAccessToken
{
    self.addAccessTokenBlock(@{kMTAccessTokenKey: kDefaultAccessToken});
}

- (NSArray *)censoredWords
{
    if (!_censoredWords) {
        NSString *fullPath = [[NSBundle bundleForClass:[self class]] pathForResource:@"censored_words"
                                                                              ofType:@""];
        
        NSError *error = nil;
        NSString *censoredWordsString = [NSString stringWithContentsOfFile:fullPath
                                                                  encoding:NSUTF8StringEncoding
                                                                     error:&error];
        
        _censoredWords = [censoredWordsString componentsSeparatedByCharactersInSet:[NSCharacterSet newlineCharacterSet]];

        _censoredWords = [_censoredWords reject:^BOOL(NSString *str) {
            return 0 == [str length];
        }];        
    }
    
    return _censoredWords;
}

#pragma mark - SSKeychain

+ (BOOL)saveMTAccessToken:(NSString *)MTAccessToken
{
    return [SSKeychain setPassword:MTAccessToken
                        forService:kMTService
                           account:kMTAccount];
}

+ (NSString *)MTAccessToken
{
    return [SSKeychain passwordForService:kMTService account:kMTAccount];
}

+ (NSString *)MTAccessTokenOrEmptyString
{
    return [MTHTTPClient MTAccessToken] ?: @"";
}

+ (BOOL)deleteMTAccessToken
{
    return [SSKeychain deletePasswordForService:kMTService account:kMTAccount];
}

#pragma mark - Login&Register

- (void)loginUserWithEmail:(NSString *)email
                  password:(NSString *)pass
     shouldSaveAccessToken:(BOOL)shouldSaveAccessToken
                   success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                   failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kUserLoginWithEmailAPIName = @"login";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validateEmail:email withError:&error];
    
    if (!error) [MTHTTPClient validatePassword:pass withError:&error];
    
    if (!error) {
        
        [self performRequest:GET
                  withParams:@{kMTAPINameKey: kUserLoginWithEmailAPIName,
                               kUserEmailKey: email,
                               kUserPasswordKey: pass}
       successExecutionBlock:^(NSDictionary *dictionary) {
           self.addAccessTokenBlock(dictionary); //dictinary with AT
           successBlock(dictionary);
       }
       andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
       }];
        
    } else {
        failureBlock(NO, error);
    }
}

- (void)registerUserWithEmail:(NSString *)email
                     password:(NSString *)pass
                      success:(MTHTTPCompletionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kRegisterUserWithEmailAPIName = @"register";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validateEmail:email withError:&error];
    
    if (!error) [MTHTTPClient validatePassword:pass withError:&error];
    
    NSString *currentLocale = [[NSLocale preferredLanguages] objectAtIndex:0];
    
    if (!error) {
        [self performRequest:GET
                  withParams:[NSMutableDictionary dictionaryWithObjectsAndKeys:
                              kRegisterUserWithEmailAPIName, kMTAPINameKey,
                              email, kUserEmailKey,
                              pass, kUserPasswordKey,
                              currentLocale, kLocale, nil]
       successExecutionBlock:nil
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished) {
                       successBlock(YES, nil);
                   } else {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)loginUserWithSocialID:(NSString *)sociadID
                      success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kLoginSocialUserAPIName = @"logindevice";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    //don't validate email since it's returned by SocialHTTPClient and valid for sure
    NSMutableDictionary *params = [NSMutableDictionary new];
    params = [NSMutableDictionary dictionaryWithObjectsAndKeys:
              kLoginSocialUserAPIName, kMTAPINameKey,
              sociadID, kSocialIDKey, nil];
    
    if (!error) {
        [self performRequest:GET
                  withParams:params
       successExecutionBlock:^(NSDictionary *dictionary) {
           self.addAccessTokenBlock(dictionary); //dictionary with AT
           successBlock(dictionary); //dictionary with flag
       }
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished == NO || error != nil) {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)registerWithSocialID:(NSString *)socialID
                       email:(NSString *)email
                     success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                     failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kRegisterSocialUserAPIName = @"registerdevice";
//    static NSString *const kAutoLoginParam = @"autologinifexists";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    NSString *currentLocale = [[NSLocale preferredLanguages] objectAtIndex:0];
    //don't validate email since it's returned by SocialHTTPClient and valid for sure
    NSMutableDictionary *params = [NSMutableDictionary new];
    params =[NSMutableDictionary dictionaryWithObjectsAndKeys:
             kRegisterSocialUserAPIName, kMTAPINameKey,
             email, kUserEmailKey,
             socialID, kSocialIDKey,
             currentLocale, kLocale, nil];
    
//    [params setValue:@(isAutoLoginEnabled) forKey:kAutoLoginParam];
    
    if (!error) {
        [self performRequest:GET
                  withParams:params
       successExecutionBlock:^(NSDictionary *dictionary) {
           self.addAccessTokenBlock(dictionary); //dictionary with AT
           successBlock(dictionary); //dictionary with flag
       }
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished == NO || error != nil) {
                       failureBlock(NO, error);
                   }               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)deleteAccountWithSuccess:(MTHTTPCompletionBlock)successBlock
                         failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kDeleteParentAPIName = @"deletesession";

    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    [self performRequest:GET
              withParams:@{kMTAPINameKey: kDeleteParentAPIName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString]}
   successExecutionBlock:nil
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
           }];
}

- (void)deleteAccountWithEmail:(NSString *)email
                       success:(MTHTTPCompletionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kDeleteParentAPIName = @"deleteaccount";
    static NSString * const kParentEmailKey = @"email";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validateEmail:email withError:&error];

    if (!error) {
        [self performRequest:GET
                  withParams:@{kMTAPINameKey: kDeleteParentAPIName,
                               kParentEmailKey: email}
       successExecutionBlock:nil
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished) {
                       successBlock(YES, nil);
                   } else {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)logout
{
    self.isChildActivated = NO;
    [MTHTTPClient deleteMTAccessToken];
}

- (void)resetPasswordWithEmail:(NSString *)email
                       success:(MTHTTPCompletionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kResetPasswordAPIName = @"resetpassword";
    static NSString * const kParentEmailKey = @"email";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validateEmail:email withError:&error];

    if (!error) {
        [self performRequest:GET
                  withParams:@{kMTAPINameKey: kResetPasswordAPIName, kParentEmailKey: email}
       successExecutionBlock:nil
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished) {
                       successBlock(YES, nil);
                   } else {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)changePasswordWithEmail:(NSString *)email
                currentPassword:(NSString *)currentPassword
                    newPassword:(NSString *)newPassword
                        success:(MTHTTPCompletionBlock)successBlock
                        failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kChangePasswordAPIName = @"changepassword";
    static NSString * const kOldPasswordKey = @"oldpassword";
    static NSString * const kNewPasswordKey = @"newpassword";
    static NSString * const kEmailKey = @"email";

    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validatePassword:currentPassword withError:&error];

    if (!error) [MTHTTPClient validatePassword:newPassword withError:&error];
    if (!error) [MTHTTPClient validateEmail:email withError:&error];
        
    if (!error) {
        [self performRequest:GET
                  withParams:@{kMTAPINameKey: kChangePasswordAPIName,
                               kOldPasswordKey: currentPassword,
                               kNewPasswordKey: newPassword,
                               kEmailKey: email}
       successExecutionBlock:nil
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished) {
                       successBlock(YES, nil);
                   } else {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

#pragma mark - Child

- (void)addChildWithName:(NSString *)name
                 success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                 failure:(MTHTTPCompletionBlock)failureBlock;
{
    static NSString * const kAddChildName = @"addchild";
    static NSString * const kChildName = @"name";
    
    successBlock = successBlock ?: ^(NSDictionary *responseDictionary){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSError *error = nil;
    
    [MTHTTPClient validateNickname:name withError:&error];
    
    if (!error) {
        [self performRequest:GET
                  withParams:[NSMutableDictionary dictionaryWithObjectsAndKeys:
                              kAddChildName, kMTAPINameKey,
                              [MTHTTPClient MTAccessToken], kMTAccessTokenKey,
                              name, kChildName, nil]
       successExecutionBlock:successBlock
               andCompletion:^(BOOL finished, NSError *error) {
                   if (finished == NO || error != nil) {
                       failureBlock(NO, error);
                   }
               }];
    } else {
        failureBlock(NO, error);
    }
}

- (void)deleteChildWithName:(NSString *)name
                    success:(MTHTTPCompletionBlock)successBlock
                    failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kDeleteChildName = @"deletechild";
    static NSString * const kChildName = @"name";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    
    [self performRequest:GET
              withParams:@{kMTAPINameKey: kDeleteChildName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
                           kChildName: name}
   successExecutionBlock:nil
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
           }];
}

- (void)getChildsSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                 failure:(MTHTTPCompletionBlock)failureBlock;
{
    static NSString * const kGetChildDetailsAPIName = @"getchilds";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
        
    [self performRequest:GET
              withParams:@{kMTAPINameKey: kGetChildDetailsAPIName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString]}
   successExecutionBlock:successBlock
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
    }];
}

- (void)setActiveChildWithID:(NSString *)identifier
                       success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                       failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kSetActiveChildAPIName = @"setactivechild";
    static NSString * const kChildID = @"id";
    
    successBlock = successBlock ?: ^(NSDictionary *dictionary){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
        
    [self performRequest:POST
              withParams:@{kMTAPINameKey: kSetActiveChildAPIName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
                           kChildID: identifier}
   successExecutionBlock:^(NSDictionary *successResponseData) {
       self.isChildActivated = YES;
       self.isRequestsIgnored = NO;

       [[NSNotificationCenter defaultCenter] postNotificationName:kChildSelectionSuccessNotification
                                                           object:nil];
       successBlock(successResponseData);
   }
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
           }];
}

- (void)setChildDetails:(NSDictionary *)data
                success:(MTHTTPCompletionBlock)successBlock
                failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kEditChildDetailsAPIName = @"setchilddetails";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
	NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithDictionary:data];
    [dict setValue:kEditChildDetailsAPIName forKey:kMTAPINameKey];
	[dict setValue:[MTHTTPClient MTAccessTokenOrEmptyString] forKey:kMTAccessTokenKey];
    
    NSError *error = nil;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dict[@"sendStatisticsAccounts"]
                                                       options:NSJSONWritingPrettyPrinted // Pass 0 if you don't care about the readability of the generated string
                                                         error:&error];
    
    NSString *jsonString = nil;
    
    if (jsonData) {
        jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
    }
    
    if (jsonString) {
        [dict setValue:jsonString forKey:@"sendStatisticsAccounts"];
    }
    
    [self performRequest:POST
			  withParams:dict
   successExecutionBlock:nil
		   andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
           }];
}

#pragma mark - Update Levels

- (void)setLevelsData:(NSArray *)data
             progress:(MTHTTPProgressBlock)progressBlock
              success:(MTHTTPCompletionBlock)successBlock
              failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kUpdateLevels = @"updatelevels";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};
    
//    NSLog(@"data sent for red path: %@", data[0]);

//    NSArray *levels = [data[0] objectForKey:@"levels"];
//    
//    NSDictionary *firstLevelDictionary = [levels match:^BOOL(NSDictionary *dictionary) {
//        return [dictionary[@"identifierString"] isEqualToString:@"1-A-1"];
//    }];
//
//    NSLog(@"data sent for Red path Level 1: %@", firstLevelDictionary);
    
    
    NSError *error = nil;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:data
                                                       options:NSJSONWritingPrettyPrinted // Pass 0 if you don't care about the readability of the generated string
                                                         error:&error];
    
    NSString *jsonString = nil;
    
    if (jsonData) {
        jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
    }
    
    [self performRequest:POST
			  withParams:@{kMTAPINameKey: kUpdateLevels,
                        kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
                        kLevelData: jsonString}
           progressBlock:progressBlock
   successExecutionBlock:nil
		   andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
    }];
}

- (void)getLevelsWithSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                    progress:(MTHTTPProgressBlock)progressBlock
                     failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kGetLevels = @"getlevels";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};

    [self performRequest:GET
              withParams:@{kMTAPINameKey: kGetLevels,
       kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString]}
           progressBlock:progressBlock
   successExecutionBlock:successBlock
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
    }];
}

#pragma mark - Update Olympiad Levels

- (void)setOlympiadLevelsData:(NSArray *)data
                     progress:(MTHTTPProgressBlock)progressBlock
                      success:(MTHTTPCompletionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kUpdateOlympiadLevels = @"updateolymplevels";
    
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};

    NSError *error = nil;
    NSData *jsonData = [NSJSONSerialization dataWithJSONObject:data
                                                       options:NSJSONWritingPrettyPrinted // Pass 0 if you don't care about the readability of the generated string
                                                         error:&error];
    
    NSString *jsonString = nil;
    
    if (jsonData) {
        jsonString = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
    }
    
    [self performRequest:POST
			  withParams:@{kMTAPINameKey: kUpdateOlympiadLevels,
       kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
              kLevelData: jsonString}
           progressBlock:progressBlock
   successExecutionBlock:nil
		   andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
    }];
}

- (void)getOlympiadLevelsWithSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                            progress:(MTHTTPProgressBlock)progressBlock
                             failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kGetOlympiadLevels = @"getolymplevels";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};

    [self performRequest:GET
              withParams:@{kMTAPINameKey: kGetOlympiadLevels,
       kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString]}
           progressBlock:progressBlock
   successExecutionBlock:successBlock
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
        }];
}

NSString * const kCity = @"city";
NSString * const kCountry = @"country";
NSString * const kWorld = @"world";

NSString * const kName    = @"name";
NSString * const kPoints  = @"points";
NSString * const kUndefined = @"Undefined";
NSString * const kRate = @"rate";
NSString * const kChildID = @"id";

- (void)getChildsRateSuccess:(MTHTTPSuccessResponseExecutionBlock)successBlock
                     failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kGetChildeRateAPIName = @"getrate";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    [self performRequest:GET
              withParams:@{kMTAPINameKey: kGetChildeRateAPIName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString]}
   successExecutionBlock:successBlock
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
           }];
}

#pragma mark - Tasks/Helps Objectives Data

- (void)getGameDataWithObject:(NSString *)objectString
                  levelNumber:(NSNumber *)levelNumber
                   isOlympiad:(NSNumber *)isOlympiad
                      success:(MTHTTPSuccessResponseExecutionBlock)successBlock
                      failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kGetGameDataAPIName = @"retrievejson";
    static NSString * const kObjectKey = @"object";
    static NSString * const kLevelKey = @"level";
    static NSString * const kOlympiadKey = @"olympiad";
    static NSString * const kLocaleKey = @"locale";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    /*
     [object] => [level | task | scheme | help]
     * [level] => [1 | 2 | 3 | 4]
     [olympiad] => [0 | 1]
     [locale] => [en | ru]
    */
    
    NSString *currentLocale = [[NSLocale preferredLanguages] objectAtIndex:0];
    
    //get en by default
    if (![[[NSBundle mainBundle] localizations] containsObject:currentLocale]) {
        currentLocale = @"en";
    }
    
    [self performRequest:GET
              withParams:@{kMTAPINameKey: kGetGameDataAPIName,
                           kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
                           kObjectKey: objectString, kLevelKey: [levelNumber stringValue],
                           kOlympiadKey: [isOlympiad stringValue], kLocaleKey: currentLocale}
   successExecutionBlock:^(NSDictionary *successResponseData) {
       successBlock(successResponseData[@"json"]);
   }
           andCompletion:^(BOOL finished, NSError *error) {
               if (finished == NO || error != nil) {
                   failureBlock(NO, error);
               }
           }];
}

#pragma mark - Parent

- (void)parentUpdateLocationWithSuccess:(MTHTTPCompletionBlock)successBlock
                                failure:(MTHTTPCompletionBlock)failureBlock
{
    static NSString * const kUpdateLocation = @"updatelocation";
    static NSString * const kParentLongitude = @"longitude";
    static NSString * const kParentLatitude = @"latitude";
    
    Parent *currentParent = [DataUtils currentParent];
    successBlock = successBlock ?: ^(BOOL finished, NSError *error){};
    failureBlock = failureBlock ?: ^(BOOL finished, NSError *error){};
    
    NSMutableDictionary *param = [NSMutableDictionary new];
    param = [@{kMTAPINameKey: kUpdateLocation,
              kMTAccessTokenKey: [MTHTTPClient MTAccessTokenOrEmptyString],
               kParentLongitude: currentParent.longitude ?: @0,
               kParentLatitude: currentParent.latitude ?: @0} mutableCopy];
    
    [self performRequest:POST
			  withParams:param
   successExecutionBlock:nil
		   andCompletion:^(BOOL finished, NSError *error) {
               if (finished) {
                   successBlock(YES, nil);
               } else {
                   failureBlock(NO, error);
               }
           }];}


#pragma mark - Private

- (void)performRequest:(RequestMethod)method
            withParams:(NSDictionary *)params
 successExecutionBlock:(MTHTTPSuccessResponseExecutionBlock)successBlock
         andCompletion:(MTHTTPCompletionBlock)completionBlock
{
        [self performRequest:method
              withParams:params
           progressBlock:nil
   successExecutionBlock:successBlock
           andCompletion:completionBlock];
}

- (void)performRequest:(RequestMethod)method
            withParams:(NSDictionary *)params
         progressBlock:(MTHTTPProgressBlock)progressBlock
 successExecutionBlock:(MTHTTPSuccessResponseExecutionBlock)successBlock
         andCompletion:(MTHTTPCompletionBlock)completionBlock
{
    if (self.isRequestsIgnored) {
        NSLog(@"!!! ignored: %@", params[kMTAPINameKey]);
        completionBlock(NO, nil);
        return;
    }
    
    static NSString * const kGETRequestString = @"GET";
    static NSString * const kPOSTRequestString = @"POST";
    static NSString * const kServerAPIPath = @"api";
    
    successBlock = successBlock ?: ^(NSDictionary *successResponseData){};
    completionBlock = completionBlock ?: ^(BOOL finished, NSError *error){};
    progressBlock = progressBlock ?: ^(CGFloat progress){};
    
    //if AT is empty string => return AUTH error without passing request to server
    if (params[kMTAccessTokenKey] && 0 == [params[kMTAccessTokenKey] length]) {
       // NSLog(@"is current Child: %@ default : %@", [ChildManager sharedInstance].currentChild.name, [DataUtils isCurrentChildDefault] ? @"YES" : @"NO");
        if (![DataUtils isCurrentChildDefault]) {
            [[NSNotificationCenter defaultCenter] postNotificationName:kAuthorizationFailureNotification object:nil];
            completionBlock(NO, [NSError errorWithString:NSLocalizedString(@"Authorization Error", nil)]);
            return;
        } else {
            completionBlock(NO, nil);
            return;
        }
    }
    
    NSURLRequest *request = [self requestWithMethod:((method == GET) ? kGETRequestString : kPOSTRequestString)
                                               path:kServerAPIPath
                                         parameters:params];
    
    //NSLog(@"Request %@", request);
    
    self.currentOperation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
    // NSLog(@"\n\nResponse \n-------\ncode: %i JSON: %@ \n\nFOR %@", [response statusCode], JSON ,request);
        
        NSError *error = nil;
        
        if ([self validateResponseJSON:JSON withError:&error]) {
            if (successBlock) successBlock(JSON);
        }
        
        if (completionBlock) {
            // !error == success, otherwise failure
            completionBlock(!error, error);
        }
        
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        
        NSLog(@"Request Failed with Error: %@, %@", [error localizedDescription], error.userInfo);
        
        //replace all back-end error to hide details from users
        if ([self isReachable]) {
            error = [NSError errorWithString:NSLocalizedString(@"Server Error", @"back-end response")];
        }
        
        if (completionBlock) {
            completionBlock(NO, error);
        }
    }];
    
    [self.currentOperation setUploadProgressBlock:^(NSUInteger bytesWritten, long long totalBytesWritten, long long totalBytesExpectedToWrite) {
        CGFloat progress = (double)totalBytesWritten/(double)totalBytesExpectedToWrite;
//        NSLog(@"URL: %@ progress: %f bytesWritten: %i totalBytesWritten: %lld totalBytesExpectedToWrite: %lld", [[request URL] absoluteString], progress, bytesWritten, totalBytesWritten, totalBytesExpectedToWrite);
        progressBlock(progress);
    }];
    
    [self.currentOperation setDownloadProgressBlock:^(NSUInteger bytesRead, long long totalBytesRead, long long totalBytesExpectedToRead) {
        CGFloat progress = (double)totalBytesRead/(double)totalBytesExpectedToRead;
//        NSLog(@"URL: %@ progress: %f bytesRead: %i totalBytesRead: %lld totalBytesExpectedToRead: %lld", [[request URL] absoluteString], progress, bytesRead, totalBytesRead, totalBytesExpectedToRead);
        progressBlock(progress);
    }];
    
    [self enqueueHTTPRequestOperation:self.currentOperation];
}

- (void)cancelCurrentRequest
{
    [self.currentOperation cancel];
}

#pragma mark - Validation

- (BOOL)validateResponseJSON:(id)JSON withError:(NSError * __autoreleasing *)error
{
    static NSString * const kResponseStatusKey = @"status";
    static NSString * const kResponseStatusOKString = @"ok";
    static NSString * const kResponseErrorKey = @"errors";
    
    BOOL isValid = [[JSON valueForKey:kResponseStatusKey] isEqualToString:kResponseStatusOKString];
    
    if (!isValid) {
        
        if ([JSON valueForKey:kResponseErrorKey]) {
            //errors = { 1002 = "Incorrect email format";};
            NSString *errorString = NSLocalizedString([[[JSON valueForKey:kResponseErrorKey] allObjects] componentsJoinedByString:@".\n"], nil);
            
            NSUInteger errorCode = [[[[JSON valueForKey:kResponseErrorKey] allKeys] lastObject] integerValue];

            //avoid displaying alert message to user (redundant)
            if (errorCode == kChildSelectionFailureCode || errorCode == kNoChildFailureCode) {
                *error = [NSError errorWithString:@"" andCode:errorCode];
            } else {
                *error = [NSError errorWithString:errorString andCode:errorCode];
            }
            
            if (errorCode == kAuthorizationFailureCode && ![DataUtils isCurrentChildDefault]) {
                [[NSNotificationCenter defaultCenter] postNotificationName:kAuthorizationFailureNotification object:nil];
            }
        
            //delete current child if failed to set active
            if (errorCode == kNoChildFailureCode && self.isRequestsIgnored) {
                [[ChildManager sharedInstance].currentChild deleteEntity];
                [[ChildManager sharedInstance] logoutCurrentChild];
                self.isRequestsIgnored = NO;
            }
            
            if ((errorCode == kChildSelectionFailureCode || errorCode == kNoChildFailureCode)/* && !self.isRequestsIgnored*/) {
                self.isChildActivated = NO;
                
                [[NSNotificationCenter defaultCenter] postNotificationName:kChildSelectionFailureNotification object:nil];
            }
            
        } else {
            *error = [NSError errorWithString:NSLocalizedString(@"Server Error", @"back-end response")];
        }
    }

    return isValid;
}

#pragma mark - Input validation methods

+ (BOOL)isExpressionValid:(NSString *)expression expressionType:(ExpressionType)expType
{
    static NSString * const kPhoneNumberRegularExpression = @"^[0-9+]{9,20}$";
    // \w is equivalent to [0-9a-zA-Z_], depending on the specific machine locale this might (not) work with accented / unicode characters, either way it will always match digits, and it shouldn't.
    // http://stackoverflow.com/questions/5963228/regex-for-names-with-special-characters-unicode
    static NSString * const kUserNameRegularExpression = @"^[A-Za-zА-Яа-я]+([\\wА-Яа-я_\\s.-]){1,30}$";
    static NSString * const kEmailRegularExpression = @"^([0-9a-zA-Z]+[-._+&amp;])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,4}$";
    static NSString * const kPasswordRegularExpression = @"^[0-9A-Za-z\\wА-Яа-я_\\s.-]{6,15}$";
    
    if (!expression || ![expression length]) {
        return NO;
    }
    
    NSString *pattern = nil;
    
    switch (expType) {
        case ExpPhoneNumber:
            pattern = kPhoneNumberRegularExpression;
            break;
        case ExpName:
            pattern = kUserNameRegularExpression;
            break;
        case ExpEmail:
            pattern = kEmailRegularExpression;
            break;
        case ExpPassword:
            pattern = kPasswordRegularExpression;
            break;
        default:
            return NO;
    }
    
	NSError *err = nil;
	NSRegularExpression *regex = [[NSRegularExpression alloc] initWithPattern:pattern options:0 error:&err];
	NSRange range = NSMakeRange(0, [expression length]);
	NSArray *matches = [regex matchesInString:expression
									  options:0
										range:range];
    
    if (err) {
        NSLog(@"validation error %@", [err userInfo]);
        return NO;
    }
    
	return (BOOL)[matches count];
}

+ (BOOL)validateNickname:(NSString *)nickname withError:(NSError * __autoreleasing *)error
{
    if (![MTHTTPClient isExpressionValid:nickname expressionType:ExpName]) {
        *error = [NSError errorWithString:NSLocalizedString(@"Invalid nickname. You can use only Latin/Cyrillic symbols, whitespaces, digits and symbols '.-_'. Maximum is 30 symbols", nil)];
        return NO;
    }
    
    if (![MTHTTPClient validateForCensoreNickname:nickname]) {
        *error = [NSError errorWithString:NSLocalizedString(@"Censored nickname error", nil)];
        return NO;
    }
    
    return YES;
}

+ (BOOL)validateForCensoreNickname:(NSString *)nickname
{
    return ![[[MTHTTPClient sharedMTHTTPClient] censoredWords] any:^BOOL(NSString *censoredWord) {
        return NSOrderedSame == [nickname compare:censoredWord options:NSCaseInsensitiveSearch];
    }];
}

+ (BOOL)validateEmail:(NSString *)email withError:(NSError * __autoreleasing *)error
{
    if (![MTHTTPClient isExpressionValid:email expressionType:ExpEmail]) {
        *error = [NSError errorWithString:NSLocalizedString(@"Invalid email", nil)];
        return NO;
    }
    
    return YES;
}

+ (BOOL)validatePassword:(NSString *)password withError:(NSError * __autoreleasing *)error
{
    if (![MTHTTPClient isExpressionValid:password expressionType:ExpPassword]) {
        *error = [NSError errorWithString:NSLocalizedString(@"Invalid password. Input from 6 to 15 symbols", nil)];
        return NO;
    }
    
    return YES;
}

@end
