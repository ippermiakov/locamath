//
//  OlympiadViewController.h
//  Mathematic
//
//  Created by Developer on 11.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"

@class LWRatingView;

@interface OlympiadViewController : BaseViewController

@property (strong, nonatomic) IBOutletCollection(LWRatingView) NSArray *stars;

- (IBAction)onTapHomeButton:(id)sender;
- (IBAction)onTapCupButton:(id)sender;

@end
