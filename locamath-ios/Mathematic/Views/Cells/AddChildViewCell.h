//
//  AddChildViewCell.h
//  Mathematic
//
//  Created by SanyaIOS on 24.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "GMGridViewCell.h"
typedef void(^AddChildBlock)();

@interface AddChildViewCell : GMGridViewCell

@property (copy, nonatomic) AddChildBlock addChildBlock;

@end
