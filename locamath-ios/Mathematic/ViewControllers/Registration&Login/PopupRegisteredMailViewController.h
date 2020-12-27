//
//  PopupRegisteredMailViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 18.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseKeyboardAppearancePopupViewController.h"

@interface PopupRegisteredMailViewController : BaseKeyboardAppearancePopupViewController

@property (unsafe_unretained, nonatomic) BOOL isRegister;
@property (unsafe_unretained, nonatomic) BOOL isAutoLogin;

//we don't retain values in ivars, just interface to modify internal structure
@property (copy, nonatomic) NSString *email;
@property (copy, nonatomic) NSString *password;

@end
