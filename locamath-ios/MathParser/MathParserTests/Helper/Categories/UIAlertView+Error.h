//
//  UIAlertView+Error.h
//  Flixa
//
//  Created by alexbutenko on 5/3/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UIAlertView (Error)

+ (void)showErrorAlertViewWithMessage:(NSString *)message;

+ (void)showErrorAlertViewWithMessage:(NSString *)message
                              handler:(void (^)(UIAlertView *, NSInteger)) block;

+ (void)showAlertViewWithTitle:(NSString *)title
                       message:(NSString *)message
                       handler:(void (^)(UIAlertView *, NSInteger)) block;

+ (void)showAlertViewWithMessage:(NSString *)message;

@end
