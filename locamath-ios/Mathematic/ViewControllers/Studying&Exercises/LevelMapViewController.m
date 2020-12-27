//
//  LevelMap.m
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "LevelMapViewController.h"
#import "Level_1_ViewController.h"
#import "PrivacyPolicyPopupViewController.h"
#import "ChooseAvatarPopupViewController.h"
#import "ChooseNamePopupViewController.h"
#import "ChooseLocationPopupViewController.h"
#import "ChooseLocationExplanationPopupViewController.h"
#import "ChooseChildPopupViewController.h"
#import "Definition1ViewController.h"
#import "Definition2ViewController.h"
#import "Definition3ViewController.h"
#import "PresentingSeguesStructure.h"
#import "DebugMode.h"
#import "MBProgressHUD.h"
#import "MBProgressHUD+Mathematic.h"
#import "DefinitionPresenter.h"
#import "ChildManager.h"
#import "ProfileViewController.h"
#import "StatisticViewController.h"
#import "OlympiadViewController.h"
#import "FLSegmentedButton.h"
#import "StrokeLabel.h"
#import "LoginOrRegisterViewController.h"
#import "MTHTTPClient.h"
#import "Parent.h"
#import "StatisticManager.h"
#import "UIViewController+DismissViewController.h"

NSInteger const kMaxX = 2972;
NSInteger const kMaxY = 1413;

NSInteger const kOffsetX = 430;
NSInteger const kOffsetY = 285;

NSInteger const kScreenWidth = 768;
NSInteger const kScreenHeight = 1024;

@interface LevelMapViewController ()
- (IBAction)onSolvingPath:(id)sender;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *pathSolvingButtonsCollection;

@property (strong, nonatomic) IBOutlet UILabel *pointsLabel;
@property (strong, nonatomic) IBOutlet UIImageView *childAvatar;
@property (strong, nonatomic) ProfileViewController *profileVC;
@property (strong, nonatomic) IBOutlet StrokeLabel *pointsEarned;

- (IBAction)onMusic:(UIButton *)sender;
- (IBAction)onSound:(UIButton *)sender;
- (IBAction)onProfile:(id)sender;
- (IBAction)onStatistic:(UIButton *)sender;
- (IBAction)onOlympiad:(id)sender;

@property (strong, nonatomic) IBOutlet UIButton *musicButton;
@property (strong, nonatomic) IBOutlet UIButton *soundButton;
@property (strong, nonatomic) IBOutlet UILabel *stubAvatarLabel;
@property (unsafe_unretained, nonatomic) BOOL isPresentingSelectChildsForSchooldMode;

@end

@implementation LevelMapViewController

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
	// Do any additional setup after loading the view.
    [self updateCurrentLevel];
    
    __weak UIScrollView *scrollView = self.theScrollView;
    __weak LevelMapViewController * levelMap = self;
    
    self.level.backBlock = ^() {
        [scrollView setContentSize:CGSizeMake(levelMap.currentLevel.view.frame.size.width, levelMap.currentLevel.view.frame.size.height)];
        [scrollView setContentOffset:[levelMap centerPoint]];
    };

    self.theScrollView.minimumZoomScale = 0.545;
    self.theScrollView.maximumZoomScale = 1.0;
    
    [self.theScrollView setZoomScale:self.theScrollView.maximumZoomScale];

    [self.theScrollView addSubview:self.currentLevel.view];
    
    [self.theScrollView setContentSize:CGSizeMake(self.currentLevel.view.frame.size.width, self.currentLevel.view.frame.size.height)];
    [self.theScrollView setContentOffset:[self centerPoint]];

#ifdef DEBUG
    [self.pathSolvingButtonsCollection each:^(UIButton *sender) {
        if (!([sender.titleLabel.text isEqualToString:@"Maroon"] && self.levelType == kLevelType1)) {
            sender.hidden = NO;
        }
    }];
#endif
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self.currentLevel viewWillAppear:NO];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    [self.currentLevel viewDidAppear:NO];
    [self updateChild];
    
    if (!self.isPresentingSelectChildsForSchooldMode) {
        [self selectChildWithTraining:YES];
    } else {
        self.isPresentingSelectChildsForSchooldMode = NO;
    }
}

- (void)viewDidUnload
{
    [self setPathSolvingButtonsCollection:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - UIScrollViewDelegate

- (UIView *)viewForZoomingInScrollView:(UIScrollView *)scrollView
{
    // Return the view that we want to zoom
    return self.level.view;
}

- (void)scrollViewDidZoom:(UIScrollView *)scrollView
{
    // The scroll view has zoomed, so we need to re-center the contents
    [self centerScrollViewContents];
}

#pragma mark - Actions

- (IBAction)onOlympiad:(id)sender
{
    if ([ChildManager sharedInstance].currentChild == nil) {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Child not selected.", @"Home screen on tap Olympiad")];
    } else {
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
        
        OlympiadViewController *olympiadVC = [[OlympiadViewController alloc] init];
        [self presentViewController:olympiadVC animated:YES completion:nil];
    }
}

- (IBAction)onSolvingPath:(UIButton *)sender
{    
//    MBProgressHUD *HUD = [MBProgressHUD showSyncHUDForWindow];
    
    [DebugMode solveLevelsPathWithColor:sender.titleLabel.text
                            levelNumber:@(self.levelType + 1)
                               progress:^(CGFloat progress) {
//                                   [HUD updateWithProgress:progress];
    }
                                 finish:^{
        [self.level updateLevelsView];
//        [HUD hideOnSyncCompletion];
                                     
        if (self.level.backBlock) {
            self.level.backBlock();
        }
    }
                                failure:^(NSError *error) {
                                    [self.level updateLevelsView];
//                                    [HUD hideOnSyncFailure];
    }];
}

- (IBAction)onStatistic:(UIButton *)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
//        PrivacyPolicyPopupViewController *check = [PrivacyPolicyPopupViewController new];
//        check.shouldPassDefaultChildCheck = [DataUtils isCurrentChildDefault];
//        check.onFinish = ^{
//            if ([[MTHTTPClient sharedMTHTTPClient] isParentAuthentificated] || (![[MTHTTPClient sharedMTHTTPClient] isReachable] && [DataUtils currentParent])) {
//                StatisticViewController *parentPageViewController = [[StatisticViewController alloc]
//                                                                     initWithNibName:@"StatisticNewViewController" bundle:nil];
//                [self presentViewController:parentPageViewController animated:YES completion:nil];
//            } else {
//                
//                LoginOrRegisterViewController *registerViewController = [LoginOrRegisterViewController new];
//                registerViewController.isRegister = YES;
//                
//                registerViewController.onFinish = ^{
//                    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
//                    
//                    void(^registrationCompletionBlock)() = ^() {
//                        StatisticViewController *parentPageViewController = [[StatisticViewController alloc]
//                                                                                initWithNibName:@"StatisticNewViewController" bundle:nil];
//                        [self presentViewController:parentPageViewController animated:YES completion:nil];
//                    };
//                    
//                    if ([[DataUtils currentParent].city isEqualToString:kUndefined] ||
//                        [[DataUtils currentParent].city length] == 0) {
//                        [seguesStructure addLink:[ChooseLocationExplanationPopupViewController class]];
//                        [seguesStructure addLink:[ChooseLocationPopupViewController class]];
//                    }
//                    
//                    if ([[ChildManager sharedInstance].currentChild.name isEqualToString:kDefaultChildName]) {
//                        ChooseNamePopupViewController *chooseNameViewController = [ChooseNamePopupViewController new];
//                        chooseNameViewController.shouldCreateNewChild = YES;
//                        
//                        ChooseAvatarPopupViewController *chooseAvatarViewController = [ChooseAvatarPopupViewController new];
//                        chooseAvatarViewController.onFinish = registrationCompletionBlock;
//                        
//                        [seguesStructure addLinkWithObject:chooseNameViewController];
//                        [seguesStructure addLinkWithObject:chooseAvatarViewController];
//                        
//                        [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
//
//                    } else {
//                        [[ChildManager sharedInstance] createChildWithName:[ChildManager sharedInstance].currentChild.name
//                                                                   success:^{
//                                                                       [[seguesStructure nextViewController] presentOnViewController:self
//                                                                                                                              finish:registrationCompletionBlock];
//                                                                   }
//                                                                   failure:^(NSError *error) {
//                                                                       NSLog(@"create child failure with error : %@", [error localizedDescription]);
//                                                                   }];
//                    }
//                };
//                
//                [registerViewController presentOnViewController:self finish:nil];                
//            }
//    };
//    
//    [check presentOnViewController:self finish:nil];
    [self registerParentIfNeededWithComplitionBlock:^{
        StatisticViewController *parentPageViewController = [[StatisticViewController alloc]
                                                                                initWithNibName:@"StatisticNewViewController" bundle:nil];
        [self presentViewController:parentPageViewController animated:YES completion:nil];
    }];
}

- (IBAction)onProfile:(id)sender
{
    if ([ChildManager sharedInstance].currentChild.avatar.length == 0) {
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
        PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
        
        [seguesStructure addLink:[ChooseAvatarPopupViewController class]];
        [seguesStructure addLink:[ChooseNamePopupViewController class]];

        [[seguesStructure nextViewController] presentOnViewController:self finish:^{
            [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
            
            self.profileVC = [ProfileViewController new];
            self.profileVC.backDelegate = self;
            [self presentViewController:self.profileVC animated:YES completion:nil];
        }];
    } else {
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
        
        self.profileVC = [ProfileViewController new];
        self.profileVC.backDelegate = self;
        [self presentViewController:self.profileVC animated:YES completion:nil];
    }
}

- (IBAction)onMusic:(UIButton *)sender
{
    sender.selected = !sender.selected;
    
    [ChildManager sharedInstance].currentChild.isMusicEnabled = @(!sender.selected);
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    if (sender.selected) {
        [[SoundManager sharedInstance] stopPlayBackgroundMusicIfNeeded];
    } else {
        [[SoundManager sharedInstance] playBackgroundMusicIfNeeded];
    }
    
    [[ChildManager sharedInstance] updateChildWithSuccess:^{
        NSLog(@"set child data");
    } failure:^(NSError *error) {
        NSLog(@"error set child data");
    }];
    
}

- (IBAction)onSound:(UIButton *)sender
{
    sender.selected = !sender.selected;
    [ChildManager sharedInstance].currentChild.isSoundEnabled = @(!sender.selected);
    [[ChildManager sharedInstance] updateChildWithSuccess:^{
        NSLog(@"set child data");
    } failure:^(NSError *error) {
        NSLog(@"error set child data");
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

#pragma mark - Helper

- (CGPoint)centerPoint
{
    CGFloat zoomScale = self.theScrollView.zoomScale;
    
    CGFloat pointX = (self.level.charactersView.frame.origin.x - kOffsetX) * zoomScale;
    CGFloat pointY = (self.level.charactersView.frame.origin.y - kOffsetY) * zoomScale;
    
    if (pointX - (kScreenHeight/2 - kOffsetX * zoomScale) < 0) {
        pointX = self.level.charactersView.frame.origin.x * zoomScale - (pointX + kOffsetX * zoomScale);
    } else if (self.level.charactersView.frame.origin.x * zoomScale + kScreenHeight/2 + self.level.charactersView.frame.size.height > kMaxX * zoomScale) {
        pointX = self.level.charactersView.frame.origin.x * zoomScale - (pointX + kScreenHeight + kOffsetX * zoomScale - kMaxX * zoomScale);
    } else if (zoomScale < 1) {
        pointX = (self.level.charactersView.frame.origin.x * zoomScale - kOffsetX);
    }
    
    if (pointY - (kScreenWidth/2 - kOffsetY * zoomScale) < 0) {
        pointY = self.level.charactersView.frame.origin.y * zoomScale - (pointY + kOffsetY * zoomScale);
    } else if (pointY + kScreenWidth > kMaxY * zoomScale) {
        pointY = self.level.charactersView.frame.origin.y * zoomScale - (pointY + kScreenWidth + kOffsetY * zoomScale - kMaxY * zoomScale);
    } else if (zoomScale < 1) {
        pointY = (self.level.charactersView.frame.origin.y * zoomScale - kOffsetY);
    }
    
    return (CGPoint){pointX, pointY};
}

- (void)centerScrollViewContents
{    
    CGSize boundsSize = self.theScrollView.bounds.size;
    CGRect contentsFrame = self.level.view.frame;
    
    if (contentsFrame.size.width < boundsSize.width) {
        contentsFrame.origin.x = (boundsSize.width - contentsFrame.size.width) / 2.0f;
    } else {
        contentsFrame.origin.x = 0.0f;
    }
    
    if (contentsFrame.size.height < boundsSize.height) {
        contentsFrame.origin.y = (boundsSize.height - contentsFrame.size.height) / 2.0f;
    } else {
        contentsFrame.origin.y = 0.0f;
    }
    
    self.level.view.frame = contentsFrame;
}

- (void)updateChild
{
    if (self.childAvatar != nil) {
        [ChildManager sharedInstance].currentChild.points = @([StatisticManager earnedScoreByCurrentPlayer]);
        self.pointsLabel.text = [[ChildManager sharedInstance].currentChild.points stringValue];
        self.childAvatar.image = [UIImage imageNamed:[[ChildManager sharedInstance].currentChild avatarWithSuffix:@"_Small"]];
        
        if (self.childAvatar.image) {
            self.stubAvatarLabel.hidden = YES;
        } else {
            self.stubAvatarLabel.hidden = NO;
        }
        
        self.pointsEarned.text = NSLocalizedString(@"Points earned", nil);
        
        if ([ChildManager sharedInstance].currentChild)  {
            self.musicButton.selected = ![[ChildManager sharedInstance].currentChild.isMusicEnabled boolValue];
            [[SoundManager sharedInstance] playBackgroundMusicIfNeeded];
            self.soundButton.selected = ![[ChildManager sharedInstance].currentChild.isSoundEnabled boolValue];
        }
    }
}

- (void)updateCurrentLevel
{
    self.currentLevel = nil;
    
    switch (self.levelType) {
        case kLevelType1:
            self.currentLevel = [[Level_1_ViewController alloc] init];
            self.level = (BaseLevelViewController *)self.currentLevel;
            break;
            
//        case kLevelType2:
//            self.currentLevel = [[Level_2_ViewController alloc] init];
//            self.level = (BaseLevelViewController *)self.currentLevel;
            
        default:
            break;
    }
}

- (BOOL)canViewUnloadingBeUnlocked
{
    return NO;
}

- (void)showSelectionChildForSchoolMode
{
    self.isPresentingSelectChildsForSchooldMode = YES;
    
    [[ChildManager sharedInstance] logoutCurrentChild];
    
    //get the top viewcontroller and dismiss everything to LevelMap
    [self dismissGameFlowViewControllersWithViewController:self.topPresentedViewController];
    
    //display selection screen without add button
    [self selectChildWithTraining:NO canAddChilds:NO];
}

@end
