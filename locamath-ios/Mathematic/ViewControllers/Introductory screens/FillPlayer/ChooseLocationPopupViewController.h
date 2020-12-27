//
//  ChooseLocationPopupViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"
#import <CoreLocation/CoreLocation.h>

@protocol ChooseLocationPopupViewControllerDelegate <NSObject>
- (void)didSelectedRegion;
@end


@interface ChooseLocationPopupViewController : PresentableViewController <CLLocationManagerDelegate>

@property (strong, nonatomic) IBOutlet UILabel *country;
@property (strong, nonatomic) IBOutlet UILabel *city;

@property (weak, nonatomic)   BaseViewController<ChooseLocationPopupViewControllerDelegate> *parentVC;

- (IBAction)onTapCountry:(id)sender;
- (IBAction)onTapContinue:(id)sender;

@end
