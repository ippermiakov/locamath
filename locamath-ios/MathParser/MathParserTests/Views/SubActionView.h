//
//  SubActionView.h
//  Mathematic
//
//  Created by Developer on 18.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SubActionViewDelegate.h"

@interface SubActionView : UIView

@property (unsafe_unretained, nonatomic) ActionType theType;
@property (unsafe_unretained, nonatomic) NSInteger index;
@property (weak, nonatomic) id <SubActionViewDelegate> delegate;
@property (weak, nonatomic) UIScrollView *parentScrollView;
@property (unsafe_unretained, nonatomic) BOOL isTaskCorrect;

- (id)initWithType:(ActionType)type;
- (void)drawAnswerLabel:(BOOL)flag;
- (void)createComponentFromString:(NSString *)string;
- (void)displaySupportObjects;

@end
