//
//  FLSegmentedButton.h
//  Flixa
//
//  Created by SanyaIOS on 05.06.13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <UIKit/UIKit.h>
typedef void (^ButtonPressActionHandler)(int buttonIndex);
typedef void (^ButtonPressActionHandlerWithBlocked)(int buttonIndex, BOOL bloked);

@interface FLSegmentedButton : UIView

@property (unsafe_unretained, nonatomic) NSUInteger selectedIndex;
@property (unsafe_unretained, nonatomic) BOOL shouldBeSelected; //YES by default, if NO - no reaction on selection
@property (unsafe_unretained, nonatomic) BOOL isBlocked;

- (void)initWithButtonsCollection:(NSArray *)buttons withHandler:(ButtonPressActionHandler)handler;
- (void)initWithImagesOrStrings:(NSArray *)buttonImagesOrStrings
               buttonTintNormal:(UIColor *)backgroundColorNormal
              buttonTintPressed:(UIColor *)backgroundColorPressed
                  actionHandler:(ButtonPressActionHandler)handler;
- (void)initWithButtonsCollection:(NSArray *)buttons
                     selectedTags:(NSNumber *)selectedTags
        withHandlerMultipleChoice:(ButtonPressActionHandlerWithBlocked)handler;

@end
