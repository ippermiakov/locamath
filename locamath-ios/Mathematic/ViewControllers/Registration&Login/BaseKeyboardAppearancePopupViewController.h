//
//  BaseKeyboardAppearancePopupViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 24.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PresentableViewController.h"

@interface BaseKeyboardAppearancePopupViewController : PresentableViewController

@property (strong, nonatomic) IBOutlet UIButton *continueButton;
@property (unsafe_unretained, nonatomic) CGFloat lowestViewYOffset;

- (CGRect)lowestFieldRect;

@end
