//
//  UILabel+Mathematic.m
//  Mathematic
//
//  Created by alexbutenko on 6/27/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UILabel+Mathematic.h"

@implementation UILabel (Mathematic)

+ (UILabel *)olympiadTasksLabelWithText:(NSString *)text
                            placeholder:(NSString *)placeholder
                          withAlignment:(HintsAlignmentType)alignmentType
{
    UILabel *label = [UILabel new];
    
    UIFont *font = [UIFont olympiadTasksFont];
    
    [label setBackgroundColor:[UIColor clearColor]];
    [label setTextColor:[UIColor solvingToolsColor]];
    [label setFont:font];
        
    CGRect placeholderFittedFrame = label.frame;
    label.text = text;
    
    if (alignmentType == HintsAlignmentTypeRight) {
        CGSize textSize = [text sizeWithFont:font];
        CGSize placeholderSize = [placeholder sizeWithFont:font];
        
        if (textSize.width > placeholderSize.width) {
            placeholderFittedFrame.size = textSize;
        } else {
            placeholderFittedFrame.size = placeholderSize;
        }
        
        label.frame = placeholderFittedFrame;
        label.textAlignment = NSTextAlignmentRight;
    } else {
        [label sizeToFit];
    }
    
    return label;
}

@end
