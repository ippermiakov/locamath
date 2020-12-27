//
//  FLSegmentedButton.m
//  Flixa
//
//  Created by SanyaIOS on 05.06.13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "FLSegmentedButton.h"
#import <QuartzCore/QuartzCore.h>
#import "NSArray+SortViewsByXOrigin.h"

@interface FLSegmentedButton()

@property (strong, nonatomic) NSArray *segmentedButtons;
@property (copy, nonatomic) ButtonPressActionHandler buttonHandler;
@property (copy, nonatomic) ButtonPressActionHandlerWithBlocked buttonHandlerWithBlocked;
@property (strong, nonatomic) UIColor *buttonBackgroundColorForStateNormal;
@property (strong, nonatomic) UIColor *buttonBackgroundColorForStatePressed;
@property (strong, nonatomic) NSMutableArray *buttons;
@property (strong, nonatomic) UIButton *lastSelectedButton;

@end

@implementation FLSegmentedButton

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        self.shouldBeSelected = YES;
        self.selectedIndex = 0;
    }
    return self;
}

- (void)dealloc
{
    //remove all actions linked to |self|
    [self.segmentedButtons each:^(UIButton *button) {
        [button removeTarget:self action:NULL forControlEvents:UIControlEventTouchUpInside];
    }];
}

//multiple Choice
- (void)initWithButtonsCollection:(NSArray *)buttons
                     selectedTags:(NSNumber *)selectedTags
        withHandlerMultipleChoice:(ButtonPressActionHandlerWithBlocked)handler
{
    self.segmentedButtons = [buttons sortedArrayByYOrigin];
    
    __weak FLSegmentedButton *weakSelf = self;
    
    [self.segmentedButtons enumerateObjectsUsingBlock:^(UIButton *button, NSUInteger idx, BOOL *stop) {
        button.selected = ([selectedTags integerValue] >> button.tag) & 1;
//        NSLog(@"selectedTags: %@ button.tag: %i selected: %@", selectedTags, button.tag, button.selected ? @"YES":@"NO");
     
        if (button.tag == 0) button.tag = idx;
        
        [button addTarget:weakSelf action:@selector(multipleButtonPressed:) forControlEvents:UIControlEventTouchUpInside];
    }];
    
    self.buttonHandlerWithBlocked = handler;
}

- (void)initWithButtonsCollection:(NSArray *)buttons withHandler:(ButtonPressActionHandler)handler
{
    self.segmentedButtons = [buttons sortedArrayByYOrigin];
    
    [self.segmentedButtons enumerateObjectsUsingBlock:^(UIButton *button, NSUInteger idx, BOOL *stop) {
        //select first button in collection
        if (idx == self.selectedIndex) {
            button.selected = YES;
        }
        
        if (button.tag == 0) button.tag = idx;
        
        [button addTarget:self action:@selector(buttonPressed:) forControlEvents:UIControlEventTouchUpInside];
    }];
    
    self.buttonHandler = handler;
}


- (void)initWithImagesOrStrings:(NSArray *)buttonImagesOrStrings
               buttonTintNormal:(UIColor *)backgroundColorNormal
              buttonTintPressed:(UIColor *)backgroundColorPressed
                  actionHandler:(ButtonPressActionHandler)handler
{
    self.buttons = [NSMutableArray new];
    int numberOfButtons = [buttonImagesOrStrings count];
	self.selectedIndex = 0;
    
    for (int i = 0; i < numberOfButtons; i++) {
        UIButton *newButton = [UIButton buttonWithType:UIButtonTypeCustom];
        newButton.frame = CGRectMake(i * (self.frame.size.width/numberOfButtons), 0, self.frame.size.width/numberOfButtons, self.frame.size.height);
        newButton.layer.bounds = CGRectMake(0, 0, self.frame.size.width/numberOfButtons, self.frame.size.height);
        newButton.layer.borderWidth = .5;
        newButton.layer.borderColor = [UIColor colorWithWhite:.6 alpha:1].CGColor;
        newButton.backgroundColor = backgroundColorNormal;
        newButton.clipsToBounds = YES;
        newButton.layer.masksToBounds = YES;
        
         [newButton addTarget:self action:@selector(buttonPressed:event:) forControlEvents:UIControlEventTouchUpInside];
        
		id instance = buttonImagesOrStrings[i];
        
		if ([instance isKindOfClass:[UIImage class]]) {
			[newButton setImage:instance forState:UIControlStateNormal];
			[newButton setImage:[self grayishImage:instance] forState:UIControlStateSelected];
			if (i == 0) {
				self.lastSelectedButton = newButton;
				self.lastSelectedButton.selected = YES;
			}
            
		} else if ([instance isKindOfClass:[NSString class]]) {
			newButton.titleLabel.text = instance;
			[newButton setTitle:instance forState:UIControlStateNormal];
			newButton.titleLabel.font = [UIFont boldSystemFontOfSize:22];
			
			const CGFloat *componentColors = CGColorGetComponents(backgroundColorNormal.CGColor);
			CGFloat colorBrightness = ((componentColors[0] * 299) + (componentColors[1] * 587) + (componentColors[2] * 114)) / 1000;
			if (colorBrightness < 0.5) {
				[newButton setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
				[newButton setTitleShadowColor:[UIColor grayColor] forState:UIControlStateNormal];
			} else {
				[newButton setTitleColor:[UIColor colorWithWhite:.2 alpha:1] forState:UIControlStateNormal];
				[newButton setTitleShadowColor:[UIColor whiteColor] forState:UIControlStateNormal];
			}
		}
        
        
        [self addSubview:newButton];
        [self.buttons addObject:newButton];
        self.buttonBackgroundColorForStateNormal = backgroundColorNormal;
        self.buttonBackgroundColorForStatePressed = backgroundColorPressed;
    }
    
    self.layer.cornerRadius = 10;
    self.layer.borderColor = [UIColor colorWithWhite:.6 alpha:1].CGColor;
    self.layer.borderWidth = 1;
    self.clipsToBounds = YES;
    self.buttonHandler = [handler copy];
}

#pragma mark - Actions

- (void)buttonPressed:(UIButton *)button
{
    self.buttonHandler(button.tag);
    
    if (self.shouldBeSelected) {
        [self.segmentedButtons enumerateObjectsUsingBlock:^(UIButton *buttonSegment, NSUInteger idx, BOOL *stop) {
            if (buttonSegment.tag == button.tag) {
                buttonSegment.selected = YES;
                
            } else {
                buttonSegment.selected = NO;
            }
        }];
    }
}

- (void)multipleButtonPressed:(UIButton *)button
{
    if (!self.isBlocked) {
        button.selected = !button.isSelected;
    }
    
    self.buttonHandlerWithBlocked(button.tag, self.isBlocked);
}

- (void)buttonPressed:(id)aButton event:(id)event
{
    if (self.buttonHandler) {
        for (int i = 0; i < [self.buttons count]; i++) {
            if (aButton == self.buttons[i]) {
				self.selectedIndex = i;
                self.buttonHandler(i);
                [aButton setBackgroundColor:self.buttonBackgroundColorForStatePressed];
            }
        }
    }
    
	if (self.lastSelectedButton != nil && aButton != self.lastSelectedButton) {
		[self.lastSelectedButton setBackgroundColor:self.buttonBackgroundColorForStateNormal];
		self.lastSelectedButton.selected = NO;
	}
    
	self.lastSelectedButton = aButton;
	self.lastSelectedButton.selected = YES;
}

#pragma mark - Helper

- (UIImage *)grayishImage:(UIImage *)inputImage
{
    // Create a graphic context.
    UIGraphicsBeginImageContextWithOptions(inputImage.size, YES, 1.0);
    CGRect imageRect = CGRectMake(0, 0, inputImage.size.width, inputImage.size.height);
	CGContextRef ctx = UIGraphicsGetCurrentContext();
	
    // Draw a white background
    CGContextSetRGBFillColor(ctx, 169.0/255.0, 125.0/255.0, 21.0/255.0, 1.0f);
    CGContextFillRect(ctx, imageRect);
    // Draw the image with the luminosity blend mode.
    // On top of a white background, this will give a black and white image.
    [inputImage drawInRect:imageRect blendMode:kCGBlendModeLuminosity alpha:1.0];
	
    // Get the resulting image.
    UIImage *filteredImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
	
    return filteredImage;
}

@end
