//
//  StrokeLabel.m
//  Mathematic
//
//  Created by SanyaIOS on 10.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StrokeLabel.h"
#import <QuartzCore/QuartzCore.h>

@implementation StrokeLabel

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)drawTextInRect:(CGRect)rect {
    
    CGSize shadowOffset = self.shadowOffset;
    UIColor *textColor = self.textColor;
    
    CGContextRef context = UIGraphicsGetCurrentContext();
    CGContextSetLineWidth(context, 3);
    
    CGContextSetTextDrawingMode(context, kCGTextFillStroke);
    self.textColor = [UIColor whiteColor];
    [super drawTextInRect:rect];
    
    CGContextSetTextDrawingMode(context, kCGTextFill);
    self.textColor = textColor;
    self.shadowOffset = CGSizeMake(0, 0);
    [super drawTextInRect:rect];
    
    self.shadowOffset = shadowOffset;
}

- (void)whiteShadowForLabel
{
    self.layer.shadowColor = [[UIColor whiteColor] CGColor];
    self.layer.shadowOffset = CGSizeMake(0.0f, 1.0f);
    self.layer.shadowOpacity = 10.0f;
    self.layer.shadowRadius = 1.0f;
}

@end
