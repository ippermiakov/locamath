//
//  PasswordForLoginViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 03.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PasswordForLoginViewController.h"
#import "SSTextField.h"
#import "UIAlertView+Error.h"
#import "MTHTTPClient.h"
#import "MBProgressHUD.h"
#import "Parent.h"
#import "MBProgressHUD+Mathematic.h"

@interface PasswordForLoginViewController ()

@property (strong, nonatomic) IBOutlet SSTextField *passwordTextField;
@property (strong, nonatomic) IBOutlet SSTextField *confirmPassTextField;

- (IBAction)onContinue:(id)sender;
- (IBAction)onBack:(id)sender;

@end

@implementation PasswordForLoginViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setPasswordTextField:nil];
    [self setConfirmPassTextField:nil];
    [super viewDidUnload];
}

- (IBAction)onContinue:(id)sender
{
    if ([self.passwordTextField.text length] == 0 || [self.confirmPassTextField.text length] == 0) {
        [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"You should input all rows", @"Mail registration  popup")];
    } else if (![self.passwordTextField.text isEqualToString:self.confirmPassTextField.text]) {
        [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Password and confirm must be same", nil)];
    } else {
        [MBProgressHUD showHUDForWindow];
        
        [[MTHTTPClient sharedMTHTTPClient] changePasswordWithEmail:self.userEmail
                                                   currentPassword:@"111111"                                                      newPassword:self.passwordTextField.text
                                                           success:^(BOOL finished, NSError *error) {
           [MBProgressHUD hideHUDForWindow];
           [self createParent];
           //[UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Password applied", nil)];
           [self dismiss];
           if (self.onFinish) {
               self.onFinish();
           }
                                                           }
                                                           failure:^(BOOL finished, NSError *error) {
           [MBProgressHUD hideHUDForWindow];
           [UIAlertView showAlertViewWithMessage:[error localizedDescription]];
                                                           }];
    }
}

- (IBAction)onBack:(id)sender
{
    self.onEnd = nil;
    [self dismiss];
}

- (void)createParent
{
    [Parent truncateAll];
    Parent *parentNew  = [Parent createEntity];
    parentNew.email = self.userEmail;
    parentNew.password = self.passwordTextField.text;
}

@end
