//
//  ParentPageViewController.m
//  Mathematic
//
//  Created by Alexander on 9/14/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatisticViewController.h"
#import "JottingViewController.h"
#import "ChildManager.h"
#import "DetailStatisticViewController.h"
#import "StatisticPieGraph.h"
#import "StatisticBarGraph.h"
#import "StatisticScatterGraph.h"
#import "StatisticManager.h"
#import "Task.h"
#import "ChildManager.h"
#import "Level.h"
#import "DataUtils.h"
#import "MTHTTPClient.h"
#import "AchievementsViewController.h"
#import "StatisticStarsView.h"
#import "HelpForParentViewController.h"
#import "PopupForDefaultChildViewController.h"
#import "AboutPopupViewController.h"
#import "FLSegmentedButton.h"
#import "MBProgressHUD+Mathematic.h"
#import "NSNumber+BitsOperations.h"
#import "AddAccountPopupViewController.h"
#import "LogoutAlertViewController.h"
#import "UIViewController+DismissViewController.h"
#import "MTUnderlineButton.h"
#import "Parent.h"

enum
{
    DAILY = 1,
    WEEKLY = 2,
    MOTHLY = 3
};

enum
{
    TOTAL_GRAPH = 212,
    PROGRESS_GRAPH = 213
};

typedef enum {
    kGraphTypeBar       = 0,
    kGraphTypeScatter   = 1
} GraphType;


@interface StatisticViewController ()<UIPopoverControllerDelegate, MTUnderlineButtonDelegate>

@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *datePeriodButtons;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *differentGraphsButtons;
@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *mailSendButtonsCollection;
@property (strong, nonatomic) IBOutletCollection(MTUnderlineButton) NSArray *underlineButtons;

@property (unsafe_unretained, nonatomic) GraphType graphType;
@property (unsafe_unretained, nonatomic) DateType currentDateType;

@property (strong, nonatomic) StatisticPieGraph *pieGraph;
@property (strong, nonatomic) StatisticBarGraph *barGraph;

@property (strong, nonatomic) StatisticScatterGraph *scatterGraph;
@property (strong, nonatomic) ChildManager *childManager;

@property (weak, nonatomic) IBOutlet UIImageView *avatar;
@property (weak, nonatomic) IBOutlet UILabel *childname;
@property (weak, nonatomic) IBOutlet UILabel *earnedScore;
@property (strong, nonatomic) IBOutlet UITableView *statisticTableView;
@property (weak, nonatomic) IBOutlet UILabel *comingSoonPlaceholderLabel;

@property (strong, nonatomic) AchievementsViewController *achievementsViewController;
@property (weak, nonatomic) IBOutlet StatisticStarsView *statisticStarsView;

@property (strong, nonatomic) FLSegmentedButton * segmentedMailButton;
@property (weak, nonatomic) IBOutlet UISwitch *schoolModeSwitch;

- (IBAction)onTapBackButton:(id)sender;
- (IBAction)onTapJotting:(id)sender;
- (IBAction)onTapCalculationError:(id)sender;
- (IBAction)onTapUnderstandingError:(id)sender;
- (IBAction)onTapAbout:(id)sender;
- (IBAction)onTapMail:(id)sender;
- (IBAction)onTapLogoff:(id)sender;
- (IBAction)onSchoolMode:(UISwitch *)sender;

@end

@implementation StatisticViewController {
    UIPopoverController *popoverController;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.graphType = kGraphTypeBar;
        self.currentDateType = kDateTypeDay;
        
        self.childManager = [ChildManager sharedInstance];
    }
    return self;
}


#pragma mark - ViewController lifecicle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.statisticStarsView.isParentsStatistic = YES;
    
    [self updateUserStatistics];
    
     __weak StatisticViewController *weakSelf = self;
    
    self.segmentedMailButton = [FLSegmentedButton new];
    
    [self.segmentedMailButton initWithButtonsCollection:self.mailSendButtonsCollection
                                           selectedTags:self.childManager.currentChild.sendTypes
                              withHandlerMultipleChoice:^(int buttonIndex, BOOL isBlocked) {
                                  
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
                              }];
    
    [self.schoolModeSwitch setOn:[[DataUtils currentParent].isSchoolMode boolValue]];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    if ([ChildManager sharedInstance].currentChild == nil) {
        [self selectChildWithTraining:NO];
    }
    
    [self updateUserStatistics];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [self updateUserStatistics];
}

- (void)viewDidUnload
{
    [self setDatePeriodButtons:nil];
    [self setDifferentGraphsButtons:nil];
    [self setAvatar:nil];
    [self setChildname:nil];
    [self setEarnedScore:nil];
    [self setStatisticTableView:nil];
    [self setComingSoonPlaceholderLabel:nil];
    [self setStatisticStarsView:nil];
    [super viewDidUnload];
}


- (void)popoverControllerDidDismissPopover:(UIPopoverController *)popoverController
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
}

#pragma mark - IBActions

- (IBAction)onTapBackButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismissModalViewControllerAnimated:YES];
}

- (IBAction)onTapJotting:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[3] loop:NO];
    
    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    
    NSInteger lastPage = 3;
    
    for (NSInteger i = 1; i <= lastPage; i++) {
        
        NSString *nibName = [NSString stringWithFormat:@"HelpForParent%i", i];
        
        [seguesStructure addLinkWithInstantiator:^PresentableViewController *{
            HelpForParentViewController *vc = [[HelpForParentViewController alloc] initWithNibName:nibName bundle:nil];
            
            if (i == lastPage) {
                vc.isLast = YES;
            }
            
            return vc;
        }];
    }
    
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (IBAction)onTapCalculationError:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[3] loop:NO];
    
    DetailStatisticViewController *detailStatisticViewController = [DetailStatisticViewController new];
    detailStatisticViewController.taskErrorType = kTaskErrorTypeCalculation;
    [detailStatisticViewController presentOnViewController:self finish:nil];
}

- (IBAction)onTapUnderstandingError:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[3] loop:NO];
    
    DetailStatisticViewController *detailStatisticViewController = [DetailStatisticViewController new];
    detailStatisticViewController.taskErrorType = kTaskErrorTypeUnderstanding;
    [detailStatisticViewController presentOnViewController:self finish:nil];
}

- (IBAction)onTapAbout:(id)sender
{
    AboutPopupViewController *aboutViewController = [AboutPopupViewController new];
    [aboutViewController presentOnViewController:self finish:nil];
}

- (void)changeDatePeriodWithIndex:(NSInteger)index
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[2] loop:NO];
    
    switch (index) {
        case DAILY:
            self.currentDateType = kDateTypeDay;
            break;
            
        case WEEKLY:
            self.currentDateType = kDateTypeWeek;
            break;
            
        case MOTHLY:
            self.currentDateType = kDateTypeMonth;
            break;
    }
    
    [self showGraph];
}

- (IBAction)onTapChangeGraph:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    UIButton *send = (UIButton *)sender;
    
    [self changeImagesForButtons:self.differentGraphsButtons sender:send defaultImageName:@"Button_Chart" selectedImageName:@"Button_Chart_tapped"];
    
    switch (send.tag) {
        case TOTAL_GRAPH:
            self.graphType = kGraphTypeBar;
            break;
            
        case PROGRESS_GRAPH:
            self.graphType = kGraphTypeScatter;
            break;
    }
    
    [self showGraph];
}

- (IBAction)onTapMail:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    AddAccountPopupViewController *addAccountPopupViewController = [[AddAccountPopupViewController alloc] init];
    [addAccountPopupViewController presentOnViewController:self finish:nil];
    [addAccountPopupViewController.imageView setImage:[UIImage imageNamed:@"Button_Mail@2x"]];
    [addAccountPopupViewController.label setText:NSLocalizedString(@"Enter your mail account to continue", @"Settings")];
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
                                                                                                       [self updateUserStatistics];
                                                                                                   }
                                                                                                   
                                                                                               }];
                                    }
                                }];
}

- (IBAction)onSchoolMode:(UISwitch *)sender
{
    [DataUtils currentParent].isSchoolMode = @(sender.isOn);
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)didSelectButtunWithTag:(NSInteger)tag
{
    [self changeDatePeriodWithIndex:tag];
    
    [self.underlineButtons each:^(MTUnderlineButton *sender) {
        if (sender.tag != tag) {
            [sender removeLine];
        }
    }];
}

#pragma mark - Main methods

- (void)showGraph
{
    if (self.barGraph) {
        [self.barGraph removeFromSuperview];
        self.barGraph = nil;
    }
    
    if (self.scatterGraph) {
        [self.scatterGraph removeFromSuperview];
        self.scatterGraph = nil;
    }
    
    if (self.graphType == kGraphTypeBar) {
        [self showBarGraph];
    } else {
        self.comingSoonPlaceholderLabel.hidden = NO;
    }
}

- (void)showPieGraph
{
    [self.pieGraph removeFromSuperview];
    self.pieGraph = nil;
    
    self.pieGraph = [[StatisticPieGraph alloc] initWithFrame:CGRectMake(790.0f, 515.0f, 250.0f, 250.0f)];
    [self.view addSubview:self.pieGraph];
}

- (void)showBarGraph
{
    self.barGraph = [[StatisticBarGraph alloc] initWithFrame:CGRectMake(74.0f, 400.0f, 471.0f, 280.0f)];
    
    [self.barGraph configurateWithDateType:self.currentDateType andTaskStatus:kTaskStatusSolved withConcretError:nil];
    
    [self.view addSubview:self.barGraph /*belowSubview:self.differentGraphsButtons[0]*/];
}

- (void)showScatterGraph
{
    self.scatterGraph = [[StatisticScatterGraph alloc] initWithFrame:CGRectMake(460.0f, 448.0f, 471.0f, 229.0f)];
    [self.scatterGraph configurateWithDateType:self.currentDateType];
    [self.view insertSubview:self.scatterGraph belowSubview:self.differentGraphsButtons[0]];
}

#pragma mark - Update Child Interface Methods

- (void)changeImagesForButtons:(NSArray *)aButtons sender:(UIButton *)aSender defaultImageName:(NSString *)aDefImg selectedImageName:(NSString *)aSelImg
{
    for (UIButton *butt in aButtons) {
        if ([butt isEqual:aSender]) {
            [butt setBackgroundImage:[UIImage imageNamed:aSelImg] forState:UIControlStateNormal];
            continue;
        }
        [butt setBackgroundImage:[UIImage imageNamed:aDefImg] forState:UIControlStateNormal];
    }
}

- (void)setAvatarImage
{
    self.avatar.image = [UIImage imageNamed:[ChildManager sharedInstance].currentChild.bigAvatar];
}

- (void)showChildName
{
    self.childname.text = [ChildManager sharedInstance].currentChild.name;
    
    if ([self.childname.text isEqualToString:@""]) {
        self.childname.text = NSLocalizedString(@"Name", nil);
    }
}

#pragma marck - Helper

- (void)updateUserStatistics
{
    [self showChildName];
    [self showPieGraph];
    [self showGraph];
    self.earnedScore.text = [NSString stringWithFormat:@"%d", [StatisticManager earnedScoreByCurrentPlayer]];
    self.avatar.image = [UIImage imageNamed:self.childManager.currentChild.bigAvatar];
    
    self.achievementsViewController = [AchievementsViewController new];
    self.achievementsViewController.viewControllerToPresentContent = self;
    self.achievementsViewController.tableView = self.statisticTableView;
    
    [self.achievementsViewController reloadData];
    [self.statisticStarsView reloadData];
}

@end
