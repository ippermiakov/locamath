//
//  ChildViewCell.h
//  Mathematic
//
//  Created by SanyaIOS on 16.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "GMGridView.h"

@interface ChildViewCell : GMGridViewCell

@property (strong, nonatomic) IBOutlet UIImageView *avatarImageView;
@property (strong, nonatomic) IBOutlet UILabel *nameLabel;
@property (strong, nonatomic) IBOutlet UIImageView *backgroundForChildImag;
@property (strong, nonatomic) IBOutlet UIImageView *backgroundNameImage;

@end
