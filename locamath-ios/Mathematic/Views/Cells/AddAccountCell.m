//
//  AddAccountCell.m
//  Mathematic
//
//  Created by Developer on 19.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AddAccountCell.h"

@implementation AddAccountCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    if (selected) {
        self.textField.textColor = [UIColor whiteColor];
    }
    else {
        self.textField.textColor = [UIColor registrationFormsTextColor];
    }
    // Configure the view for the selected state
}

- (void)textFieldDidEndEditing:(UITextField *)textField
{
    [self.delegate textHasBeenEdited:textField.text forIndex:self.button.tag];
}


@end
