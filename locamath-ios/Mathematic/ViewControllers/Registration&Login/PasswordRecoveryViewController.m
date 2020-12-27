//
//  PasswordRecoveryViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 23.07.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PasswordRecoveryViewController.h"
#import "MTHTTPClient.h"
#import "SSTextField.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"

@interface PasswordRecoveryViewController ()

@property (strong, nonatomic) IBOutlet SSTextField *userEmailTextField;
- (IBAction)onContinue:(id)sender;
- (IBAction)onBack:(id)sender;

@end

@implementation PasswordRecoveryViewController

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
    [self setUserEmailTextField:nil];
    [super viewDidUnload];
}

- (IBAction)onContinue:(id)sender
{
    [MBProgressHUD showHUDForWindow];

    [[MTHTTPClient sharedMTHTTPClient] resetPasswordWithEmail:self.userEmailTextField.text
                                                      success:^(BOOL finished, NSError *error) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          self.onEnd = nil;
                                                          [self dismiss];
                                                          [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Password was sent to your e-mail", nil)];
                                                      }
                                                      failure:^(BOOL finished, NSError *error) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          self.onEnd = nil;
                                                          [self dismiss];
                                                          [UIAlertView showAlertViewWithMessage:[error localizedDescription]];
                                                      }];
}

- (IBAction)onBack:(id)sender
{
    self.onEnd = nil;
    [self dismiss];
}

@end
