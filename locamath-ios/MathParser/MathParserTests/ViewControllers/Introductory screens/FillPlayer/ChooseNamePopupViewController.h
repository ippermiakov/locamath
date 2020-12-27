//
//  ChooseNamePopupViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseKeyboardAppearancePopupViewController.h"
#import "SSTextField.h"

@class Child;

@protocol ChooseNamePopupViewControllerDelegate <NSObject>
@optional
- (void)didEditChild:(Child *)child;
@end


@interface ChooseNamePopupViewController : BaseKeyboardAppearancePopupViewController <UITextFieldDelegate>

@property (weak, nonatomic)   BaseViewController<ChooseNamePopupViewControllerDelegate> *parentVC;
@property (strong, nonatomic) IBOutlet SSTextField *nameTF;
@property (unsafe_unretained, nonatomic) BOOL shouldCreateNewChild;

- (IBAction)onTapContinue:(id)sender;

@end
