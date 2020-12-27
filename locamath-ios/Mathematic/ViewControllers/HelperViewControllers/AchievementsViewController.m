//
//  AchievementsViewController.m
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AchievementsViewController.h"
#import "AbstractAchievementViewController.h"
#import "AbstractAchievement.h"
#import "SolvingAndExercisesViewController.h"

@interface AchievementsViewController ()<UITableViewDataSource, UITableViewDelegate>

@property (strong, nonatomic) NSArray *activities;

@end

@implementation AchievementsViewController

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
    
    [self.tableView reloadData];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)reloadData
{
    self.activities = DataUtils.achievementsFromCurrentChild;
    [self.tableView reloadData];
}

#pragma mark - UITableViewDelegate

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 44.0f;
}

#pragma mark - UITableViewDatasource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [self.activities count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    id<AbstractAchievement> achievement = [self.activities objectAtIndex:indexPath.row];
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellIdentifier];
    }
    cell.backgroundColor = [UIColor clearColor];
    
    cell.textLabel.tag = kRightTextPositionsTag;
    
    [cell.textLabel setTextColor:[UIColor whiteColor]];
    
    cell.textLabel.adjustsFontSizeToFitWidth = YES;
    cell.textLabel.text = [DataUtils corretcLocaleTableTextIfNeededWithString:[achievement statisticDescription]];
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    id<AbstractAchievement> achievement = [self.activities objectAtIndex:indexPath.row];
    
    if (achievement.controllerClass) {
        BaseViewController<AbstractAchievementViewController> *solvingViewController =
        [[achievement.controllerClass alloc] initWithAchievement:achievement];
        
        solvingViewController.isCalledFromStatistic = YES;
        self.viewControllerToPresentContent.isViewUnloadingLocked = YES;
        
        solvingViewController.backDelegate = self.viewControllerToPresentContent;
        [self.viewControllerToPresentContent presentViewController:solvingViewController
                                                          animated:YES
                                                        completion:nil];
    }
}

#pragma mark - Setters&Getters

- (void)setTableView:(UITableView *)tableView
{
    _tableView = tableView;
    _tableView.delegate = self;
    _tableView.dataSource = self;
}

@end
