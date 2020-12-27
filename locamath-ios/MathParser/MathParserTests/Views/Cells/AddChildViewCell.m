//
//  AddChildViewCell.m
//  Mathematic
//
//  Created by SanyaIOS on 24.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AddChildViewCell.h"

@interface AddChildViewCell ()

- (IBAction)onTapAdd:(id)sender;

@end

@implementation AddChildViewCell

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

- (IBAction)onTapAdd:(id)sender
{
    if (self.addChildBlock) {
        self.addChildBlock();
    }
}

@end
