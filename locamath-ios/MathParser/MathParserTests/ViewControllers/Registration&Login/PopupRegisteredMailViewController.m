//
//  PopupRegisteredMailViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 18.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PopupRegisteredMailViewController.h"
#import "PasswordRecoveryViewController.h"
#import "MTHTTPClient.h"
#import "SSTextField.h"
#import "FLSegmentedButton.h"
#import "MBProgressHUD.h"
#import "GameAlertViewController.h"
#import "Parent.h"
#import "MBProgressHUD+Mathematic.h"
#import "BaseKeyboardAppearancePopupViewController.h"
#import "GameManager.h"
#import "UIAlertView+BlocksKit.h"
#import "ChildManager.h"

@interface PopupRegisteredMailViewController () <UITextFieldDelegate>

@property (strong, nonatomic) IBOutlet SSTextField *passwordConfirmationTextField;
@property (strong, nonatomic) IBOutlet SSTextField *emailTextField;
@property (strong, nonatomic) IBOutlet SSTextField *passwordTextField;
@property (strong, nonatomic) IBOutlet UIImageView *passwordComfirImage;
@property (strong, nonatomic) FLSegmentedButton *segmentedButton;
@property (strong, nonatomic) IBOutlet UILabel *labelText;

@property (strong, nonatomic) IBOutlet UIButton *passwordRecoveryButton;
@property (strong, nonatomic) Parent *parent;


- (IBAction)onContinue:(id)sender;
- (IBAction)onBack:(id)sender;
- (IBAction)onPasswordRecovery:(id)sender;

@end

@implementation PopupRegisteredMailViewController

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
    self.parent = [DataUtils currentParent];
    [self updateView];
}

- (void)viewDidAppear:(BOOL)animated
{
    if (!self.didViewAppear) {
        if (self.isAutoLogin) {
            self.emailTextField.text = self.email;
            self.passwordTextField.text = self.password;
            self.passwordConfirmationTextField.text = self.password;
            
            [self onContinue:nil];
            
            self.isAutoLogin = NO;
        }
    }
    
    [super viewDidAppear:animated];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setPasswordConfirmationTextField:nil];
    [self setPasswordComfirImage:nil];
    [self setLabelText:nil];
    [self setPasswordRecoveryButton:nil];
    [self setContinueButton:nil];
    [self setPasswordTextField:nil];
    [self setEmailTextField:nil];
    [self setPasswordTextField:nil];
    [super viewDidUnload];
}


#pragma mark - Actions

- (IBAction)onContinue:(id)sender
{
    if ([self.passwordTextField.text length] && [self.emailTextField.text length]) {
        if (self.isRegister) {
            if ([self.passwordConfirmationTextField.text length] == 0) {
                [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"You should input all rows", @"Mail registration  popup")];
            } else {
                if ([self.passwordConfirmationTextField.text isEqualToString:self.passwordTextField.text]) {
                    [MBProgressHUD showHUDForWindow];
                    
                    [[MTHTTPClient sharedMTHTTPClient] registerUserWithEmail:self.emailTextField.text
                                                                    password:self.passwordTextField.text
                                                                     success:^(BOOL finished, NSError *error) {
                                                                         //TODO: modify register on server side to return authorize user
                                                                         [self loginParentAfterRegistration:YES];
                    }
                                                                     failure:^(BOOL finished, NSError *error) {
                                                                         [self handleIfParentIsRegisteredWithError:error
                                                                                                       andUserInfo:@{kUserEmailKey: self.emailTextField.text,
                                                                                                                     kUserPasswordKey: self.passwordTextField.text}];
                    }];
                } else {
#warning not translated
                    [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Confirm_ERROR", nil)];
                }
            }
        } else {
            [self loginParentAfterRegistration:NO];
        }
        
    } else {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"You should input all rows", @"Mail registration  popup")];
    }
}

- (void)loginParentAfterRegistration:(BOOL)isAfterRegistration
{
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [MBProgressHUD showHUDForWindow];
        
        [[MTHTTPClient sharedMTHTTPClient] loginUserWithEmail:self.emailTextField.text
                                                     password:self.passwordTextField.text
                                        shouldSaveAccessToken:YES
                                                      success:^(NSDictionary *successResponseData) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          
                                                          Parent *parent = [DataUtils currentParent];
                                                          parent.city = successResponseData[@"city"];
                                                          parent.country = successResponseData[@"country"];
                                                          
                                                          //if email not equal - create new parent
                                                          if (![parent.email isEqualToString:self.emailTextField.text]) {
                                                              [Parent truncateAll];
                                                              Parent *parentNew  = [Parent createEntity];
                                                              parentNew.email = self.emailTextField.text;
                                                              parentNew.password = self.passwordTextField.text;
                                                              parentNew.city = successResponseData[@"city"];
                                                              parentNew.country = successResponseData[@"country"];
                                                          }
                                                          
                                                          [self dismissToRootViewController];

                                                          if (isAfterRegistration) {
                                                              [GameAlertViewController showGameAlertWithMessage:NSLocalizedString(@"You need to confirm your e-mail before continue", nil) withPresenter:GameManager.levelMap.view];
                                                              [GameAlertViewController sharedInstance].onFinish = self.onFinish;
                                                          } else {
                                                              if (self.onFinish) {
                                                                  self.onFinish();
                                                              }
                                                          }
                                                      }
                                                      failure:^(BOOL finished, NSError *error) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                                      }];
    } else {
        if ([self.parent.email isEqualToString:self.emailTextField.text]) {
            if ([self.parent isPasswordEqualToPassword:self.passwordTextField.text]) {
                
                [[MTHTTPClient sharedMTHTTPClient] addDefaultAccessToken];
                [self dismissToRootViewController];
                
                if (self.onFinish) {
                    self.onFinish();
                }
            } else {
                [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Incorect password", nil)];
            }
        } else {
            [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Incorect e-mail", nil)];
        }
    }
}

- (IBAction)onBack:(id)sender
{
    self.onEnd = nil;
    [self dismiss];
}

- (IBAction)onPasswordRecovery:(id)sender
{
    PasswordRecoveryViewController *passwordRecoveryViewController = [PasswordRecoveryViewController new];
    [passwordRecoveryViewController presentOnViewController:self
                                                     finish:^{
                                                         
                                                     }];
}

#pragma mark - UITextFieldDelegate

- (void)textFieldDidBeginEditing:(UITextField *)textField
{
    textField.textColor = [UIColor whiteColor];
 
}

#pragma mark - Helper

- (void)updateView
{
    self.passwordComfirImage.hidden = !self.isRegister;
    self.passwordConfirmationTextField.hidden = !self.isRegister;
    self.passwordRecoveryButton.hidden = self.isRegister;
    
    if (self.isRegister) {
        self.labelText.text = NSLocalizedString(@"Register", nil);
    } else self.labelText.text = NSLocalizedString(@"Login", nil);
}

@end
