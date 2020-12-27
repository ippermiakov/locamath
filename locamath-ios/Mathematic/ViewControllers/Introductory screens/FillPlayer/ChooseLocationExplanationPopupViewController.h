//
//  ChooseLocationPopupViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"
#import <CoreLocation/CoreLocation.h>

@interface ChooseLocationExplanationPopupViewController : PresentableViewController <CLLocationManagerDelegate>

- (IBAction)onTapContinue:(id)sender;

@end
