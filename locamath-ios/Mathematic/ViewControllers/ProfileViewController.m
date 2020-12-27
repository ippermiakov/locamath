//
//  ProfileVC.m
//  profile_controller
//
//  Created by serg on 1/11/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ProfileViewController.h"
#import "ProfileOlympiadResultCell.h"

#import "Task.h"
#import "Parent.h"

#import "OlympiadTask.h"
#import "OlympiadLevel.h"
#import "ChildManager.h"
#import "StatisticManager.h"
#import "UIView+Transform.h"
#import "Level.h"
#import "DataUtils.h"
#import "ComparativeStatisticsTableViewController.h"
#import "FLSegmentedButton.h"

#import "AchievementsViewController.h"
#import "LevelsPath.h"
#import "StatisticStarsView.h"
#import "MBProgressHUD.h"
#import "PresentingSeguesStructure.h"
#import "ChooseLocationPopupViewController.h"
#import "ChooseLocationExplanationPopupViewController.h"
#import "LogoutAlertViewController.h"
#import "MTHTTPClient.h"
#import "StrokeLabel.h"
#import "UIAlertView+Error.h"
#import "GameManager.h"
#import "PopupForDefaultChildViewController.h"
#import "ChooseAvatarPopupViewController.h"
#import "ChooseNamePopupViewController.h"

@interface ProfileViewController ()<UITableViewDataSource, UITableViewDelegate>

@property (strong, nonatomic) IBOutlet UITableView *olympiadTable;
@property (strong, nonatomic) IBOutlet UITableView *taskTableView;
@property (strong, nonatomic) IBOutlet UITableView *comparativeStatisticsTableView;

@property (strong, nonatomic) ComparativeStatisticsTableViewController *comparativeStatisticsTableViewController;
@property (unsafe_unretained, nonatomic) ComparativeType comparativeType;

@property (strong, nonatomic) ChildManager *childManager;

@property (strong, nonatomic) AchievementsViewController *achievementsViewController;

@property (weak, nonatomic) IBOutlet UIImageView *backgroundImageView;
@property (weak, nonatomic) IBOutlet UIImageView *avatarImageView;
@property (weak, nonatomic) IBOutlet UIImageView *avatarNameImageView;
@property (weak, nonatomic) IBOutlet UIImageView *avatarFrameImageView;
@property (weak, nonatomic) IBOutlet UILabel *userNameLabel;
@property (weak, nonatomic) IBOutlet UIImageView *pointsFrame;
@property (weak, nonatomic) IBOutlet UILabel *pointsLabel;
@property (weak, nonatomic) IBOutlet UIImageView *winPointsFrame;
@property (weak, nonatomic) IBOutlet UIImageView *winAchivmentsFrame;
@property (weak, nonatomic) IBOutlet UILabel *earnedLabel;
@property (weak, nonatomic) IBOutlet UILabel *achievementsLabel;

@property (weak, nonatomic) IBOutlet UILabel *cityLabel;
@property (weak, nonatomic) IBOutlet UILabel *countryLabel;

@property (strong, nonatomic) FLSegmentedButton *segmentedButton;
@property (strong, nonatomic) IBOutlet StrokeLabel *labelRecent;
@property (strong, nonatomic) IBOutlet StrokeLabel *labelPointsEarn;

@property (strong, nonatomic) NSArray *cups;

@property (strong, nonatomic) IBOutletCollection(UIButton) NSArray *comparativeButtonsCollection;

- (IBAction)onTapBack:(id)sender;
- (IBAction)onTapLogout:(id)sender;
- (IBAction)onTapShop:(id)sender;

- (IBAction)onTapMyCity:(id)sender;
- (IBAction)onTapMyCountry:(id)sender;
- (IBAction)onTapMyWorld:(id)sender;
@property (weak, nonatomic) IBOutlet StatisticStarsView *statisticStarsView;

- (IBAction)onImage:(UIButton *)sender;
- (IBAction)onName:(id)sender;

- (IBAction)onTapCity:(id)sender;
- (IBAction)onTapCountry:(id)sender;

@end

@implementation ProfileViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    
    if (self) {
        self.childManager = [ChildManager sharedInstance];
    }
    
    return self;
}

- (void)dealloc
{
    NSLog(@"clear ProfileViewController");
}

#pragma mark - View methods

- (void)viewDidLoad
{
    [self.olympiadTable registerNib:[UINib nibWithNibName:@"ProfileOlympiadResultCell" bundle:nil]
             forCellReuseIdentifier:kCellIdentifier];
    
    self.olympiadTable.allowsSelection = NO;
    
    [super viewDidLoad];
    
    //avoid showing view with stub content while it isn't appeared
    self.view.hidden = YES;
    self.olympiadTable.rotation = -(M_PI / 2);
        
    self.segmentedButton = [FLSegmentedButton new];
    self.segmentedButton.selectedIndex = 0;
    
    __weak ProfileViewController *weakSelf = self;
    
    [self.segmentedButton initWithButtonsCollection:self.comparativeButtonsCollection withHandler:^(int buttonIndex) {
        [weakSelf comparativeActionWithIndex:buttonIndex];
    }];
    
    self.achievementsViewController = [AchievementsViewController new];
    self.achievementsViewController.viewControllerToPresentContent = self;
    self.achievementsViewController.tableView = self.taskTableView;
    
    [self.labelRecent whiteShadowForLabel];
    [self.labelPointsEarn whiteShadowForLabel];
}

- (void)viewDidUnload
{
    [self setOlympiadTable:nil];
    [self setTaskTableView:nil];
    [self setComparativeStatisticsTableView:nil];
    [self setComparativeButtonsCollection:nil];
    [self setStatisticStarsView:nil];
    [self setLabelRecent:nil];
    [self setLabelPointsEarn:nil];
    [super viewDidUnload];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];

    //avoid showing view with stub content while it isn't appeared
    self.view.hidden = NO;
    
    //enable view unloading on disappearance
    self.isViewUnloadingLocked = NO;

    if ([ChildManager sharedInstance].currentChild == nil) {
        [self selectChildWithTraining:NO];
    } else {

        [self updateUserView];
        
        if (!self.comparativeStatisticsTableViewController) {
            self.comparativeStatisticsTableViewController = [ComparativeStatisticsTableViewController new];
            self.comparativeStatisticsTableViewController.tableView = self.comparativeStatisticsTableView;
            self.comparativeStatisticsTableViewController.tableView.delegate = self.comparativeStatisticsTableViewController;
            self.comparativeStatisticsTableViewController.tableView.dataSource = self.comparativeStatisticsTableViewController;
        }
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Helper

- (void)updateUserView
{
    self.childManager.currentChild.points = @([StatisticManager earnedScoreByCurrentPlayer]);
    self.pointsLabel.text = [self.childManager.currentChild.points stringValue];
    self.avatarImageView.image = [UIImage imageNamed:self.childManager.currentChild.bigAvatar];
    self.userNameLabel.text = self.childManager.currentChild.name;
    
    self.cityLabel.adjustsFontSizeToFitWidth = YES;
    self.countryLabel.adjustsFontSizeToFitWidth = YES;
    
    self.cityLabel.text = self.childManager.currentChild.parent.city;
    self.countryLabel.text = self.childManager.currentChild.parent.country;
    
    self.avatarImageView.image = [UIImage imageNamed:self.childManager.currentChild.bigAvatar];
    
    if (self.childManager.currentChild.gender == Male) {
        self.backgroundImageView.image = [UIImage imageNamed:@"Back_Blue@2x"];
        self.avatarNameImageView.image = [UIImage imageNamed:@"Avatar_name@2x"];
        self.avatarFrameImageView.image = [UIImage imageNamed:@"Avatar_frame@2x.png"];
        self.pointsFrame.image = [UIImage imageNamed:@"Points_score_field@2x.png"];
        self.winPointsFrame.image = [UIImage imageNamed:@"Win_Points_stat_BOY@2x"];
        self.winAchivmentsFrame.image = [UIImage imageNamed:@"Win_achievments_BOY@2x"];
    } else {
        self.backgroundImageView.image = [UIImage imageNamed:@"Back_Rose"];
        self.avatarNameImageView.image = [UIImage imageNamed:@"Avatar_name_GIRL@2x"];
        self.avatarFrameImageView.image = [UIImage imageNamed:@"Avatar_frame_GIRL@2x.png"];
        self.pointsFrame.image = [UIImage imageNamed:@"Points_score_field_GIRL@2x.png"];
        self.winPointsFrame.image = [UIImage imageNamed:@"Win_Points_stat_GIRL@2x"];
        self.winAchivmentsFrame.image = [UIImage imageNamed:@"Win_achievments_GIRL@2x"];
        [self.earnedLabel setTextColor:[UIColor colorWithRed:24.0f/255.0f green:113.0f/255.0f blue:113.0f/255.0f alpha:1.0f]];
        [self.achievementsLabel setTextColor:[UIColor colorWithRed:24.0f/255.0f green:113.0f/255.0f blue:113.0f/255.0f alpha:1.0f]];
    }
    
    [self.statisticStarsView reloadData];
    [self.achievementsViewController reloadData];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    [self comparativeActionWithIndex:self.comparativeType];
    
    __weak ProfileViewController *weakSelf = self;

    if (self.childManager.currentChild && [[MTHTTPClient sharedMTHTTPClient] isParentAuthentificated]) {
        [self.childManager updateChildWithSuccess:^{
            
            [weakSelf.comparativeStatisticsTableViewController updateRateChildsWithFinishBlock:^{
                [weakSelf comparativeActionWithIndex:weakSelf.comparativeType];
            }];
            
        } failure:^(NSError *error) {
            [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
        }];
    }
    
    NSMutableArray *cups = [NSMutableArray new];
    
    for (int j = 1; j < 4; ++j) {
        for (int i = 1; i < 4; ++i) {
            NSString *cupID = [NSString stringWithFormat:@"9-0-%d-%d", j, i];
            
            if ([self isCupCompletedWithID:cupID]) {
                [cups addObject:cupID];
            }
        }
    }
    
    self.cups = cups.copy;
    
    [self.olympiadTable reloadData];
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [self updateUserView];
}

//TODO: move to StatisticManager
- (BOOL)isCupCompletedWithID:(NSString *)identifier
{
    NSPredicate *levelPredicate = [NSPredicate predicateWithFormat:@"identifier == %@ && child == %@",
                                   identifier, [ChildManager sharedInstance].currentChild];
    OlympiadLevel *level = [OlympiadLevel findFirstWithPredicate:levelPredicate];
    
    NSArray *tasks = [level.tasks allObjects];
    
    if (tasks.count == 0) {
        return NO;
    }
    
    for (OlympiadTask *task in tasks) {
        if ( ! task.isCorrect) {
            return NO;
        }
    }
    
    return YES;
}

#pragma mark - Actions Methods

- (IBAction)onTapBack:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];

    [self goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
}

- (IBAction)onTapLogout:(id)sender
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
                                        [[seguesStructure nextViewController] presentOnViewController:self finish:^{
                                            
                                            [self goBackAnimated:YES withDelegate:self.backDelegate withOption:NO];
                                        }];
                                    }
                                }];
}

- (IBAction)onTapShop:(id)sender
{
    NSLog(@"Shop selected");
}

- (IBAction)onTapMyCity:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[2] loop:NO];
}

- (IBAction)onTapMyCountry:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[2] loop:NO];
}

- (IBAction)onTapMyWorld:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[2] loop:NO];
}

- (IBAction)onImage:(UIButton *)sender
{
    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    
    [seguesStructure addLink:[ChooseAvatarPopupViewController class]];
    
    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (IBAction)onName:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    ChooseNamePopupViewController *chooseNameVC = [ChooseNamePopupViewController new];
    [chooseNameVC presentOnViewController:self finish:nil];
}

- (IBAction)onTapCity:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [self chooseLocationIfNeeded];
}

- (IBAction)onTapCountry:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [self chooseLocationIfNeeded];
}

- (void)chooseLocationIfNeeded
{
    if ([DataUtils currentParent]) {
        ChooseLocationPopupViewController *chooseLocation = [[ChooseLocationPopupViewController alloc] init];
        [chooseLocation presentOnViewController:self finish:nil];
    }
#warning uncomment for register parent
    else {
        __weak ProfileViewController *weakSelf = self;
        
        PopupForDefaultChildViewController *popUpForDefaultChild = [PopupForDefaultChildViewController new];
        popUpForDefaultChild.popupType = kPopupForDefaultTypeProfile;
        
        [popUpForDefaultChild presentOnViewController:self finish:^{
            if (popUpForDefaultChild.isOkSelected) {
                [weakSelf registerParentIfNeededWithComplitionBlock:nil];
            }
        }];
    }
}

#pragma mark - UITableViewDelegate

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 110.0f;
}

#pragma mark - UITableViewDatasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [self.cups count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    ProfileOlympiadResultCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    cell.backgroundColor = [UIColor clearColor];
    
    cell.rotation = M_PI / 2;
    
    //TODO: move to DataUtils
    OlympiadLevel *level = [OlympiadLevel findFirstWithPredicate:[NSPredicate predicateWithFormat:@"identifier == %@", self.cups[indexPath.row]]];
        
    cell.cupView.image = [UIImage imageNamed:level.image];
    
    return cell;
}

#pragma mark - Helper

- (void)comparativeActionWithIndex:(NSInteger)index
{
    switch (index) {
        case kComparativeTypeCity:
            
            [self.comparativeStatisticsTableViewController selectWithKey:kCity
                                                                andValue:self.childManager.currentChild.identifier];
            self.comparativeType = kComparativeTypeCity;
            
            break;
            
        case kComparativeTypeCountry:
            
            [self.comparativeStatisticsTableViewController selectWithKey:kCountry
                                                                andValue:self.childManager.currentChild.identifier];
            self.comparativeType = kComparativeTypeCountry;
            
            break;
            
        case kComparativeTypeWorld:
            
            [self.comparativeStatisticsTableViewController selectWithKey:kWorld
                                                                andValue:nil];
             self.comparativeType = kComparativeTypeWorld;
            break;
            
        default:
            break;
    }
}

@end
