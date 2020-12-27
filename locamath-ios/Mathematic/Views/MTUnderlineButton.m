//
//  MTUnderlineButton.m
//  Mathematic
//
//  Created by SanyaIOS on 12/12/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTUnderlineButton.h"

@interface MTUnderlineButton ()
@property (strong, nonatomic) UIView *line;
@end

@implementation MTUnderlineButton

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
    }
    return self;
}

- (void)awakeFromNib
{
    self.backgroundColor = [UIColor clearColor];
    
    if (self.subviews.count) {
        NSArray * buttons = [self.subviews select:^BOOL(UIView *obj) {
            return [obj isKindOfClass:[UIButton class]];
        }];
        if (buttons.count) {
            UIButton *button = buttons[0];
            [button addTarget:self action:@selector(drawLine:) forControlEvents:UIControlEventTouchUpInside];
        }
        
        if (self.tag == 1) {
            [self drawLine:nil];
        }
    }
}

- (void)drawLine:(UIButton *)button
{
    if (self.line) {
        [self.line removeFromSuperview];
        self.line = nil;
    }
    
    [self.delegate didSelectButtunWithTag:self.tag];
    
    self.line = [[UIView alloc] initWithFrame:CGRectMake(self.frame.size.width/2, self.frame.size.height - 5, 0, 2)];
    self.line.backgroundColor = [UIColor whiteColor];
    [self addSubview:self.line];
    [UIView animateKeyframesWithDuration:0.5 delay:0 options:UIViewAnimationOptionCurveEaseOut animations:^{
        self.line.frame = CGRectMake(0, self.frame.size.height - 5, self.frame.size.width, 2);
    } completion:nil];
}

- (void)removeLine
{
    if (self.line) {
        
        [UIView animateKeyframesWithDuration:0.5 delay:0 options:UIViewAnimationOptionCurveEaseOut animations:^{
            self.line.frame = CGRectMake(self.frame.size.width/2, self.frame.size.height - 5, 0, 2);
        } completion:^(BOOL finished) {
            [self.line removeFromSuperview];
            self.line = nil;

        }];
        
    }
}

@end
