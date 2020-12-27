//
//  UIViewController+DismissViewController.h
//  Flixa
//
//  Created by SanyaIOS on 03.06.13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UIViewController (DismissViewController)

- (void)dismissGameFlowViewControllersWithViewController:(UIViewController *)controller;
- (UIViewController *)topPresentedViewController;

@end
