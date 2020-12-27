//
//  TTTAttributedLabel+MTTextView.h
//  Mathematic
//
//  Created by SanyaIOS on 19.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TTTAttributedLabel.h"
#import "AbstractTask.h"

@interface TTTAttributedLabel (MTTextView)

- (UIScrollView *)MTTextViewWithLabel:(TTTAttributedLabel *)label
                             withTask:(id<AbstractTask>)task
                              forView:(UIView *)view;

@end
