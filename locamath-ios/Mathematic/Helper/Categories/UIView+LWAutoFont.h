//
//  UIView+LWAutoFont.h
//  LocaWIFI
//
//  Created by Dmitriy Gubanov on 21.08.12.
//
//

#import <UIKit/UIKit.h>

@interface UIView (LWAutoFont)

+ (NSString *)defaultFontName;
+ (NSString *)defaultBoldFontName;

- (void)setActualFonts;

@end
