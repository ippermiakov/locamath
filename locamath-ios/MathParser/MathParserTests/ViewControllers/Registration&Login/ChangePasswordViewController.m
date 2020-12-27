//
//  ChangePasswordViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 27.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChangePasswordViewController.h"
#import "SSTextField.h"
#import "MTHTTPClient.h"
#import "MBProgressHUD.h"
#import "Parent.h"
#import "MBProgressHUD+Mathematic.h"

@interface ChangePasswordViewController ()

@property (strong, nonatomic) IBOutlet SSTextField *passwordTextField;
@property (strong, nonatomic) IBOutlet SSTextField *changedPasswordTextField;
@property (strong, nonatomic) IBOutlet SSTextField *confirmChangedPasswordTextField;
@property (weak, nonatomic) IBOutlet SSTextField *emailTextField;

- (IBAction)onContinue:(id)sender;
- (IBAction)onTapBack:(id)sender;

@end

@implementation ChangePasswordViewController

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

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setPasswordTextField:nil];
    [self setChangedPasswordTextField:nil];
    [self setConfirmChangedPasswordTextField:nil];
    [self setEmailTextField:nil];
    [super viewDidUnload];
}

- (IBAction)onContinue:(id)sender
{
    if (![self.changedPasswordTextField.text isEqualToString:self.confirmChangedPasswordTextField.text]) {
        [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Passwords don't match", nil)];
    } else {
        [MBProgressHUD showHUDForWindow];
        
        [[MTHTTPClient sharedMTHTTPClient] changePasswordWithEmail:self.emailTextField.text
                                                   currentPassword:self.passwordTextField.text
                                                       newPassword:self.changedPasswordTextField.text
                                                           success:^(BOOL finished, NSError *error) {
                                                                                                                              
                                                               [[DataUtils currentParent] setPassword:self.changedPasswordTextField.text];
                                                               [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                                                               
                                                               [MBProgressHUD hideHUDForWindow];
                                                               [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Password is changed", nil)];
                                                               [self dismiss];
                                                           }
                                                           failure:^(BOOL finished, NSError *error) {
                                                               [MBProgressHUD hideHUDForWindow];
                                                               [UIAlertView showAlertViewWithMessage:[error localizedDescription]];
                                                           }];
    }
}

- (IBAction)onTapBack:(id)sender
{
    [self dismiss];
}

@end
