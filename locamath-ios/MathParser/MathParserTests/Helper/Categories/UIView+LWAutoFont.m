//
//  UIView+LWAutoFont.m
//  LocaWIFI
//
//  Created by Dmitriy Gubanov on 21.08.12.
//
//

#import "UIView+LWAutoFont.h"
#import <objc/message.h>

@implementation UIView (LWAutoFont)

+ (NSString *)defaultFontName
{
    NSString * language = [[NSLocale preferredLanguages] objectAtIndex:0];
    NSString *fontName = nil;
    
    if ([language isEqualToString:@"ru"]) {
        fontName = @"FloraC";
    } else {
        fontName = @"ITCFlora-Medium";
    }
    
    return fontName;
}

+ (NSString *)defaultBoldFontName
{
    NSString * language = [[NSLocale preferredLanguages] objectAtIndex:0];
    NSString *fontName = nil;
    
    if ([language isEqualToString:@"ru"]) {
        fontName = @"FloraC-Bold";
    } else {
        fontName = @"FloraLT-Bold";
    }
    
    return fontName;
}

- (NSString *)fontNameForString:(NSString *)string
{
    NSArray *components = [string componentsSeparatedByString:@"<F/>"];
    if(components.count > 1)
        return [components objectAtIndex:0];
    else
        return nil;
}

- (NSString *)clearNameForString:(NSString *)string
{
    NSArray *components = [string componentsSeparatedByString:@"<F/>"];
    if(components.count > 1)
        return [components objectAtIndex:1];
    else
        return [components objectAtIndex:0];
}

- (BOOL)isBoldFont:(UIFont *)font
{
    //getting font weight, because no clear way to do this
    return NSNotFound != [[font description] rangeOfString:@"bold" options:NSCaseInsensitiveSearch].location;
}

- (void)setActualFontsPlain
{
    if([self isKindOfClass:[UIButton class]])
    {
        UIButton *selfBtn = (UIButton*)self;
        
        NSString *title      = [selfBtn titleForState:UIControlStateNormal];
        NSString *fontName   = [self fontNameForString:title];
        
        UIFont *oldFont      = selfBtn.titleLabel.font;

        if(fontName == nil) {
            if ([self isBoldFont:oldFont]) {
                fontName = [UIView defaultBoldFontName];
            }
            else {
	            fontName = [UIView defaultFontName];
            }
        }
        
        UIFont *newFont      = [UIFont fontWithName:fontName size:oldFont.pointSize];
        
        [selfBtn.titleLabel setFont:newFont];
        
        
        [selfBtn setTitle:[self clearNameForString:title] forState:UIControlStateNormal];
    }
  
    else if([self isKindOfClass:[UILabel class]] &&
            //HACK: otherwise it will crack textfields drawing for iOS 7 (no idea, SDK imp details)
            ![NSStringFromClass([self class]) isEqualToString:@"UITextFieldLabel"] &&
            ![NSStringFromClass([self class]) isEqualToString:@"UITextFieldCenteredLabel"])
    {
//        NSLog(@"class: %@ text: %@", NSStringFromClass([self class]), [self performSelector:@selector(text)]);        
        
        UILabel *selfLbl = (UILabel *)self;
        NSString *title      = [selfLbl text];
        
        NSString *fontName   = [self fontNameForString:title];
        UIFont *oldFont      = selfLbl.font;

        if (fontName == nil) {
            if ([self isBoldFont:oldFont]) {
                fontName = [UIView defaultBoldFontName];
            }
            else {
	            fontName = [UIView defaultFontName];
            }
        }
        
        UIFont *newFont = [UIFont fontWithName:fontName size:oldFont.pointSize];
        
        [selfLbl setFont:newFont];
        [selfLbl setText:[self clearNameForString:title]];
    }
    else if([self isKindOfClass:[UITextView class]])
    {
        UITextView *selfTxVw = (UITextView*)self;       
        NSString *title      = [selfTxVw text];
        NSString *fontName   = [self fontNameForString:title];
        UIFont *oldFont      = selfTxVw.font;
        
        if(fontName == nil) {
            if ([self isBoldFont:oldFont]) {
                fontName = [UIView defaultBoldFontName];
            }
            else {
	            fontName = [UIView defaultFontName];
            }
        }
        
        UIFont *newFont      = [UIFont fontWithName:fontName size:oldFont.pointSize];
        [selfTxVw setFont:newFont];
        [selfTxVw setText:[self clearNameForString:title]];        
    }
    else if([self isKindOfClass:[UITableViewCell class]]) {
        UITableViewCell *selfCell = (UITableViewCell*)self;
        [selfCell.textLabel setActualFontsPlain];
    }
}

- (void)willMoveToSuperview:(UIView *)newSuperview
{
    if ([newSuperview isKindOfClass:[UIAlertView class]]) {
        return;
    }
    
    [self setActualFontsPlain];
}

- (void)setActualFonts
{
    [self setActualFontsPlain];
    [self.subviews makeObjectsPerformSelector:_cmd];
}

@end
