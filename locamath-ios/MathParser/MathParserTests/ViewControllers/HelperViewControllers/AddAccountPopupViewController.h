//
//  AddAccountPopupViewController.h
//  Mathematic
//
//  Created by Developer on 19.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseKeyboardAppearancePopupViewController.h"
#import "AddAccountCellDelegate.h"

@interface AddAccountPopupViewController : BaseKeyboardAppearancePopupViewController <AddAccountCellDelegate>

@property (weak, nonatomic) IBOutlet UIImageView *imageView;
@property (weak, nonatomic) IBOutlet UILabel *label;
@property (unsafe_unretained, nonatomic) BOOL isFBAccount;

@end
