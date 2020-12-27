//
//  SettingsViewController.m
//  Mathematic
//
//  Created by Developer on 10.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SettingsViewController.h"
#import "ChooseNamePopupViewController.h"
#import "AddAccountPopupViewController.h"
#import "ChooseLocationPopupViewController.h"
#import "ChooseAvatarPopupViewController.h"
#import "Child.h"
#import "Parent.h"
#import "ChildManager.h"
#import "ChooseAvatarPopupViewController.h"
#import "PresentingSeguesStructure.h"
#import "ChooseNamePopupViewController.h"
#import "ChooseAvatarPopupViewController.h"
#import "ChooseLocationPopupViewController.h"
#import "FLSegmentedButton.h"
#import "MBProgressHUD.h"
#import "LogoutAlertViewController.h"
#import "ChangePasswordViewController.h"
#import "MBProgressHUD+Mathematic.h"
#import "NSNumber+BitsOperations.h"
#import "PrivacyPolicyPopupViewController.h"
#import "UIViewController+DismissViewController.h"

@interface SettingsViewController ()<ChooseNamePopupViewControllerDelegate,
                                     ChooseAvatarPopupViewControllerDelegate,
                                     ChooseLocationPopupViewControllerDelegate>

@property (strong, nonatomic) ChildManager *childManager;

@property (strong, nonatomic) FLSegmentedButton * segmentedSoundsButton;
@property (strong, nonatomic) FLSegmentedButton * segmentedMusicButton;
@property (strong, nonatomic) FLSegmentedButton * segmentedFBButton;
@property (strong, nonatomic) FLSegmentedButton * segmentedMailButton;

@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *soundButtonsCollection;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *musicButtonsCollection;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *fbPostButtonsCollection;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *mailSendButtonsCollection;

@property (weak, nonatomic) IBOutlet UIImageView *avatar;
@property (strong, nonatomic) IBOutlet UILabel *username;
@property (weak, nonatomic) IBOutlet UILabel *userLabel;
@property (weak, nonatomic) IBOutlet UILabel *cityLabel;
@property (weak, nonatomic) IBOutlet UILabel *countryLabel;

@property (unsafe_unretained, nonatomic) BOOL isFBSettingsBlocked;
@property (unsafe_unretained, nonatomic) BOOL isMailSettingsBlocked;

- (IBAction)onTapBack:(id)sender;
- (IBAction)onTapNameEdit:(id)sender;
- (IBAction)onTapFacebook:(id)sender;
- (IBAction)onTapMail:(id)sender;
- (IBAction)onTapCity:(id)sender;
- (IBAction)onTapCountry:(id)sender;
- (IBAction)onTapLogoff:(id)sender;
- (IBAction)onTapUser:(id)sender;
- (IBAction)onChangePassword:(id)sender;
- (IBAction)onHome:(id)sender;

@end

@implementation SettingsViewController

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
    
    [self updateSettings];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [self updateChildView];
}

- (void)viewDidUnload
{
    [self setAvatar:nil];
    [self setUserLabel:nil];
    [self setCityLabel:nil];
    [self setCountryLabel:nil];
    [self setSoundButtonsCollection:nil];
    [self setMusicButtonsCollection:nil];
    [self setFbPostButtonsCollection:nil];
    [self setMailSendButtonsCollection:nil];
    [self setMailSendButtonsCollection:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Helper

- (void)updateChildView
{
    self.avatar.image  = [UIImage imageNamed:self.childManager.currentChild.bigAvatar];
    self.username.text = self.childManager.currentChild.name;
    self.cityLabel.text    = [DataUtils currentParent].city;
    self.countryLabel.text = [DataUtils currentParent].country;
    self.username.text = self.childManager.currentChild.name;
    if ([self.username.text isEqualToString:@""])
        self.username.text = NSLocalizedString(@"Name", nil);
    self.userLabel.text = self.username.text;
    
    [MBProgressHUD showHUDForWindow];
    
    [self.childManager updateChildWithSuccess:^{
        NSLog(@"Update child data Success");
        [MBProgressHUD hideHUDForWindow];
    } failure:^(NSError *error) {
        [MBProgressHUD hideHUDForWindow];
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
    }];
}

- (void)updateSettings
{
    self.isFBSettingsBlocked = NO;
    self.isMailSettingsBlocked = NO;
    
    self.segmentedSoundsButton = [FLSegmentedButton new];
    self.segmentedSoundsButton.selectedIndex = ![self.childManager.currentChild.isSoundEnabled boolValue];
    
    __weak SettingsViewController *weakSelf = self;
    
    [self.segmentedSoundsButton initWithButtonsCollection:self.soundButtonsCollection withHandler:^(int buttonIndex) {
        if (buttonIndex == 0) {
            [weakSelf.soundManager playTouchSoundNamed:weakSelf.soundManager.soundNames[0] loop:NO];
            weakSelf.childManager.currentChild.isSoundEnabled = @YES;
        } else {
            weakSelf.childManager.currentChild.isSoundEnabled = @NO;
        }
        
        [MBProgressHUD showHUDForWindow];
        
        [weakSelf.childManager updateChildWithSuccess:^{
            NSLog(@"Update child data Success");
            [MBProgressHUD hideHUDForWindow];
        } failure:^(NSError *error) {
            [MBProgressHUD hideHUDForWindow];
            [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
        }];
    }];
    
    self.segmentedMusicButton = [FLSegmentedButton new];
    self.segmentedMusicButton.selectedIndex = ![self.childManager.currentChild.isMusicEnabled boolValue];
    
    [self.segmentedMusicButton initWithButtonsCollection:self.musicButtonsCollection withHandler:^(int buttonIndex) {
        if (buttonIndex == 0) {
            [weakSelf.soundManager playTouchSoundNamed:weakSelf.soundManager.soundNames[0] loop:NO];
            weakSelf.childManager.currentChild.isMusicEnabled = @YES;
            [weakSelf.soundManager playBackgroundMusicIfNeeded];
        } else {
            [weakSelf.soundManager playTouchSoundNamed:weakSelf.soundManager.soundNames[0] loop:NO];
            weakSelf.childManager.currentChild.isMusicEnabled = @NO;
            [weakSelf.soundManager stopPlayBackgroundMusicIfNeeded];
        }
        
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
        
        [MBProgressHUD showHUDForWindow];
        
        [weakSelf.childManager updateChildWithSuccess:^{
            NSLog(@"Update child data Success");
            [MBProgressHUD hideHUDForWindow];
        } failure:^(NSError *error) {
            [MBProgressHUD hideHUDForWindow];
            [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
        }];
    }];
    
    self.segmentedFBButton = [FLSegmentedButton new];
    self.segmentedFBButton.isBlocked = self.isFBSettingsBlocked;
    
    [self.segmentedFBButton initWithButtonsCollection:self.fbPostButtonsCollection
                                         selectedTags:self.childManager.currentChild.postTypes
                            withHandlerMultipleChoice:^(int buttonIndex, BOOL isBlocked) {
                                if (isBlocked) {
                                    //            PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
                                    //            [check presentOnViewController:self finish:^{
                                    //                if (check.isPassedCheck) {
                                    weakSelf.isFBSettingsBlocked = NO;
                                    weakSelf.segmentedFBButton.isBlocked = weakSelf.isFBSettingsBlocked;
                                    //                }
                                    //            }];
                                } else {
                                    
                                    [weakSelf.soundManager playTouchSoundNamed:weakSelf.soundManager.soundNames[0] loop:NO];
                                    weakSelf.childManager.currentChild.postTypes = [weakSelf.childManager.currentChild.postTypes numberWithSwitchedBitAtIndex:buttonIndex];
                                }
                                
                                [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                            }];
    
    self.segmentedMailButton = [FLSegmentedButton new];
    self.segmentedMailButton.isBlocked = self.isMailSettingsBlocked;
    
    [self.segmentedMailButton initWithButtonsCollection:self.mailSendButtonsCollection
                                           selectedTags:self.childManager.currentChild.sendTypes
                              withHandlerMultipleChoice:^(int buttonIndex, BOOL isBlocked) {
                                  if (isBlocked) {
                                      //            PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
                                      //            [check presentOnViewController:self finish:^{
                                      //                if (check.isPassedCheck) {
                                      weakSelf.isMailSettingsBlocked = NO;
                                      weakSelf.segmentedMailButton.isBlocked = weakSelf.isMailSettingsBlocked;
                                      //                }
                                      //            }];
                                      
                                  } else {
                                      [weakSelf.soundManager playTouchSoundNamed:weakSelf.soundManager.soundNames[0] loop:NO];
                                      weakSelf.childManager.currentChild.sendTypes = [weakSelf.childManager.currentChild.sendTypes numberWithSwitchedBitAtIndex:buttonIndex];
                                      [MBProgressHUD showHUDForWindow];
                                      
                                      [weakSelf.childManager updateChildWithSuccess:^{
                                          NSLog(@"Update child data Success");
                                          [MBProgressHUD hideHUDForWindow];
                                      } failure:^(NSError *error) {
                                          [MBProgressHUD hideHUDForWindow];
                                          [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                      }];
                                      
                                      
                                      [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
                                  }
                              }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

#pragma mark - Actions

- (IBAction)onTapUser:(id)sender
{
    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    
    [seguesStructure addLink:[ChooseAvatarPopupViewController class]];
    
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (IBAction)onChangePassword:(id)sender
{
//    PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//    [check presentOnViewController:self finish:^{
//        if (check.isPassedCheck) {
            ChangePasswordViewController *changePassword = [ChangePasswordViewController new];
            [changePassword presentOnViewController:self finish:nil];
//        }
//    }];
}

- (IBAction)onHome:(id)sender
{
    [self dismissGameFlowViewControllersWithViewController:self];
}

- (IBAction)onTapBack:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onTapNameEdit:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    ChooseNamePopupViewController *chooseNameVC = [ChooseNamePopupViewController new];
    [chooseNameVC presentOnViewController:self finish:nil];
}

- (IBAction)onTapFacebook:(id)sender
{
//    PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//    [check presentOnViewController:self finish:^{
//        if (check.isPassedCheck) {
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
            
            AddAccountPopupViewController *addAccountPopupViewController = [[AddAccountPopupViewController alloc] init];
            addAccountPopupViewController.isFBAccount = YES;
            [addAccountPopupViewController presentOnViewController:self finish:nil];
            [addAccountPopupViewController.imageView setImage:[UIImage imageNamed:@"Button_Facebook@2x"]];
            [addAccountPopupViewController.label setText:NSLocalizedString(@"Enter your facebook account to continue", @"Settings")];
//        }
//    }];
}

- (IBAction)onTapMail:(id)sender
{
//    PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//    [check presentOnViewController:self finish:^{
//        if (check.isPassedCheck) {
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
            
            AddAccountPopupViewController *addAccountPopupViewController = [[AddAccountPopupViewController alloc] init];
            [addAccountPopupViewController presentOnViewController:self finish:nil];
            [addAccountPopupViewController.imageView setImage:[UIImage imageNamed:@"Button_Mail@2x"]];
            [addAccountPopupViewController.label setText:NSLocalizedString(@"Enter your mail account to continue", @"Settings")];
//        }
//    }];
}

- (IBAction)onTapCity:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    ChooseLocationPopupViewController *chooseLocation = [[ChooseLocationPopupViewController alloc] init];
    [chooseLocation presentOnViewController:self finish:nil];
}

- (IBAction)onTapCountry:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    ChooseLocationPopupViewController *chooseLocation = [[ChooseLocationPopupViewController alloc] init];
    [chooseLocation presentOnViewController:self finish:nil];
}

- (IBAction)onTapLogoff:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [UIAlertView showAlertViewWithTitle:@""
                                message:NSLocalizedString(@"Are you sure you want to exit your profile? All your data will be save and you can come back any time.", @"Logout alert message")
                      cancelButtonTitle:NSLocalizedString(@"Cancel",  @"Logout alert cancel button")
                      otherButtonTitles:@[NSLocalizedString(@"OK", @"Logout alert accept button")]
                                handler:^(UIAlertView *alert, NSInteger buttonIndex) {
                                    if (buttonIndex == 1) {
                                        PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
                                        [seguesStructure addLink:[LogoutAlertViewController class]];
                                        
                                        [[seguesStructure nextViewController] presentOnViewController:self
                                                                                               finish:^{
                                                                                                   if (![ChildManager sharedInstance].currentChild) {
                                                                                                       [self dismissGameFlowViewControllersWithViewController:self];
                                                                                                   } else {
                                                                                                       [self updateChildView];
                                                                                                       [self updateSettings];
                                                                                                   }
                                                                                                   
                                        }];
                                    }
                                }];
}

#pragma mark - ChooseNamePopupViewControllerDelegate

- (void)didEditChild:(Child *)child
{
    [self updateChildView];
}

#pragma mark - ChooseAvatarPopupViewControllerDelegate

- (void)didChangedAvatar
{
    [self updateChildView];
}

#pragma mark - ChooseLocationPopupViewControllerDelegate

- (void)didSelectedRegion
{
    [self updateChildView];
}

@end
