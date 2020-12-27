//
//  AddAccountPopupViewController.m
//  Mathematic
//
//  Created by Developer on 19.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AddAccountPopupViewController.h"
#import "AddAccountCell.h"
#import "ChildManager.h"
#import "AccountMail.h"
#import "AccountFB.h"
#import "MTHTTPClient.h"
#import "UIAlertView+Error.h"

static NSUInteger const kPositionForFBCellView_X = 20;
static NSUInteger const kPositionForFBCellView_Y = 5;

@interface AddAccountPopupViewController ()

@property (strong, nonatomic) NSArray *accounts;
@property (weak, nonatomic) IBOutlet UITableView *theTableView;

- (IBAction)onTapContinue:(id)sender;

@end

@implementation AddAccountPopupViewController

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
    self.theTableView.allowsSelection = NO;
    if (self.isFBAccount) {
         self.theTableView.scrollEnabled = NO;
        if ([ChildManager sharedInstance].currentChild.postFBAccount) {
            self.accounts = [NSArray arrayWithObject:[ChildManager sharedInstance].currentChild.postFBAccount];
        }
    } else {
        self.accounts = [[ChildManager sharedInstance].currentChild.sendStatisticsAccounts allObjects];
    }

    [self.theTableView registerNib:[UINib nibWithNibName:@"AddAccountCell" bundle:nil]
         forCellReuseIdentifier:kCellIdentifier];
    
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
    [self setTheTableView:nil];
    [self setImageView:nil];
    [self setLabel:nil];
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onTapContinue:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    AddAccountCell *cell = (AddAccountCell *)[self.theTableView cellForRowAtIndexPath:[NSIndexPath indexPathForRow:0 inSection:0]];
    
    NSError *error = nil;

    if (0 == [cell.textField.text length]) {
        [self dismiss];
        return;
    }
    
    [MTHTTPClient validateEmail:cell.textField.text withError:&error];
    
    if (error) {
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
        cell.textField.text = @"";
        [self updateSelf];
        return;
    }
    
    if (self.isFBAccount) {
        [[ChildManager sharedInstance] createFBAccountIfNeededWithEmail:cell.textField.text];
    } else {
        [[ChildManager sharedInstance] addAccountForChildWithName:cell.textField.text];
    }
    
    [self dismiss];
}

#pragma mark - UITableViewDelegate

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 54.0f;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    NSUInteger cellCount = [self.accounts count] + 1;
    
    if (self.isFBAccount) {
        cellCount = 1;
    }
    
	return cellCount;
}

- (AddAccountCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    AddAccountCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    
    cell.backgroundColor = [UIColor clearColor];
    cell.delegate = self;
    
    cell.button.tag = indexPath.row;

    if (self.isFBAccount) {
        
        if ([self.accounts count] == 1) {
            AccountFB *account = [self.accounts objectAtIndex:0];
            
            //correct view in cell )
            cell.cellView.frame = CGRectMake(kPositionForFBCellView_X,
                                             kPositionForFBCellView_Y,
                                             cell.cellView.frame.size.width,
                                             cell.cellView.frame.size.height);
            
            cell.textField.text = account.mail;
            cell.button.hidden = YES;
        } else {
            cell.cellView.frame = CGRectMake(kPositionForFBCellView_X,
                                             kPositionForFBCellView_Y,
                                             cell.cellView.frame.size.width,
                                             cell.cellView.frame.size.height);
            cell.button.hidden = YES;
        }
        
    } else {
        if (indexPath.row == 0) {
            [cell.button setBackgroundImage:[UIImage imageNamed:@"solving_Button_Add_Solution_Exspression"] forState:UIControlStateNormal];
            [cell.button addTarget:self action:@selector(addAccount:) forControlEvents:UIControlEventTouchUpInside];
            cell.textField.userInteractionEnabled = YES;
        } else {
            [cell.button setBackgroundImage:[UIImage imageNamed:@"solving_Button_Delete"] forState:UIControlStateNormal];
            [cell.button addTarget:self action:@selector(deleteAccoount:) forControlEvents:UIControlEventTouchUpInside];
            cell.textField.userInteractionEnabled = NO;
        }
        
        if (indexPath.row > 0) {
            AccountMail *account = [self.accounts objectAtIndex:indexPath.row - 1];
            cell.textField.text = account.name;
        }
    }

    return cell;
}

#pragma mark - AddAccountCellDelegate

- (void)textHasBeenEdited:(NSString *)text forIndex:(NSInteger)index
{
    NSInteger indexToEdit = index - 1;

    if ([self.accounts count] > indexToEdit) {
        AccountMail *account = [self.accounts objectAtIndex:indexToEdit];
        account.name = text;
        
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
        
        [self updateSelf];
    }
}

#pragma mark - Helper

- (void)updateSelf
{
    if (!self.isFBAccount) {
        self.accounts = [[ChildManager sharedInstance].currentChild.sendStatisticsAccounts allObjects];
    }
    
    [self.theTableView reloadData];
}

- (void)addAccount:(id)sender
{
    AddAccountCell *cell = (AddAccountCell *)[self.theTableView cellForRowAtIndexPath:[NSIndexPath indexPathForRow:[sender tag] inSection:0]];
    
    if (cell.textField.text) {
        NSError *error = nil;
        
        if ([MTHTTPClient validateEmail:cell.textField.text withError:&error]) {
            if (![self isAccountHaveSameEmailName:cell.textField.text]) {
                [[ChildManager sharedInstance] addAccountForChildWithName:cell.textField.text];
                cell.textField.text = @"";
                [self updateSelf];
            } else {
                [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"User with same email already exists", nil)];
            }
        } else {
            [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
            cell.textField.text = @"";
            [self updateSelf];
        }
    }
}

- (void)deleteAccoount:(id)sender
{
    //starts with second row (1st index)
    NSInteger indexToRemove = [sender tag] - 1;
    if ([self.accounts count] > indexToRemove) {
        AccountMail *accountTodelete = [self.accounts objectAtIndex:indexToRemove];
        [accountTodelete deleteEntity];
        
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
        
        [self updateSelf];
    }
}

- (BOOL)isAccountHaveSameEmailName:(NSString *)email
{
    AccountMail *account = [self.accounts match:^BOOL(AccountMail *obj) {
        return [obj.name isEqualToString:email];
    }];
    
    return account != nil;
}

@end
