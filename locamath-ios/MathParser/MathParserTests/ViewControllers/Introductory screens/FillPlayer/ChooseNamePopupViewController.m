//
//  ChooseNamePopupViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChooseNamePopupViewController.h"
#import "ChooseAvatarPopupViewController.h"
#import "ChildManager.h"
#import "Child.h"
#import "MTHTTPClient.h"

@interface ChooseNamePopupViewController ()
@property (strong, nonatomic) ChildManager *childManager;
@end

@implementation ChooseNamePopupViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.childManager = [ChildManager sharedInstance];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.nameTF.textColor = [UIColor registrationFormsTextColor];
    
    self.nameTF.text = self.childManager.currentChild.name.length > 0 ? self.childManager.currentChild.name : nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setNameTF:nil];
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onTapContinue:(id)sender
{
    NSError *error = nil;
    
    if (self.nameTF.text.length == 0) {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Please enter your name", @"Choose name popup")];
        return;
    }
    
    [MTHTTPClient validateNickname:self.nameTF.text withError:&error];
    if (error) {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString([error localizedDescription], nil)];
        return;
    }
    
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];

    [self.nameTF resignFirstResponder];
    
    if (self.shouldCreateNewChild) {
        if([[ChildManager sharedInstance] isParentHaveChildWithName:self.nameTF.text]) {
            [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Child with this name already exists", nil)];
            self.nameTF.text = nil;
        } else {
            
            if (nil != [ChildManager sharedInstance].currentChild) {
                [ChildManager sharedInstance].currentChild.name = self.nameTF.text;
            }
            
            [[ChildManager sharedInstance] createChildWithName:self.nameTF.text
                                                       success:^{
                                  [self presentNextViewController];
                                  
                                  [[ChildManager sharedInstance] updateChildWithSuccess:^{
                                      NSLog(@"update success!!!");
                                  } failure:^(NSError *error) {
                                      NSLog(@"update failure with error : %@",[error localizedDescription] );
                                  }];
                              } failure:^(NSError *error) {
                                   NSLog(@"update failure with error : %@", [error localizedDescription]);
            }];
        }
    } else {
        [ChildManager sharedInstance].currentChild.name = self.nameTF.text;
        [self presentNextViewController];
    }
}

//- (void)prepareForTransition
//{
//    [ChildManager sharedInstance].currentChild.name = self.nameTF.text;
//}

- (void)dismiss
{
    if ([self.parentVC respondsToSelector:@selector(didEditChild:)]) {
        [self.parentVC didEditChild:[ChildManager sharedInstance].currentChild];
    }
    [super dismiss];
}

#pragma mark - UITextFieldDelegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    [textField resignFirstResponder];
    return NO;
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string
{
    if (textField.text.length + string.length > 15) {
        return NO;
    } else {
        return YES;
    }
}

@end
