//
//  LWRatingView.h
//  LocaWIFI
//
//  Created by Dmitriy Gubanov on 22.08.12.
//
//

#import <UIKit/UIKit.h>

@interface LWRatingView : UIView

@property(nonatomic, unsafe_unretained) NSUInteger  maxRating;
@property(nonatomic, unsafe_unretained) CGFloat     rating;
@property (strong, nonatomic) NSArray *tasks;

@property(nonatomic, strong) IBOutlet   UIImageView *fullStar;
@property(nonatomic, strong) IBOutlet   UIImageView *halfStar;
@property(nonatomic, strong) IBOutlet   UIImageView *emptyStar;

@end
