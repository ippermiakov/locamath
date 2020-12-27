//
//  ChooseChildFieldCell.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 26.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChooseChildFieldCell.h"
#import "UIColor+Mathematic.h"

@interface ChooseChildFieldCell ()<UITextFieldDelegate>

@end

@implementation ChooseChildFieldCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)awakeFromNib
{
    self.childnameTextField.delegate = self;
    self.childnameTextField.placeholder = NSLocalizedString(@"Enter child name", @"Choose child popup");
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    if (selected) {
        [self.bgImageView setImage:[UIImage imageNamed:@"Name_choosed_field.png"]];
        self.childnameTextField.textColor = [UIColor whiteColor];
        self.childnameLabel.textColor = [UIColor whiteColor];
    }
    else {
        [self.bgImageView setImage:[UIImage imageNamed:@"login_text_field.png"]];
        self.childnameTextField.textColor = [UIColor registrationFormsTextColor];
        self.childnameLabel.textColor = [UIColor registrationFormsTextColor];
    }
    // Configure the view for the selected state
}

#pragma mark - UITextFieldDelegate methods

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string
{
    if (textField.text.length + string.length > 15) {
        return NO;
    } else {
        return YES;
    }
}

- (void)textFieldDidEndEditing:(UITextField *)textField
{
    [self changeTextIfNeeded];
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    [self changeTextIfNeeded];
    [textField resignFirstResponder];
    return YES;
}

#pragma mark -

- (void)changeTextIfNeeded
{
    if (self.childnameTextField.text.length > 0) {
        [self.delegate cell:self addedChildWithName:self.childnameTextField.text];
    }
}

@end
