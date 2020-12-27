//
//  LoginViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 15.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PresentableViewController.h"

@interface LoginOrRegisterViewController : PresentableViewController

@property (nonatomic) BOOL isRegister;
@property (retain, nonatomic) NSDictionary *autoLoginUserInfo;

@end
