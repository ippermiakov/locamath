//
//  SSRatingPickerScrollView.h
//  SSToolkit
//
//  Created by Sam Soffes on 2/4/11.
//  Copyright 2011 Sam Soffes. All rights reserved.
//

@class SSGradientView;
@class SSRatingPicker;
@class SSTextField;
@class SSTextView;

@interface SSRatingPickerScrollView : UIScrollView <UITextViewDelegate>

@property (strong, nonatomic, readonly) SSRatingPicker *ratingPicker;
@property (strong, nonatomic, readonly) SSTextField *titleTextField;
@property (strong, nonatomic, readonly) SSTextView *reviewTextView;

@end
