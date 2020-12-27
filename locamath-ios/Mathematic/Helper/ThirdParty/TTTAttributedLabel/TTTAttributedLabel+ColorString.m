//
//  TTTAttributedLabel+ColorString.m
//  Mathematic
//
//  Created by SanyaIOS on 21.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TTTAttributedLabel+ColorString.h"

@implementation TTTAttributedLabel (ColorString)

- (void)setColoredStrings:(NSArray*)colorStrings
{
    UIColor *color = [UIColor yellowColor];
    NSString *stringLabel = self.text;
    
    [self setText:stringLabel afterInheritingLabelAttributesAndConfiguringWithBlock: ^(NSMutableAttributedString *mutableAttributedString) {
        for (NSInteger i = 0; i<colorStrings.count;i++) {
            NSString *tempString = colorStrings[i];
            NSRange colorRange = [stringLabel rangeOfString:tempString];
            
            if (colorRange.length > 0) {
                [mutableAttributedString addAttribute:(NSString *) kCTForegroundColorAttributeName value:(id)color.CGColor range:colorRange];
            }
        }
        return mutableAttributedString;
    }];
}

@end
