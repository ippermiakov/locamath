//
//  ChooseChildFieldCell.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 26.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SSTextField.h"

@class ChooseChildFieldCell;

@protocol ChooseChildFieldCellDelegate <NSObject>

- (void)cell:(ChooseChildFieldCell*)cell addedChildWithName:(NSString*)childname;

@end

@interface ChooseChildFieldCell : UITableViewCell

@property (weak, nonatomic) id<ChooseChildFieldCellDelegate> delegate;
@property (weak, nonatomic) IBOutlet SSTextField *childnameTextField;
@property (weak, nonatomic) IBOutlet UIImageView *bgImageView;
@property (weak, nonatomic) IBOutlet UILabel *childnameLabel;

@end
