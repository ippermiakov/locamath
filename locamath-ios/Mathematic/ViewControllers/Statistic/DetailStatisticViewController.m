//
//  DetailStatisticViewController.m
//  Mathematic
//
//  Created by Developer on 18.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DetailStatisticViewController.h"
#import "Level.h"
#import "Task.h"
#import "Action.h"
#import "TaskError.h"

//#import "SolvingViewController.h"

#import "StatisticBarGraph.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"
#import "SolvingAndExercisesViewController.h"

@interface DetailStatisticViewController ()

@property (strong, nonatomic) NSMutableArray *errorList;
@property (strong, nonatomic) StatisticBarGraph *barGraph;
@property (strong, nonatomic) IBOutlet UILabel *errorsCountLabel;
@property (strong, nonatomic) IBOutlet UILabel *fixedErrorsCountLabel;
@property (strong, nonatomic) IBOutlet UITableView *tableView;

@end

@implementation DetailStatisticViewController

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
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    [self updateContent];
    [self setActualFonts];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [super viewDidUnload];
}

- (void)didFinishBackWithOption:(BOOL)option
{
    [self updateContent];
}

#pragma mark - UITableView Data Source

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [self.errorList count];
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 30.0f;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    id<AbstractAchievement> taskError = [self.errorList objectAtIndex:indexPath.row];
    Task *task = nil;
    
    if ([NSStringFromClass([taskError class]) isEqualToString:@"Task"]) {
        task = (Task*)taskError;
    } else {
        TaskError *tempTaskError = (TaskError*)taskError;
        task = tempTaskError.task;
    }
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellIdentifier];
    }
    cell.backgroundColor = [UIColor clearColor];
    
    [cell.textLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:20.0f]];
    [cell.textLabel setTextColor:[UIColor colorWithRed:0.0/255.0 green:138.0/255.0 blue:131.0/255.0 alpha:1.0]];
    
    [cell.textLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:16.0f]];
    cell.textLabel.tag = kRightTextPositionsTag;
    cell.textLabel.textColor = [UIColor whiteColor];
    
    if (task.status == kTaskStatusSolved) {
        cell.textLabel.textColor = [UIColor yellowColor];
    }
    cell.textLabel.text = [DataUtils corretcLocaleTableTextIfNeededWithString:[taskError taskStatisticFixOrErrorDescription]];
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    Task *task = [self.errorList objectAtIndex:indexPath.row];
    
//    SolvingViewController *solvingViewController = [[SolvingViewController alloc] initWithAchievement:task];
//    solvingViewController.backDelegate = self;
//    [self presentViewController:solvingViewController animated:YES completion:nil];
    SolvingAndExercisesViewController *solvingViewController = [[SolvingAndExercisesViewController alloc] initWithAchievement:task];
    solvingViewController.backDelegate = self;
    solvingViewController.isCalledFromStatistic = YES;
    
    [self presentViewController:solvingViewController animated:YES completion:nil];
}

#pragma mark - Actions

- (IBAction)onTapCloseButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];

    [self presentNextViewController];
}

#pragma mark - Bar Graph

- (void)showBarGraph
{
    switch (self.taskErrorType) {
        case kTaskErrorTypeCalculation:
            self.barGraph = [[StatisticBarGraph alloc] initWithFrame:CGRectMake(134.0f, 355.0f, 756.0f, 265.0f)];
            [self.barGraph configurateWithDateType:kDateTypeDay
                                     andTaskStatus:kTaskStatusError
                                  withConcretError:kActionErrorTypeCalculation];
            [self.view addSubview:self.barGraph];
            break;
        case kTaskErrorTypeUnderstanding:
            self.barGraph = [[StatisticBarGraph alloc] initWithFrame:CGRectMake(134.0f, 355.0f, 756.0f, 265.0f)];
            [self.barGraph configurateWithDateType:kDateTypeDay
                                     andTaskStatus:kTaskStatusError
                                  withConcretError:kActionErrorTypeStructure];
            [self.view addSubview:self.barGraph];
            break;
            
        default:
            break;
    }
}

#pragma mark - Helper

- (void)updateContent
{
    [self showBarGraph];
    
    switch (self.taskErrorType) {
        case kTaskErrorTypeCalculation:
            self.errorLable.text = NSLocalizedString(@"Calculation error", nil);
            self.errorList = [[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation] mutableCopy];
            
            [self.errorList addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation | kActionErrorTypeStructure]];
            //errors
            self.errorsCountLabel.text = [NSString stringWithFormat:@"%i", self.errorList.count];
            
            [self.errorList addObjectsFromArray:[DataUtils solvedTasksForTask:DataUtils.tasksFromCurrentChild withErrorType:kActionErrorTypeCalculation]];
            //fixed errors
            self.fixedErrorsCountLabel.text =
            [NSString stringWithFormat:@"%i", [DataUtils solvedTaskErrorCountWithErrorType:kActionErrorTypeCalculation]];
            break;
        case kTaskErrorTypeUnderstanding:
            self.errorLable.text = NSLocalizedString(@"Understanding error", @"Statistics");
            self.errorList = [[DataUtils tasksWithActionErrorType:kActionErrorTypeStructure] mutableCopy];
            
            [self.errorList addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation | kActionErrorTypeStructure]];
            // errors
            self.errorsCountLabel.text = [NSString stringWithFormat:@"%i", self.errorList.count];
            
            [self.errorList addObjectsFromArray:[DataUtils solvedTasksForTask:DataUtils.tasksFromCurrentChild withErrorType:kActionErrorTypeStructure]];
            //fixed errors
            self.fixedErrorsCountLabel.text =
            [NSString stringWithFormat:@"%i",[DataUtils solvedTaskErrorCountWithErrorType:kActionErrorTypeStructure]];
            break;
            
        default:
            break;
    }
    
    [self.errorList sortUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1, id<AbstractAchievement> obj2) {
        return [obj1.lastChangeDate timeIntervalSince1970GMT] < [obj2.lastChangeDate timeIntervalSince1970GMT];
    }];
    
    [self.tableView reloadData];
}

@end
