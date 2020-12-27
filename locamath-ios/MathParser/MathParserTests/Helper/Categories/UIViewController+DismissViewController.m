//
//  UIViewController+DismissViewController.m
//  Flixa
//
//  Created by SanyaIOS on 03.06.13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "UIViewController+DismissViewController.h"
#import "BaseViewController.h"
#import "LevelMapViewController.h"
#import "GameManager.h"

@implementation UIViewController (DismissViewController)

- (void)dismissGameFlowViewControllersWithViewController:(BaseViewController *)controller
{
    if (controller && ![controller isKindOfClass:[LevelMapViewController class]]) {
        
        UIViewController *presentingVC = controller.presentingViewController;
        
        if (!presentingVC) {
            controller = (BaseViewController *)[self topPresentedViewController];
            presentingVC = controller.presentingViewController;
        }

        [controller goBackAnimated:NO withDelegate:controller.backDelegate withOption:NO];
        
        [self dismissGameFlowViewControllersWithViewController:presentingVC];
    }
}

- (UIViewController *)topPresentedViewController
{
    UIViewController *topPresentedViewController = [GameManager levelMap].presentedViewController;

    while (topPresentedViewController.presentedViewController) {
        topPresentedViewController = topPresentedViewController.presentedViewController;
    }
    
    return topPresentedViewController;
}

@end
