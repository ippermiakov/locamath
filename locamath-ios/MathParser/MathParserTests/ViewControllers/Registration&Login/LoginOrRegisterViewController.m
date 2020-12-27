//
//  LoginViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 15.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "LoginOrRegisterViewController.h"
#import "PrivacyPolicyPopupViewController.h"
#import "MBProgressHUD+Mathematic.h"
#import "FacebookSDK.h"
#import "SocialHTTPClient.h"
#import "MTHTTPClient.h"
#import "Parent.h"
#import "Child.h"
#import "ChildManager.h"
#import "UIAlertView+Error.h"
#import "PasswordForLoginViewController.h"
#import "PopupForDefaultChildViewController.h"
#import "PopupRegisteredMailViewController.h"
#import "PopupForDefaultChildViewController.h"

static NSString * const kRegisteredViaMail = @"isRegisteredViaMail";
static NSString * const kPasswordHash = @"passwordHash";

@interface LoginOrRegisterViewController ()

- (IBAction)onFbLogin:(id)sender;
- (IBAction)onMailLogin:(id)sender;
- (IBAction)onBack:(id)sender;
@property (weak, nonatomic) IBOutlet UILabel *titleLabel;

@end

@implementation LoginOrRegisterViewController

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
    
    self.titleLabel.text = self.isRegister ? NSLocalizedString(@"Register", nil) : NSLocalizedString(@"Login", nil);
}

- (void)viewDidAppear:(BOOL)animated
{
    if (!self.didViewAppear) {
        if (self.autoLoginUserInfo[kSocialIDKey]) {
            [self FBLoginOrRegisterWithEmail:self.autoLoginUserInfo[kUserEmailKey]
                                    socialID:self.autoLoginUserInfo[kSocialIDKey]];
        } else if (self.autoLoginUserInfo[kUserPasswordKey]) {
            [self emailLoginWithEmail:self.autoLoginUserInfo[kUserEmailKey]
                             password:self.autoLoginUserInfo[kUserPasswordKey]];
        }
        
        //handle once
        self.autoLoginUserInfo = nil;
    }
    
    [super viewDidAppear:animated];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)onFbLogin:(id)sender
{
    if (![[MTHTTPClient sharedMTHTTPClient] isReachable]) {
#warning fix!!!
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"No internet connection, вы не можете зарегистрироваться через FB при отсутствии интернета, зарегистрируйтесь при помощи почты", nil)];
    } else {
//        PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//        check.shouldPassDefaultChildCheck = YES;
//        check.onFinish = ^{
            SocialRegistrationCompletionBlock registrationBlock = ^(NSString *userEmail, NSString *socialID, SocialType idType) {
                [self FBLoginOrRegisterWithEmail:userEmail socialID:socialID];
            };
            
            [SocialHTTPClient registerViaFBWithSuccess:registrationBlock
                                               failure:^(BOOL finished, NSError *error) {
                                                [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                               }];
//        };
//        
//        [check presentOnViewController:self finish:nil];
    }
}

- (void)FBLoginOrRegisterWithEmail:(NSString *)userEmail socialID:(NSString *)socialID
{
    [MBProgressHUD showHUDForWindow];
    
    if (self.isRegister) {
        [[MTHTTPClient sharedMTHTTPClient] registerWithSocialID:socialID
                                                          email:userEmail
                                                        success:^(NSDictionary *successResponseData) {
                                                            [MBProgressHUD hideHUDForWindow];
                                                            
                                                            PasswordForLoginViewController *passwordScreen = [PasswordForLoginViewController new];
                                                            passwordScreen.userEmail = userEmail;
                                                            
                                                            passwordScreen.onFinish = ^{
                                                                [self createParentAndHideWithEmail:userEmail
                                                                                          password:successResponseData[kPasswordHash]
                                                                                           andData:successResponseData];
                                                            };
                                                            
                                                            [passwordScreen presentOnViewController:self finish:nil];
                                                        }
                                                        failure:^(BOOL finished, NSError *error) {
                                                            [self handleIfParentIsRegisteredWithError:error
                                                                                          andUserInfo:@{kSocialIDKey: socialID,
                                                                                                        kUserEmailKey: userEmail}];
                                                        }];
    } else {
        [[MTHTTPClient sharedMTHTTPClient] loginUserWithSocialID:socialID
                                                         success:^(NSDictionary *successResponseData) {
                                                             [MBProgressHUD hideHUDForWindow];
                                                             [self createParentAndHideWithEmail:userEmail
                                                                                       password:successResponseData[kPasswordHash]
                                                                                        andData:successResponseData];
                                                         }
                                                         failure:^(BOOL finished, NSError *error) {
                                                             [MBProgressHUD hideHUDForWindow];
                                                             [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                                         }];
    }
}

- (IBAction)onMailLogin:(id)sender
{
//    PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//    check.shouldPassDefaultChildCheck = NO;
//    check.onFinish = ^{
        [self emailLoginWithEmail:nil password:nil];
//    };
//    [check presentOnViewController:self finish:nil];
}

- (void)emailLoginWithEmail:(NSString *)email password:(NSString *)password
{
    PopupRegisteredMailViewController *registerViewController = [PopupRegisteredMailViewController new];
    registerViewController.isRegister = self.isRegister;
    
    registerViewController.isAutoLogin = (email && password);
    registerViewController.email = email;
    registerViewController.password = password;
    
    registerViewController.onFinish = ^{
        if (self.onFinish) {
            self.onFinish();
        }
//        [[ChildManager sharedInstance] addDefaultChildToParentIfNeeded];
    };
    
    [registerViewController presentOnViewController:self finish:nil];
}

- (IBAction)onBack:(id)sender
{
    self.onEnd = nil;
    if (self.isRegister) {
        self.isViewUnloadingLocked = YES;
        
        PopupForDefaultChildViewController *popUpForDefaultChild = [PopupForDefaultChildViewController new];
        popUpForDefaultChild.popupType = kPopupForDefaultTypeHome;
        
        popUpForDefaultChild.onFinish = ^{
            [(PresentableViewController *)self dismiss];
        };
        
        [popUpForDefaultChild presentOnViewController:self finish:nil];
    } else {
        [self dismiss];
    }
}

- (void)createParentWithEmail:(NSString *)email password:(NSString *)password andData:(NSDictionary *)data
{
    [Parent truncateAll];
    Parent *parentNew  = [Parent createEntity];
    parentNew.email = email;
    parentNew.city = data[@"city"];
    parentNew.country = data[@"country"];
    [parentNew setPasswordString:password];
}

- (void)createParentAndHideWithEmail:(NSString *)email password:(NSString *)password andData:(NSDictionary *)data
{
    [self createParentWithEmail:email password:password andData:data];
    
    [self dismiss];
    
    if (self.onFinish) {
        self.onFinish();
    }
}

@end
