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
#import "PresentableViewController.h"

@implementation UIViewController (DismissViewController)

- (void)dismissGameFlowViewControllersWithViewController:(BaseViewController *)controller
{
    if (controller && ![controller isKindOfClass:[LevelMapViewController class]]) {
        
        UIViewController *presentingVC = [controller isKindOfClass:[PresentableViewController class]] ?
                                         [controller performSelector:@selector(myPresentingViewController) withObject:nil] :
                                          controller.presentingViewController;
        
//        if (!presentingVC) {
//            controller = (BaseViewController *)[self topPresentedViewController];
//            presentingVC = controller.presentingViewController;
//        }

        [controller goBackAnimated:NO withDelegate:controller.backDelegate withOption:NO];
        
        [self dismissGameFlowViewControllersWithViewController:presentingVC];
    }
}

- (BaseViewController *)topPresentedViewController
{
    BaseViewController *topPresentedViewController = [GameManager levelMap].myPresentedViewController;

    while (topPresentedViewController.myPresentedViewController) {
        topPresentedViewController = topPresentedViewController.myPresentedViewController;
    }
    
    //NSLog(@"topPresentedViewController %@", topPresentedViewController);
    
    return topPresentedViewController;
}

@end
