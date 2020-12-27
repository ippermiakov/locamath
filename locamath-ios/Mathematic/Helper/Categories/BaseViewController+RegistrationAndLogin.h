//
//  UIViewController+RegistrationAndLogin.h
//  Flixa
//
//  Created by alexbutenko on 6/6/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ViewControllerViewAppearanceTracking.h"
#import "BaseViewController.h"

typedef void(^RegisterComplition)();

@interface BaseViewController (RegistrationAndLogin)<ViewControllerViewAppearanceTracking>

- (void)registerObservers;

- (void)removeNotificationObservation;

- (void)showLoginAndRegisterPlayerScreen;
- (void)selectChildWithTraining:(BOOL)isWithTraining canAddChilds:(BOOL)canAddChilds;
- (void)selectChildWithTraining:(BOOL)isWithTraining;

- (void)performSynchronizationIfNeeded;

- (void)handleIfParentIsRegisteredWithError:(NSError *)error andUserInfo:(NSDictionary *)userInfo;

- (BOOL)canPresentViewControllerClass:(Class)class;

- (void)registerParentIfNeededWithComplitionBlock:(RegisterComplition)complition;

@end
