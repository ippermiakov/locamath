//
//  TTTAttributedLabel+MTTextView.m
//  Mathematic
//
//  Created by SanyaIOS on 19.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TTTAttributedLabel+MTTextView.h"
#import "Task.h"
#import "Level.h"


@implementation TTTAttributedLabel (MTTextView)

- (UIScrollView *)MTTextViewWithLabel:(TTTAttributedLabel *)label
                             withTask:(id<AbstractTask>)task
                              forView:(UIView *)view
{
    UIScrollView *textView = [UIScrollView new];
    textView.frame = label.frame;
    
    CGSize size =  [task.objective sizeWithFont:label.font
                              constrainedToSize:(CGSize){label.frame.size.width, UINT32_MAX}
                                  lineBreakMode:NSLineBreakByWordWrapping];
    
    CGRect labelFrame = label.frame;
    labelFrame.origin = CGPointZero;
    labelFrame.size = CGSizeMake(size.width, size.height + label.font.capHeight) ;
    label.frame = labelFrame;
    
    textView.contentSize = label.frame.size;
    
    [textView addSubview:label];
    
    [view addSubview:textView];
    
    if (textView.contentSize.height > textView.frame.size.height + label.font.capHeight) {
        UIImage *downArrowImage = [UIImage imageNamed:@"down_arrow.png"];
        UIImageView *downArrowImageView = [[UIImageView alloc] initWithImage:downArrowImage];
        
        CGSize arrowSize = {32, 32};
        CGPoint downArrowOrigin = (CGPoint){CGRectGetMaxX(textView.frame),
                                            CGRectGetMaxY(textView.frame)};
        downArrowImageView.alpha = 0.5f;
        downArrowOrigin.y -= arrowSize.height;
        downArrowImageView.frame = (CGRect){downArrowOrigin, arrowSize};
        
        [view addSubview:downArrowImageView];
    }
    
    Level *level = nil;
    
    if ([task.level isKindOfClass:[Level class]]) {
        level = task.level;
    }
    
    NSString *labelString = task.objective;
    
    UIColor *color = [UIColor redColor];
    
    if ([level.isTest boolValue]) {
        Task *taskSolving = task;
    
        [label setText:labelString afterInheritingLabelAttributesAndConfiguringWithBlock:^NSMutableAttributedString *(NSMutableAttributedString *mutableAttributedString) {
            [taskSolving.letters each:^(NSString *letter) {
                [mutableAttributedString addAttribute:(NSString *)kCTForegroundColorAttributeName
                                                    value:(id)color.CGColor
                                                    range:[self correctRangeForLetter:letter
                                                                               inText:labelString
                                                                     withColorLetters:taskSolving.letters]];

            }];
            
            return mutableAttributedString;
        }];
        
    } else {
        label.text = labelString;
    }
    
    return textView;
}

- (BOOL)shouldLetterBeColored:(NSRange)range inText:(NSString *)text withLetters:(NSArray *)colorLetters
{
    NSRange rangeToSubstring = NSMakeRange(range.location - 2, 2);
    
    NSString *previousLetter = [text substringWithRange:rangeToSubstring];
    
    BOOL isColor = [colorLetters any:^BOOL(NSString *obj) {
        return [[NSString stringWithFormat:@" %@",obj] isEqualToString:previousLetter];
    }];
    
    return !isColor;
}

- (NSRange)correctRangeForLetter:(NSString *)letter inText:(NSString *)text withColorLetters:(NSArray *)colorLetters
{
    NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:
                                  [NSString stringWithFormat:@" %@ ", letter] options: 0 error:nil];
    
    NSArray *arrayOfRanges = [regex matchesInString:text options:0 range:NSMakeRange(0, [text length])];
    
    NSTextCheckingResult *correctnessCheck = [arrayOfRanges match:^BOOL(NSTextCheckingResult *obj) {
        return [self shouldLetterBeColored:obj.range inText:text withLetters:colorLetters];
    }];

    return correctnessCheck.range;
}

@end
