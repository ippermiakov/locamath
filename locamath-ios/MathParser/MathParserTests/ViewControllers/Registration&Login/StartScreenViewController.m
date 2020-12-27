//
//  RegistrationAndLoginViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 18.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StartScreenViewController.h"
#import "UIView+Transform.h"
#import "MBProgressHUD.h"
#import "DebugMode.h"
#import "AnimationManager.h"
#import "Parent.h"
#import "MBProgressHUD+Mathematic.h"
#import "Child.h"
#import "ChildManager.h"
#import "SettingsViewController.h"
#import "LoginOrRegisterViewController.h"
#import "MTHTTPClient.h"
#import "PrivacyPolicyPopupViewController.h"
#import "GameManager.h"
#import "Game.h"

@interface StartScreenViewController () 

//- (IBAction)onSkip:(id)sender;
- (IBAction)onNew:(id)sender;
- (IBAction)onContinue:(id)sender;
@property (strong, nonatomic) IBOutlet UIButton *skipButton;

@property (strong, nonatomic) IBOutlet UILabel *labelToRotate;
@property (weak, nonatomic) IBOutlet UIButton *deleteDebugAccountButton;
- (IBAction)onDeleteDebugAccount:(id)sender;
@property (strong, nonatomic) IBOutletCollection(UIView) NSArray *buttonsToAnimate;

@end

@implementation StartScreenViewController

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

#ifdef DEBUG
    self.skipButton.hidden = NO;
    self.deleteDebugAccountButton.hidden = NO;
#endif
    self.labelToRotate.rotation = 0.09;
    
    [[AnimationManager sharedInstance] playRegistrationAnimationsIfNeededWithViews:self.buttonsToAnimate];
}

- (void)viewDidAppear:(BOOL)animated
{
    if (!self.didViewAppear) {
        if (self.autoLoginUserInfo) {
            [GameManager sharedInstance].game.skipStatisticScreen = @NO;
            
            [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
            
            LoginOrRegisterViewController *loginViewcontroller = [LoginOrRegisterViewController new];
            loginViewcontroller.autoLoginUserInfo = self.autoLoginUserInfo;
            loginViewcontroller.isRegister = NO;
            loginViewcontroller.onFinish = ^{
                [self showStudyAndExercisesScreen];
            };
            
            [loginViewcontroller presentOnViewController:self finish:nil];
            
            self.autoLoginUserInfo = nil;
        }
    }
    
    [super viewDidAppear:animated];
}

- (void)viewDidUnload
{
    [self setLabelToRotate:nil];
    [self setSkipButton:nil];
    [self setDeleteDebugAccountButton:nil];
    [self setButtonsToAnimate:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Actions

//- (IBAction)onSkip:(id)sender
//{
//    [MBProgressHUD showHUDForWindow];
//    [DebugMode autoLoginWithSuccess:^{
//        [MBProgressHUD hideHUDForWindow];
//        [self showStudyAndExercisesScreen];
//        [[ChildManager sharedInstance] addDefaultChildToParentIfNeeded];
//    } failure:^(NSError *error) {
//        [MBProgressHUD hideHUDForWindow];
//        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
//    }];
//}

- (IBAction)onNew:(id)sender
{
    [[GameManager sharedInstance] logOffParent];
    [Parent truncateAll];
    
    [[ChildManager sharedInstance] createDefaultChildWithCompletion:^{
        [self showStudyAndExercisesScreen];
    }];
}

- (IBAction)onContinue:(id)sender
{
    if (![[MTHTTPClient sharedMTHTTPClient] isParentAuthentificated]) {
        
        PrivacyPolicyPopupViewController *privacyVC = [PrivacyPolicyPopupViewController new];
        
        privacyVC.onFinish = ^{
            [GameManager sharedInstance].game.skipStatisticScreen = @YES;
            
            [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
            
            LoginOrRegisterViewController *loginViewcontroller = [LoginOrRegisterViewController new];
            loginViewcontroller.isRegister = NO;
            loginViewcontroller.onFinish = ^{
                [self showStudyAndExercisesScreen];
            };
            
            [loginViewcontroller presentOnViewController:self finish:nil];
        };
        
        [privacyVC presentOnViewController:self finish:nil];

    } else {
        [self showStudyAndExercisesScreen];
    }
}


- (IBAction)onDeleteDebugAccount:(id)sender
{
    [MBProgressHUD showHUDForWindow];
    
    [[MTHTTPClient sharedMTHTTPClient] deleteAccountWithEmail:@"alexandr.butenko@gmail.com"
                                                      success:^(BOOL finished, NSError *error) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          [UIAlertView showAlertViewWithMessage:@"deleted debug account"];
                                                      }
                                                      failure:^(BOOL finished, NSError *error) {
                                                          [MBProgressHUD hideHUDForWindow];
                                                          [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                                      }];
}

#pragma mark - Helper

- (void)showStudyAndExercisesScreen
{
    [self dismissViewControllerAnimated:YES completion:nil];
}

@end
