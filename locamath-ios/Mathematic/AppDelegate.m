//
//  AppDelegate.m
//  Mathematic
//
//  Created by Alexander on 11/18/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "AppDelegate.h"
#import "LevelMapViewController.h"
#import "SoundManager.h"
#import "MTFileParser.h"
#import "GameManager.h"
#import "PushNotificationManager.h"
#import "ChildManager.h"
#import "MTHTTPClient.h"
#import "AlertViewManager.h"
#import "Flurry.h"
#import "Parent.h"
#import "DataUtils.h"

@implementation AppDelegate

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{    
    [Flurry setCrashReportingEnabled:YES];
    [Flurry startSession:@"YQPKXZPP5Q8TXWXKFGKH"];
    
    [MagicalRecord setupAutoMigratingCoreDataStack];
    
    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    
    self.window.rootViewController = [LevelMapViewController new];
    [self.window makeKeyAndVisible];
    
    [self configureApplication];
   
    //uncomment to delete specific parent
//    [[MTHTTPClient sharedMTHTTPClient] deleteAccountWithEmail:@"alexandr.butenko@gmail.com"
//                                                      success:^(BOOL finished, NSError *error) {
//                                                          NSLog(@"parent has been deleted from server");
//                                                      } failure:^(BOOL finished, NSError *error) {                                                                      NSLog(@"failed to delete parent");
//                                                      }];
    
    return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
    [[GameManager sharedInstance] stopAppTimer];
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
    [self saveGameSettings];
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
    //[[GameManager sharedInstance] startAppTimer];
    
    [[GameManager sharedInstance] startAppTimer];
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    [FBSession.activeSession handleDidBecomeActive];
    
    if ([[DataUtils currentParent].isSchoolMode boolValue]) {
        [GameManager.levelMap showSelectionChildForSchoolMode];
    } else if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [[ChildManager sharedInstance] reloadChildDataIfNeededWithSuccess:nil
                                                                  failure:nil];
    }
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

#pragma mark - Configuration application

- (void)configureApplication
{
    [SoundManager sharedInstance];
    [GameManager sharedInstance];
    [AlertViewManager sharedInstance];
}

- (void)saveGameSettings
{
    [[GameManager sharedInstance] saveData];
}

- (BOOL)application:(UIApplication *)application
            openURL:(NSURL *)url
  sourceApplication:(NSString *)sourceApplication
         annotation:(id)annotation
{
    return [FBSession.activeSession handleOpenURL:url];
}

- (void) onPushAccepted:(PushNotificationManager *)pushManager withNotification:(NSDictionary *)pushNotification
{
    NSLog(@"Push notification received");
}


@end
