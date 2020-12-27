//
//  UIAlertView+Error.m
//  Flixa
//
//  Created by alexbutenko on 5/3/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "UIAlertView+Error.h"
#import "BlocksKit.h"
#import "AlertViewManager.h"
#import "MBProgressHUD+Mathematic.h"
#import <objc/runtime.h>

@implementation UIAlertView (Error)

+ (void)load
{
    SEL origSel = @selector(show);
    SEL overrideSel = @selector(override_show);
    
    Method origMethod = class_getInstanceMethod(UIAlertView.class, origSel);
    Method overrideMethod = class_getInstanceMethod(UIAlertView.class, overrideSel);
    
    if (class_addMethod(UIAlertView.class, origSel, method_getImplementation(overrideMethod), method_getTypeEncoding(overrideMethod))) {
        class_replaceMethod(UIAlertView.class, overrideSel, method_getImplementation(origMethod), method_getTypeEncoding(origMethod));
    } else {
        method_exchangeImplementations(origMethod, overrideMethod);
    }
}

- (void)override_show
{
    [AlertViewManager sharedInstance].displayedAlertView = self;
    [self override_show];
}

+ (void)showErrorAlertViewWithMessage:(NSString *)message
{
    [UIAlertView showErrorAlertViewWithMessage:message handler:nil];
}

+ (void)showErrorAlertViewWithMessage:(NSString *)message handler:(void (^)(UIAlertView *, NSInteger))block
{
    [UIAlertView showAlertViewWithTitle:NSLocalizedString(@"Error", nil)
                                message:message
                                handler:block];
}

+ (void)showAlertViewWithMessage:(NSString *)message
{
    [UIAlertView showAlertViewWithTitle:nil
                                message:message
                                handler:nil];
}

+ (void)showAlertViewWithTitle:(NSString *)title
                       message:(NSString *)message
                       handler:(void (^)(UIAlertView *, NSInteger)) block
{
    if ([message length] > 0 && ![[AlertViewManager sharedInstance].displayedAlertView.message isEqualToString:message]) {
        
        if ([AlertViewManager sharedInstance].displayedHUD) {
            [MBProgressHUD hideHUDForWindow];
        }
        
        UIAlertView *alertView = [[self class] alertViewWithTitle:title
                                                          message:message];

        alertView.cancelButtonIndex = [alertView addButtonWithTitle:NSLocalizedString(@"Dismiss", nil)];
        
        // Set `didDismissBlock`
        if (block) alertView.didDismissBlock = block;
        
        // Show alert view
        [alertView show];
    }
}

@end
