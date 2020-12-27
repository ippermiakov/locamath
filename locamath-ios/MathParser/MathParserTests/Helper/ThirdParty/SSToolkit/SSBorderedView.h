//
//  SSBorderedView.h
//  SSToolkit
//
//  Created by Sam Soffes on 6/17/11.
//  Copyright 2011 Sam Soffes. All rights reserved.
//

/**
 Simple UIView for drawing top and bottom borders with optional insets in a view.
 */
@interface SSBorderedView : UIView

///-----------------------------
/// @name Drawing the Top Border
///-----------------------------

/**
 The top border color. The default is `nil`.
 
 @see bottomBorderColor
 */
@property (strong, nonatomic) UIColor *topBorderColor;

/**
 The top inset color. The default is `nil`.
 
 @see bottomInsetColor
 */
@property (strong, nonatomic) UIColor *topInsetColor;


///--------------------------------
/// @name Drawing the Bottom Border
///--------------------------------

/**
 The bottom inset color. The default is `nil`.
 
 @see topInsetColor
 */
@property (strong, nonatomic) UIColor *bottomInsetColor;

/**
 The bottom border color. The default is `nil`.
 
 @see topBorderColor
 */
@property (strong, nonatomic) UIColor *bottomBorderColor;

@end
