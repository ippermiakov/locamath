//
//  PopUpController.h
//  Mathematic
//
//  Created by Developer on 04.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PopUpControllerDelegate.h"
#import "BaseViewController.h"
#import "TaskError.h"

@interface PopUpController : BaseViewController

@property (weak, nonatomic) id <PopUpControllerDelegate> delegate;
@property (strong, nonatomic) NSArray *errorActions;

@property (weak, nonatomic) IBOutlet UILabel *pointsLabel;
@property (weak, nonatomic) IBOutlet UITextView *textViewDescription;
@property (weak, nonatomic) IBOutlet UIButton *refreshButton;
@property (weak, nonatomic) IBOutlet UIButton *backButton;
@property (strong, nonatomic) IBOutlet UILabel *nextLabel;
@property (strong, nonatomic) IBOutlet UIButton *nextButton;
@property (unsafe_unretained, nonatomic) BOOL needShowOk;

- (IBAction)onTapOkButton:(id)sender;
- (IBAction)onTapRestoreButton:(id)sender;
- (IBAction)onTapHomeButton:(id)sender;
- (IBAction)onTapNextButton:(id)sender;

@end
