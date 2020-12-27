//
//  ActionView.h
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ActionViewDelegate.h"
#import "SubActionViewDelegate.h"

@class Action;

@interface ActionView : UIView <SubActionViewDelegate>

@property (unsafe_unretained, nonatomic)    NSInteger index;
@property (strong, nonatomic)               UILabel *headerLabel;
@property (strong, nonatomic)               NSMutableArray *subActions;
@property (strong, nonatomic)               NSString *answer;
@property (weak, nonatomic)    id <ActionViewDelegate> delegate;
@property (strong, nonatomic)               Action *action;
@property (strong, nonatomic) UIButton *deleteButton;

- (id)initWithAction:(Action *)action;
- (CGFloat)drawSubActionsWithWidth:(CGFloat)width;

@end