//
//  SSRatingPickerViewController.h
//  SSToolkit
//
//  Created by Sam Soffes on 2/3/11.
//  Copyright 2011 Sam Soffes. All rights reserved.
//

@class SSRatingPickerScrollView;
@class SSRatingPicker;
@class SSTextField;
@class SSTextView;

/**
 Creates a controller object that manages a rating picker.
 */
@interface SSRatingPickerViewController : UIViewController

///----------------------------------
/// @name Accessing the Rating Picker
///----------------------------------

/**
 The rating picker. (read-only)
 
 All of the rating picker's values are the default values of `SSRatingPicker`.
 */
@property (strong, nonatomic, readonly) SSRatingPicker *ratingPicker;


///-------------------------------
/// @name Accessing the Text Input
///-------------------------------

/**
 The text field for the title. (read-only)
 */
@property (strong, nonatomic, readonly) SSTextField *titleTextField;

/**
 The text view for the review.  (read-only)
 */
@property (strong, nonatomic, readonly) SSTextView *reviewTextView;

@end