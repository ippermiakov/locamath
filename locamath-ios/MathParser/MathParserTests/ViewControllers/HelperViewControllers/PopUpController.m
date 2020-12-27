//
//  PopUpController.m
//  Mathematic
//
//  Created by Developer on 04.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PopUpController.h"
#import "UIView+LWAutoFont.h"
#import "Action.h"
#import "TaskError.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"

static NSString *const kErrorType = @"errorType";
static NSString *const kErrorCount = @"errorCount";
static NSString *const kActionType = @"actionType";
static NSString *const kActionNumber = @"actionNumber";


@interface PopUpController ()<UITableViewDataSource, UITableViewDelegate>

@end

@implementation PopUpController

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

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setPointsLabel:nil];
    [self setTextViewDescription:nil];
    [self setRefreshButton:nil];
    [self setBackButton:nil];
    [self setNextLabel:nil];
    [self setNextButton:nil];
    [super viewDidUnload];
}

- (void)goBackAnimated:(BOOL)animated withDelegate:(id)delegate withOption:(BOOL)option
{
    [self.delegate popOverDidTapOkButton];
}

#pragma mark - Actions

- (IBAction)onTapOkButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.delegate popOverDidTapOkButton];
}

- (IBAction)onTapRestoreButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.delegate popOverDidTapRestoreButton];
}

- (IBAction)onTapHomeButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.delegate popOverDidTapHomeButton];
}

- (IBAction)onTapNextButton:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self.delegate popOverDidTapNextButton];
}

#pragma mark - UITableViewDelegate

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [[self dataDict] count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    NSDictionary *dict = [[self dataDict] objectAtIndex:indexPath.row];
    
    NSString *errorDescription = nil;
    
    switch ([dict[kErrorType] integerValue]) {
        case kActionErrorTypeCalculation:
            errorDescription = NSLocalizedString(@"Calculation error", nil);
            break;
            
        case kActionErrorTypeStructure:
            errorDescription = NSLocalizedString(@"Structure error", @"Popup error description");
            break;
#pragma clang diagnostic push
#pragma clang diagnostic ignored "-Wswitch"
        case kActionErrorTypeCalculation | kActionErrorTypeStructure:
            errorDescription = NSLocalizedString(@"Structure and calculation error", @"Popup error description");
            break;
#pragma clang diagnostic pop
        default:
            break;
    }
    
    NSString *actionTypeDescription = nil;
    //Action *action = [[taskError.actions allObjects] lastObject];
    switch ([dict[kActionType] integerValue]) {
        case kActionTypeExpression:
            actionTypeDescription = NSLocalizedString(@"Expression", nil);
            break;
            
        case kActionTypeSolution:
            actionTypeDescription = NSLocalizedString(@"Solution", nil);
            break;
        case kActionTypeAnswer:
            break;
    }
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellIdentifier];
    }
    
    cell.backgroundColor = [UIColor clearColor];
    
    [cell.textLabel setTextAlignment:UITextAlignmentLeft];
    [cell.textLabel setTextColor:[UIColor whiteColor]];
    [cell.textLabel setFont:[UIFont fontWithName:[UIView defaultBoldFontName] size:16.0f]];
    cell.textLabel.adjustsFontSizeToFitWidth = YES;
    
    if ([dict[kErrorCount] integerValue] > 1) {
        cell.textLabel.text = [NSString stringWithFormat:@"%@#%d - %@ x%i", actionTypeDescription,
                               [dict[kActionNumber] integerValue], errorDescription, [dict[kErrorCount] integerValue]];
    } else {
    
        cell.textLabel.text = [NSString stringWithFormat:@"%@#%d - %@", actionTypeDescription,
                           [dict[kActionNumber] integerValue], errorDescription];
    }
    
    return cell;
}

#pragma mark - Helper

- (NSArray *)dataDict
{
    NSMutableArray *dataArray = [NSMutableArray new];
  
    TaskError *taskError = [[self sortedErrors] lastObject];
    __block NSUInteger structureCount = 0;
    [taskError.actions each:^(Action *taskErrorAction) {
        
        if (taskErrorAction.subActions) {
            NSNumber *calcErrorCount = [self countActionWithErrorType:kActionErrorTypeCalculation
                                                          fromActions:[taskErrorAction.subActions mutableCopy]];
            if ([calcErrorCount integerValue]) {
                NSMutableDictionary *data = [NSMutableDictionary new];
                [data setValue:@(kActionErrorTypeCalculation) forKey:kErrorType];
                [data setValue:calcErrorCount forKey:kErrorCount];
                [data setValue:taskErrorAction.typeNumber forKey:kActionType];
                [data setValue:@([[taskError.actions allObjects] indexOfObject:taskErrorAction] + 1) forKey:kActionNumber];
                [dataArray addObject:data];
            }
        }
        
        if (([taskErrorAction.errorNumber integerValue] == kActionErrorTypeStructure ||
            [taskErrorAction.errorNumber integerValue] == (kActionErrorTypeStructure | kActionErrorTypeCalculation))
            && structureCount == 0) {
            
            structureCount ++;
            NSMutableDictionary *data = [NSMutableDictionary new];
            [data setValue:@(kActionErrorTypeStructure) forKey:kErrorType];
            [data setValue:@0 forKey:kErrorCount];
            [data setValue:taskErrorAction.typeNumber forKey:kActionType];
            [data setValue:@([[taskError.actions allObjects] indexOfObject:taskErrorAction] + 1) forKey:kActionNumber];
            [dataArray addObject:data];
        }

    }];
    
    return dataArray;
}

- (NSNumber *)countActionWithErrorType:(ActionErrorType)errorType fromActions:(NSArray *)actions
{
    NSArray *actionsWithErrorType = [actions select:^BOOL(Action *action) {
        return [action.errorNumber integerValue] == errorType;
    }];
    
    return @(actionsWithErrorType.count);
}

- (NSArray *)sortedErrors
{
    NSArray *sortedArray = [self.errorActions sortedArrayUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1, id<AbstractAchievement> obj2) {
               return [obj1.lastChangeDate timeIntervalSince1970GMT] > [obj2.lastChangeDate timeIntervalSince1970GMT];
    }];

    return sortedArray;
}

@end