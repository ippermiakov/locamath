//
//  AddAccountCell.h
//  Mathematic
//
//  Created by Developer on 19.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "AddAccountCellDelegate.h"
#import "SSTextField.h"

@interface AddAccountCell : UITableViewCell <UITextFieldDelegate>

@property (nonatomic, assign) id <AddAccountCellDelegate> delegate;
@property (weak, nonatomic) IBOutlet UIButton *button;
@property (weak, nonatomic) IBOutlet SSTextField *textField;
@property (strong, nonatomic) IBOutlet UIView *cellView;


@end
