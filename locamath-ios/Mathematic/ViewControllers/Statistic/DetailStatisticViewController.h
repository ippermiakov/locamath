//
//  DetailStatisticViewController.h
//  Mathematic
//
//  Created by Developer on 18.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"

@interface DetailStatisticViewController : PresentableViewController <UITableViewDataSource, UITableViewDelegate>

@property (unsafe_unretained, nonatomic) TaskErrorType taskErrorType;
@property (strong, nonatomic) IBOutlet UILabel *errorLable;

- (IBAction)onTapCloseButton:(id)sender;

@end
