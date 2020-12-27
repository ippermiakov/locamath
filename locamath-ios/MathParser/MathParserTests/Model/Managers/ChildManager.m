//
//  ChildManager.m
//  Mathematic
//
//  Created by Developer on 25.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChildManager.h"
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
#import "SynchronizationManager.h"
#import "MBProgressHUD+Mathematic.h"
//#import "AppDelegate.h"
#import "Parent.h"
#import "AccountFB.h"

static NSString *const kSendStatisticAccountsKey = @"sendStatisticsAccounts";

@interface ChildManager ()

@property (strong, nonatomic) Child *currentChild;

@end

@implementation ChildManager

- (id)init
{
    self = [super init];
    
    if (self != nil) {
        [self updateCurrentChildReference];
//        [[MTHTTPClient sharedMTHTTPClient] deleteAccountWithEmail:@"sanyachmal@rambler.ru"
//                                                          success:^(BOOL finished, NSError *error) {
//                                                              NSLog(@"parent with email: %@ has been deleted from server", @"sanyachmal@rambler.ru");
//                                                          } failure:^(BOOL finished, NSError *error) {                                                                      NSLog(@"failed to delete parent with email: %@", @"sanyachmal@rambler.ru");
//                                                          }];

        
        //clear unlinked parent. it could occur due to unhandled app termination
        if (0 == [[DataUtils currentParent].childs count]) {
            
            if ([[DataUtils currentParent].email length] > 0) {
                NSString *email = [DataUtils currentParent].email;
                [[MTHTTPClient sharedMTHTTPClient] deleteAccountWithEmail:email
                                                                  success:^(BOOL finished, NSError *error) {
                                                                      NSLog(@"parent with email: %@ has been deleted from server", email);
                                                                  } failure:^(BOOL finished, NSError *error) {                                                                      NSLog(@"failed to delete parent with email: %@", email);
                                                                  }];
            }
            
            [Parent truncateAll];
            [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        }
        
//        AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
//        
//        [[NSNotificationCenter defaultCenter] addObserverForName:UIWindowDidBecomeVisibleNotification
//                                                          object:appDelegate.window
//                                                           queue:[NSOperationQueue mainQueue]
//                                                      usingBlock:^(NSNotification *note) {
//                                                          if (self.currentChild &&
//                                                              ![self.currentChild.isDataLoaded boolValue] &&
//                                                              [self.currentChild.identifier integerValue] != kNewUnsynchronizedChildID) {
//                                                              
//                                                              //block UI until network will be recognized
//                                                              if (![[MTHTTPClient sharedMTHTTPClient] isReachable]) {
//                                                                  [MBProgressHUD showHUDForWindow];
//                                                              }
//                                                          }
//                                                      }];
        
        //update current child on network status change
        [[NSNotificationCenter defaultCenter] addObserverForName:AFNetworkingReachabilityDidChangeNotification
                                                          object:nil
                                                           queue:[NSOperationQueue mainQueue]
                                                      usingBlock:^(NSNotification *note) {
                                                          if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
                                                              [self reloadChildDataIfNeededWithSuccess:nil
                                                                                                         failure:nil];
                                                          } else if (![[MTHTTPClient sharedMTHTTPClient] isReachable]) {
                                                              //HACK: when internet appears first time we get no internet notification and just then after delay we get that internet connection is available
                                                              double delayInSeconds = 2;
                                                              dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
                                                              dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                                                                  [self reloadChildDataIfNeededWithSuccess:nil
                                                                                                             failure:nil];
                                                              });
                                                          }
                                                      }];
    }
    
    return self;
}

+ (ChildManager *)sharedInstance
{
    static dispatch_once_t pred;
    static ChildManager *sharedInstance = nil;
    
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    
    return sharedInstance;
}

- (void)reloadChildDataIfNeededWithSuccess:(ChildManagerSuccessBlock)successBlock
                                   failure:(ChildManagerFailureBlock)failureBlock
{
    if (!successBlock) {
        successBlock = ^{};
    }
    
    failureBlock = failureBlock ?: ^(NSError *error){};
    
    NSLog(@"isReachable: %@", [[MTHTTPClient sharedMTHTTPClient] isReachable] ? @"YES":@"NO");
    
    [self createPostSendTypesIfNeeded];
    
    if (!self.isReloadingChildData) {
        [MBProgressHUD showHUDForWindow];
        
//        __weak ChildManager *weakSelf = self;
        self.isReloadingChildData = YES;

        self.currentChild.isSyncNeeded = @YES;
        
//        if ([[MTHTTPClient sharedMTHTTPClient] isReachable] && self.currentChild && ![DataUtils isCurrentChildDefault]) {
//            [self setActiveChildWithID:[self.currentChild.identifier stringValue]
//                                 success:^(NSDictionary *dictionary) {
//                                     weakSelf.currentChild.modificationJSONTimeInterval = dictionary[@"lastjsonmodtime"];
//                                     
//                                     NSDictionary *childDetails = [dictionary[@"childs"] lastObject];
//                                     [weakSelf updateDetailsForChild:weakSelf.currentChild withDictionary:childDetails];
////                                     NSLog(@"updated currentChild: %@", weakSelf.currentChild);
//                                     
//                                     NSLog(@"previous JSON data load timestamp: %@ current JSON data load timestamp: %@",
//                                           weakSelf.currentChild.previousModificationJSONTimeInterval, weakSelf.currentChild.modificationJSONTimeInterval);
//                                     
//                                     [[MTFileParser sharedInstance] parseFilesToCoreDataForChild:weakSelf.currentChild
//                                                                        success:^{
//                                                                            weakSelf.isReloadingChildData = NO;
//                                                                            [MBProgressHUD hideHUDForWindow];
//                                                                            [ChildManager sharedInstance].currentChild.isDataLoaded = @YES;
//                                                                            
//                                                                            [[NSNotificationCenter defaultCenter] postNotificationName:kSynchronizationFinishedNotification object:nil];
//                                                                            successBlock();
//                                                                        }
//                                                                        failure:^(NSError *error) {
//                                                                            [MBProgressHUD hideHUDForWindow];
//                                                                            weakSelf.isReloadingChildData = NO;
//                                                                            failureBlock(error);
//                                                                        }];
//                                 } failure:^(NSError *error) {
//                                     [MBProgressHUD hideHUDForWindow];
//                                     weakSelf.isReloadingChildData = NO;
//                                     failureBlock(error);
//                                 }];
//            
//            [MTHTTPClient sharedMTHTTPClient].isRequestsIgnored = YES;
//
//        } else if (self.currentChild) {
            [[MTFileParser sharedInstance] parseLocalFilesToCoreDataForChild:self.currentChild
                                                                  completion:^{
                [MBProgressHUD hideHUDForWindow];
                self.isReloadingChildData = NO;
                [[NSNotificationCenter defaultCenter] postNotificationName:kSynchronizationFinishedNotification object:nil];

                successBlock();
            }];
       /* } else {
            [MBProgressHUD hideHUDForWindow];
            self.isReloadingChildData = NO;
            successBlock();
        }*/
    } else {
        successBlock();
    }
}

- (void)createPostSendTypesIfNeeded
{
    if (self.currentChild && [self.currentChild.sendStatisticsAccounts count] == 0) {
        
        AccountMail *newAccount = [AccountMail createEntity];
        newAccount.name = [[DataUtils currentParent] email];
        [self.currentChild addSendStatisticsAccountsObject:newAccount];
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }
}

- (void)createChildWithName:(NSString *)childname
                    success:(ChildManagerSuccessBlock)successBlock
                    failure:(ChildManagerFailureBlock)failureBlock
{
    __weak ChildManager *weakSelf = self;
    
    //currently we have just one parent in persistence in a time, parent check need to be added if we will take care about more than one parent
    Child *existingChildWithSameName = [Child findFirstByAttribute:@"name" withValue:childname];
    
    if (!existingChildWithSameName || [existingChildWithSameName.identifier integerValue] == kNewUnsynchronizedChildID) {
        __block Child *newChild = existingChildWithSameName;
        
        if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
            [[MTHTTPClient sharedMTHTTPClient] addChildWithName:childname
                                                        success:^(NSDictionary *responseDictionary) {
                                                            NSLog(@"ADDED child %@", childname);
                                                            
                                                            if (!newChild) {
                                                                newChild = [self childWithName:childname
                                                                                    identifier:responseDictionary[@"childid"]];
                                                                
                                                            } else {
                                                                newChild.identifier = responseDictionary[@"childid"];
                                                                newChild.parent = [DataUtils currentParent];
                                                            }
                                                            
                                                            if ([newChild.isCurrent boolValue]) {
                                                                //update child details for current child not synced with server
                                                                [self setActiveChildWithID:[newChild.identifier stringValue]
                                                                                     success:^(NSDictionary *dictionary) {
                                                                                         
                                                                                         [weakSelf updateChildWithSuccess:^{
                                                                                             
                                                                                             [weakSelf setCurrentChild:newChild
                                                                                                           withSuccess:successBlock                                                                      failure:failureBlock];
                                                                                         }
                                                                                                                  failure:failureBlock];
                                                                                     }
                                                                 
                                                                                     failure:failureBlock];
                                                            } else {
                                                                [self setCurrentChild:newChild
                                                                          withSuccess:successBlock                                                                      failure:failureBlock];
                                                            }
                                                            
                                                        } failure:^(BOOL finished, NSError *error) {
                                                            NSLog(@"NOT ADDED child because of error: %@", [error localizedDescription]);
                                                            
                                                            if (failureBlock) {
                                                                failureBlock(error);
                                                            }
            }];
        } else {
            Child *newChild = existingChildWithSameName;
            
            if (!existingChildWithSameName) {
                newChild = [self childWithName:childname identifier:@(kNewUnsynchronizedChildID)];
            }
            
            [self setCurrentChild:newChild
                      withSuccess:successBlock
                          failure:failureBlock];
        }
        
    } else if (failureBlock) {
        [self setCurrentChild:existingChildWithSameName
                  withSuccess:successBlock
                      failure:failureBlock];
    }
}

- (Child *)childWithName:(NSString *)name identifier:(NSNumber *)identifier
{
    Child *newChild = [Child createEntity];
    newChild.name = name;
    newChild.identifier = identifier;
    newChild.parent = [DataUtils currentParent];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    return newChild;
}

- (void)setActiveChildWithID:(NSString *)identifier
                       success:(ChildManagerFinishBlock)successBlock
                       failure:(ChildManagerFailureBlock)failureBlock
{
    [[MTHTTPClient sharedMTHTTPClient] setActiveChildWithID:identifier
                                                      success:^(NSDictionary *responseDictionary) {
        NSLog(@"child set active ID : %@", identifier);
        if (successBlock) {
            successBlock(responseDictionary);
        }
    } failure:^(BOOL finished, NSError *error) {
        NSLog(@"child not set active ID : %@ because of error: %@", identifier, [error localizedDescription]);
        if (failureBlock) {
            failureBlock(error);
        }
    }];
}

- (BOOL)removeChild:(Child *)child
            success:(ChildManagerSuccessBlock)successBlock
            failure:(ChildManagerFailureBlock)failureBlock
{
    __block BOOL isCompleted = NO;
    
    [[MTHTTPClient sharedMTHTTPClient] deleteChildWithName:child.name success:^(BOOL finished, NSError *error) {
        
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"name == %@", child.name];
        isCompleted = [Child deleteAllMatchingPredicate:predicate];
        
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
        
        if (successBlock) {
            successBlock();
        }
    } failure:^(BOOL finished, NSError *error) {
        if (failureBlock) {
            failureBlock(error);
        }
    }];
    
    return isCompleted;
}

- (void)loadChildsWithSuccess:(ChildManagerSuccessBlock)successBlock
                      failure:(ChildManagerFailureBlock)failureBlock
{
    [[MTHTTPClient sharedMTHTTPClient] getChildsSuccess:^(NSDictionary *successResponseData) {
//        NSLog(@"loading childs %@", successResponseData);
        
        NSArray *childs = successResponseData[@"childs"];
        
        NSMutableArray *cachedChilds = [[Child findAll] mutableCopy];
        
        [childs each:^(NSDictionary *data) {
            
            Child *child = [DataUtils childWithName:data[@"name"]];
            
//            NSMutableDictionary *sendStatisticsDictionary = [@{kSendStatisticAccountsKey: data[kSendStatisticAccountsKey]} mutableCopy];
            
            NSMutableDictionary *dataWithoutAccounts = [NSMutableDictionary dictionaryWithDictionary:data];
            [dataWithoutAccounts removeObjectForKey:kSendStatisticAccountsKey];
            
            if (!child) {
                // create new child object
                child = [Child importFromObject:dataWithoutAccounts];
                child.parent = [DataUtils currentParent];
                
                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
            } else {
                [cachedChilds removeObject:child];
            }
            
            [self updateDetailsForChild:child withDictionary:data];
//            //avoid duplicates
//            NSArray *sendStatisticsAccounts = sendStatisticsDictionary[kSendStatisticAccountsKey];
//            
//            sendStatisticsAccounts = [sendStatisticsAccounts reject:^BOOL(NSDictionary *inAccount) {
//                if ([inAccount isKindOfClass:[NSDictionary class]]) {
//                    return [child.sendStatisticsAccounts any:^BOOL(AccountMail *account) {
//                        return [account.name isEqualToString:inAccount[@"name"]];
//                    }];
//                } else {
//                    return YES;
//                }
//            }];
//            
//            sendStatisticsDictionary[kSendStatisticAccountsKey] = sendStatisticsAccounts;
//            
//            // update cached child data
//            [child importValuesForKeysWithObject:sendStatisticsDictionary];
        }];
        
        __block BOOL isAddingNewChilds = NO;
        
        [cachedChilds each:^(Child *child) {
            if ([child.identifier integerValue] != kNewUnsynchronizedChildID) {
                [child deleteEntity];
            }
            else {
                isAddingNewChilds = YES;

                //last created child will be made active
                [self createChildWithName:child.name
                                  success:successBlock
                                  failure:failureBlock];
            }
        }];
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        
        if (successBlock && !isAddingNewChilds) {
            successBlock();
        }
        
    } failure:^(BOOL finished, NSError *error) {
        NSLog(@"failed loading childs because of error: %@", [error localizedDescription]);
        if (failureBlock) {
            failureBlock(error);
        }
    }];
}

- (void)updateDetailsForChild:(Child *)child withDictionary:(NSDictionary *)data
{
    NSMutableDictionary *sendStatisticsDictionary = [@{kSendStatisticAccountsKey: data[kSendStatisticAccountsKey]} mutableCopy];
    
    NSMutableDictionary *dataWithoutAccounts = [NSMutableDictionary dictionaryWithDictionary:data];
    [dataWithoutAccounts removeObjectForKey:kSendStatisticAccountsKey];
    
    [child importValuesForKeysWithObject:dataWithoutAccounts];
    
    //avoid duplicates
    NSArray *sendStatisticsAccounts = sendStatisticsDictionary[kSendStatisticAccountsKey];
    
    sendStatisticsAccounts = [sendStatisticsAccounts reject:^BOOL(NSDictionary *inAccount) {
        if ([inAccount isKindOfClass:[NSDictionary class]]) {
            return [child.sendStatisticsAccounts any:^BOOL(AccountMail *account) {
                return [account.name isEqualToString:inAccount[@"name"]];
            }];
        } else {
            return YES;
        }
    }];
    
    sendStatisticsDictionary[kSendStatisticAccountsKey] = sendStatisticsAccounts;
    
    // update cached child data
    [child importValuesForKeysWithObject:sendStatisticsDictionary];
}

- (void)updateChildWithSuccess:(ChildManagerSuccessBlock)successBlock
                       failure:(ChildManagerFailureBlock)failureBlock
{
    if (!successBlock) {
        successBlock = ^(){};
    }
    
    failureBlock = failureBlock ?: ^(NSError *error){};
    
    if (self.currentChild) {
        NSMutableDictionary *dict = [NSMutableDictionary new];
        dict = (NSMutableDictionary *)[[ChildManager sharedInstance].currentChild toDictionary];
        
        [[MTHTTPClient sharedMTHTTPClient] setChildDetails:dict success:^(BOOL finished, NSError *error) {
            successBlock();
        } failure:^(BOOL finished, NSError *error) {
            failureBlock(error);
        }];
    } else {
        failureBlock([NSError errorWithString:@"No child selected"]);
    }
}

- (void)childsRateWithSuccess:(ChildManagerFinishBlock)finishBlock
                      failure:(ChildManagerFailureBlock)failureBlock
{
    [[MTHTTPClient sharedMTHTTPClient] getChildsRateSuccess:^(NSDictionary *successResponseData) {
        //NSLog(@"childs rate received: %@", successResponseData);
        if (finishBlock) {
            finishBlock(successResponseData);
        }
    } failure:^(BOOL finished, NSError *error) {
        if (failureBlock) {
            failureBlock(error);
        }
    }];
}

- (void)logoutCurrentChild
{
    self.currentChild.isCurrent = @NO;
    self.currentChild = nil;
}

- (void)setCurrentChild:(Child *)currentChild
            withSuccess:(ChildManagerSuccessBlock)successBlock
                failure:(ChildManagerFailureBlock)failureBlock
{
    if (self.currentChild) {
        self.currentChild.isCurrent = @NO;
    }
        
    self.currentChild = currentChild;
    self.currentChild.isCurrent = @YES;
    self.currentChild.isLocationPopupShown = @NO;
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    if (self.currentChild) {
        
        [self reloadChildDataIfNeededWithSuccess:^{
            //set game to current child
            [GameManager sharedInstance].game.openedLevel = nil;
            [GameManager sharedInstance].game.child = self.currentChild;
            
            if (self.addChildCreateGameBlock) {
                self.addChildCreateGameBlock(self.currentChild);
            }
            
            if (self.addChildBlock) {
                self.addChildBlock(self.currentChild);
            }
            if (successBlock) {
                successBlock();
            }
        }
                                         failure:failureBlock];
    }
}

- (void)addAccountForChildWithName:(NSString *)name
{
    AccountMail *newAccount = [AccountMail createEntity];
    newAccount.name = name;
    newAccount.child = self.currentChild;
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)createFBAccountIfNeededWithEmail:(NSString *)email
{
    AccountFB *accountFB = nil;
    if (nil == self.currentChild.postFBAccount) {
        accountFB = [AccountFB createEntity];
        accountFB.mail = email;
        accountFB.child = [ChildManager sharedInstance].currentChild;
    } else {
        accountFB = self.currentChild.postFBAccount;
        accountFB.mail = email;
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)createDefaultChildWithCompletion:(ChildManagerSuccessBlock)completionBlock
{
    Child *unregisteredChild = [DataUtils childWithName:kDefaultChildName];
    
    if (nil == unregisteredChild) {
        unregisteredChild = [Child createEntity];
        unregisteredChild.name = kDefaultChildName;
        unregisteredChild.identifier = @(kNewUnsynchronizedChildID);
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }
    
    [self setCurrentChild:unregisteredChild withSuccess:completionBlock failure:nil];
}

//- (void)addDefaultChildToParentIfNeeded
//{
//    Child *child = self.currentChild;
//    
//    if (child && ([child.name isEqualToString:kDefaultChildName] || child.parent == nil)) {
//        NSLog(@" << current Parent : %@", [DataUtils currentParent]);
//        child.parent = [DataUtils currentParent];
//        
//        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
//    }
//}

- (BOOL)isParentHaveChildWithName:(NSString *)name
{
    BOOL isHave = [[DataUtils childs] any:^BOOL(Child *child) {
        return [child.name isEqualToString:name];
    }];
    
    return isHave;
}

- (void)updateCurrentChildReference
{
    self.currentChild = [Child findFirstByAttribute:@"isCurrent" withValue:@YES];
}


#pragma mark - Setters&Getters


@end
