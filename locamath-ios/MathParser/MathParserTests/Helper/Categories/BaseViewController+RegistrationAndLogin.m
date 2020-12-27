//
//  UIViewController+RegistrationAndLogin.m
//  Flixa
//
//  Created by alexbutenko on 6/6/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "BaseViewController+RegistrationAndLogin.h"
#import "MTHTTPClient.h"
#import "StartScreenViewController.h"
#import "ChildManager.h"
#import "LevelMapViewController.h"
#import "Level_1_ViewController.h"

#import "ChooseChildPopupViewController.h"
#import "ChooseNamePopupViewController.h"
#import "PresentingSeguesStructure.h"
#import "Definition1ViewController.h"
#import "Definition2ViewController.h"
#import "Definition3ViewController.h"
#import "ProfileViewController.h"
#import "StatisticViewController.h"
#import "DataUtils.h"
#import "SynchronizationManager.h"
#import "MTHTTPClient.h"
#import "PopupForDefaultChildViewController.h"
#import "MBProgressHUD+Mathematic.h"
#import "UIViewController+DismissViewController.h"
#import "GameManager.h"
#import "Game.h"


@implementation BaseViewController (RegistrationAndLogin)

@dynamic didViewAppear;

- (void)registerObservers
{
    //provide opportunity to play without user authentification
    [self addAuthorizationNotificationObservationIfNeeded];
    
    [[MTHTTPClient sharedMTHTTPClient] setReachabilityStatusChangeBlock:^(AFNetworkReachabilityStatus status) {
        [self addAuthorizationNotificationObservationIfNeeded];
    }];
    
//    NSLog(@"subscribed %@", self);
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(showChildSelectionScreen)
                                                 name:kChildSelectionFailureNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(performSynchronizationIfNeeded)
                                                 name:kChildSelectionSuccessNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(showLoginScreenWithUserInfo:)
                                                 name:kParentExistsFailureNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(updateViewOnSyncFinished)
                                                 name:kSynchronizationFinishedNotification
                                               object:nil];
}

- (void)removeNotificationObservation
{
//    NSLog(@"unsubscribed %@", self);

    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

- (void)addAuthorizationNotificationObservationIfNeeded
{
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kAuthorizationFailureNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kChildSelectionFailureNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kChildSelectionSuccessNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kParentExistsFailureNotification object:nil];
    
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(showLoginAndRegisterPlayerScreen)
                                                     name:kAuthorizationFailureNotification
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(showChildSelectionScreen)
                                                     name:kChildSelectionFailureNotification
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(performSynchronizationIfNeeded)
                                                     name:kChildSelectionSuccessNotification
                                                   object:nil];
        
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(showLoginScreenWithUserInfo:)
                                                     name:kParentExistsFailureNotification
                                                   object:nil];
    }
}

- (void)performSynchronizationIfNeeded
{
    if (self.didViewAppear) {
        
        __block MBProgressHUD *HUD = nil;
        
        [[SynchronizationManager sharedInstance] syncDataIfNeededWithSuccess:^{
            [MBProgressHUD hideOnSyncCompletion];

        } failure:^(NSError *error) {
            [MBProgressHUD hideOnSyncFailure];
        } gettingProgress:^(CGFloat progress) {
            if ([[ChildManager sharedInstance].currentChild.isDataLoaded boolValue] &&
                ![[ChildManager sharedInstance].currentChild.isSyncCompleted boolValue]) {
                HUD = [MBProgressHUD showSyncHUDForWindow];
                HUD.detailsLabelText = NSLocalizedString(@"Getting data", nil);
            }
            
        } sendingProgress:^(CGFloat progress) {
            [HUD updateWithProgress:progress];
        }];
    }
}

- (void)showLoginAndRegisterPlayerScreen
{
    // if current view controller is top in presenting hierarchy
    if ([self canPresentViewControllerClass:[StartScreenViewController class]]) {
        [self presentViewController:[StartScreenViewController new]
                           animated:NO
                         completion:nil];
    }
}

- (void)showLoginScreenWithUserInfo:(NSNotification *)notification
{
    if ([self canPresentViewControllerClass:[StartScreenViewController class]]) {
        StartScreenViewController *startScreenViewController = [StartScreenViewController new];
        startScreenViewController.autoLoginUserInfo = [notification userInfo];
        [self presentViewController:startScreenViewController
                           animated:NO
                         completion:nil];
    }
}

- (void)showChildSelectionScreen
{
    if (![ChildManager sharedInstance].currentChild) {
        
        if ([self canPresentViewControllerClass:[ChooseChildPopupViewController class]]) {
            [self selectChildWithTraining:[self isKindOfClass:[LevelMapViewController class]]];
        }
        else {
            [self dismissGameFlowViewControllersWithViewController:self];
        }
    } else {
        [[ChildManager sharedInstance] reloadChildDataIfNeededWithSuccess:^ {
            NSLog(@"reactivated %@", [ChildManager sharedInstance].currentChild.name);
        } failure:^(NSError *error) {
            NSLog(@"failed to reactivate %@", [ChildManager sharedInstance].currentChild.name);
        }];
    }
}

- (void)selectChildWithTraining:(BOOL)isWithTraining
{
    //login if parent not authentificated and child not default
    if (![[MTHTTPClient sharedMTHTTPClient] isParentAuthentificated] && ![DataUtils isCurrentChildDefault]) {
        [self showLoginAndRegisterPlayerScreen];
        return;
    }

    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    
    //there is parent, but no current child is bound yet
    if ([ChildManager sharedInstance].currentChild == nil && [DataUtils currentParent] != nil) {
        
        ChooseChildPopupViewController *chooseChildPopupViewController = [ChooseChildPopupViewController new];
        
        if (![GameManager sharedInstance].game.skipStatisticScreen) {
            chooseChildPopupViewController.onFinish = ^{
                [self presentViewController:[StatisticViewController new] animated:NO completion:nil];
            };
        }
        
        [seguesStructure addLinkWithObject:chooseChildPopupViewController];
    }
    
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (void)handleIfParentIsRegisteredWithError:(NSError *)error andUserInfo:(NSDictionary *)userInfo
{
    [MBProgressHUD hideHUDForWindow];
    
    if (error.code == kParentAlreadyExistsErrorCode) {
        [(PresentableViewController *)self dismissToRootViewController];
        
        NSString *message = [NSString stringWithFormat:@"%@.%@", [error localizedDescription], NSLocalizedString(@" Child's progress will be lost!", nil)];
        
        [UIAlertView showAlertViewWithTitle:NSLocalizedString(@"Attention", nil)
                                    message:message cancelButtonTitle:@"Cancel"
                          otherButtonTitles:[NSArray arrayWithObjects:@"OK", nil]  handler:^(UIAlertView *alertView, NSInteger buttonIndex) {
                              
                              if (buttonIndex == 1) {
                                  
                                  Child *childToDelete = [ChildManager sharedInstance].currentChild;
                                  
                                  [[ChildManager sharedInstance] logoutCurrentChild];
                                  [childToDelete deleteEntity];
                                  
                                  [[NSNotificationCenter defaultCenter] postNotificationName:kParentExistsFailureNotification
                                                                                      object:nil
                                                                                    userInfo:userInfo];
                                  
                              }
                          }];
    } else {
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
    }
}

#pragma mark - Helper methods

//TODO: refactor
- (BOOL)canPresentViewControllerClass:(Class)class
{
    BOOL isTopMost = [self isViewLoaded] && self.view == [self topMostViewFromViews:self.view.window.subviews];
    
//    if ([self isViewLoaded]) {
//        NSLog(@"topMostViewFromViews: %@", [self topMostViewFromViews:self.view.window.subviews]);
//    }
    
    BOOL canPresent = !self.presentedViewController &&
    ([self respondsToSelector:@selector(didViewAppear)] ? self.didViewAppear : YES) &&
    isTopMost &&
    ![self isKindOfClass:class] &&
    ![self isKindOfClass:[Definition3ViewController class]];
    
    NSLog(@"!!! VALIDATE TO PRESENT : %@", self);
    NSLog(@"isPresentedViewController check: %@", !self.presentedViewController ? @"OK":@"FAILED");
    
    if ([self respondsToSelector:@selector(didViewAppear)]) {
        NSLog(@"viewDidAppear check: %@", self.didViewAppear ? @"OK":@"FAILED");
    }
    NSLog(@"topMostViewFromViews check: %@", isTopMost ? @"OK":@"FAILED");
    NSLog(@"self presenting check: %@", ![self isKindOfClass:class] ? @"OK":@"FAILED");
    NSLog(@"Definition3ViewController restriction for iOS 5: %@", ![self isKindOfClass:[Definition3ViewController class]] ? @"OK":@"FAILED");
    
    return canPresent;
}

- (UIView *)topMostViewFromViews:(NSArray *)views
{
    __block UIView *fullscreenView = nil;
    
//    NSLog(@"views: %@", views);
    
    [views enumerateObjectsWithOptions:NSEnumerationReverse usingBlock:^(UIView *view, NSUInteger idx, BOOL *stop) {
        
        if ([self isFullScreenView:view] && [NSStringFromClass([view class]) isEqualToString:@"UIView"]) {
            fullscreenView = view;
            *stop = YES;
        }
    }];
    
//    NSLog(@"fullscreenView: %@", fullscreenView);
//
//    NSLog(@"[[views lastObject] superview]: %@", [[views lastObject] superview]);
    
    return fullscreenView ? [self topMostViewFromViews:fullscreenView.subviews] : [[views lastObject] superview];
}

- (BOOL)isFullScreenView:(UIView *)view
{
    CGSize screenSize = [[UIScreen mainScreen] bounds].size;
    
    return CGSizeEqualToSize(view.frame.size, (CGSize){screenSize.width, screenSize.height}) ||
           CGSizeEqualToSize(view.frame.size, (CGSize){screenSize.height, screenSize.width});
}

@end
