//
//  GameAlertViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 16.07.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PresentableViewController.h"

@interface GameAlertViewController : PresentableViewController

+ (GameAlertViewController *)sharedInstance;

+ (void)showGameAlertWithMessageError:(NSError *)error withPresenter:(UIView *)view;
+ (void)showGameAlertWithMessage:(NSString *)message withPresenter:(UIView *)view;

@end
