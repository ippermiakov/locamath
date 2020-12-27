//
//  SSTextField.m
//  SSToolkit
//
//  Created by Sam Soffes on 3/11/10.
//  Copyright 2010-2011 Sam Soffes. All rights reserved.
//

#import "SSTextField.h"
#import "SSDrawingUtilities.h"

@interface SSTextField ()
- (void)_initialize;

@property (strong, nonatomic) NSString *tempPlaceholder;

@end

@implementation SSTextField

#pragma mark - Accessors

@synthesize textEdgeInsets = _textEdgeInsets;
@synthesize placeholderTextColor = _placeholderTextColor;

- (void)setPlaceholderTextColor:(UIColor *)placeholderTextColor {
	_placeholderTextColor = placeholderTextColor;

    //http://stackoverflow.com/questions/1340224/iphone-uitextfield-change-placeholder-text-color
    [self setValue:_placeholderTextColor forKeyPath:@"_placeholderLabel.textColor"];
}


#pragma mark - UIView

- (id)initWithCoder:(NSCoder *)aDecoder {
    if ((self = [super initWithCoder:aDecoder])) {
        [self _initialize];
    }
    return self;
}


- (id)initWithFrame:(CGRect)frame {
    if ((self = [super initWithFrame:frame])) {
        [self _initialize];
    }
    return self;
}

- (void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

#pragma mark - UITextField

- (CGRect)textRectForBounds:(CGRect)bounds {
	return UIEdgeInsetsInsetRect([super textRectForBounds:bounds], _textEdgeInsets);
}

#pragma mark - Private

- (void)_initialize {
	_textEdgeInsets = UIEdgeInsetsZero;

    self.placeholderTextColor = [UIColor colorWithRed:1 green:1 blue:1 alpha:0.8f];
    
    //http://stackoverflow.com/questions/16882652/how-do-i-invoke-drawplaceholderinrect
    [[NSNotificationCenter defaultCenter] addObserverForName:UITextFieldTextDidBeginEditingNotification object:self queue:[NSOperationQueue mainQueue] usingBlock:^(NSNotification *note) {
        self.tempPlaceholder = self.placeholder;
        self.placeholder = @"";
    }];
    
    [[NSNotificationCenter defaultCenter] addObserverForName:UITextFieldTextDidEndEditingNotification object:self queue:[NSOperationQueue mainQueue] usingBlock:^(NSNotification *note) {
        self.placeholder = self.tempPlaceholder;
    }];
}

@end
