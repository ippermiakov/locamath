//
//  PopupOlympiadSolvedViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 09.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"

@interface PopupOlympiadSolvedViewController : PresentableViewController

@property (strong, nonatomic) IBOutlet UILabel *pointsLabel;

- (IBAction)onTapOkButton:(id)sender;

@end
