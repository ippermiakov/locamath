//
//  ChooseChildPopupViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 26.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChooseChildPopupViewController.h"
#import "ChildManager.h"
#import "ChooseAvatarPopupViewController.h"
#import "ChooseNamePopupViewController.h"
#import "PresentingSeguesStructure.h"
#import "GameManager.h"
#import "MTHTTPClient.h"
#import "MBProgressHUD.h"
#import "DebugMode.h"
#import <QuartzCore/QuartzCore.h>
#import "MBProgressHUD+Mathematic.h"
#import "GMGridView.h"
#import "ChildViewCell.h"
#import "AddChildViewCell.h"
#import "GMGridViewLayoutStrategies.h"

static CGFloat const kHeaderHeight = 25.0f;

@interface ChooseChildPopupViewController ()<GMGridViewDataSource, GMGridViewActionDelegate>

@property (strong, nonatomic) ChildManager     *childManager;
@property (unsafe_unretained, nonatomic) BOOL isChildNameInputted;

@property (copy, nonatomic) NSString *currentChildName;
@property (copy, nonatomic) NSString *selectedChildName;

@property (unsafe_unretained, nonatomic) BOOL isChildsLoading;
@property (strong, nonatomic) NSMutableArray *childCells;
@property (strong, nonatomic) IBOutlet GMGridView *childViews;
@property (strong, nonatomic) AddChildViewCell *footerAddChild;
@property (unsafe_unretained, nonatomic) BOOL needShowAddButton;

- (IBAction)onRefresh:(id)sender;
- (IBAction)onAddChild:(id)sender;


@end

@implementation ChooseChildPopupViewController

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
    self.needShowAddButton = YES;
    [self updateChildsData];
    
    [super viewDidLoad];
    
    self.childViews.style = GMGridViewStyleSwap;
    self.childViews.itemSpacing = 130;
    self.childViews.centerGrid = NO;
    self.childViews.enableEditOnLongPress = YES;
    self.childViews.minimumPressDuration = 0.5;
    self.childViews.layoutStrategy = [GMGridViewLayoutStrategyFactory strategyFromType:GMGridViewLayoutHorizontal];
    self.childViews.dataSource = self;
    self.childViews.actionDelegate = self;
    self.childViews.clipsToBounds = YES;
    
    [self.childViews reloadData];
    __weak ChooseChildPopupViewController *weakSelf = self;
    
    self.view.onTouchDownBlock = ^(NSSet *touches, UIEvent *event) {
        [weakSelf resetEditingIfNeeded];
        [weakSelf.childViews reloadData];
    };
}

- (void)resetEditingIfNeeded
{
    if (self.childViews.isEditing) {
        [self.childViews setEditing:NO animated:YES];
        self.needShowAddButton = YES;
        [self updateChildsData];
    }
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [MBProgressHUD showHUDForWindow];
    [self loadChilds];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onRefresh:(id)sender
{
    [self loadChilds];
}

- (IBAction)onAddChild:(id)sender
{
    [[ChildManager sharedInstance] logoutCurrentChild];
    
    [self updateChildDetailsAndAddToParent];
}

- (void)updateChildDetailsAndAddToParent
{
    ChooseNamePopupViewController *chooseNameVC = [ChooseNamePopupViewController new];
    chooseNameVC.shouldCreateNewChild = YES;
    
    [self.seguesStructure addLinkWithObject:chooseNameVC];
    
    ChooseAvatarPopupViewController *chooseAvatarVC = [ChooseAvatarPopupViewController new];
    
    if (self.onFinish) {
        chooseAvatarVC.onFinish = self.onFinish;
    }
    
    [self.seguesStructure addLinkWithObject:chooseAvatarVC];
    
    [self presentNextViewController];
}

#pragma mark - GMGridViewDataSource

- (NSInteger)numberOfItemsInGMGridView:(GMGridView *)gridView
{
    return [DataUtils childs].count + 1;
}

- (CGSize)GMGridView:(GMGridView *)gridView sizeForItemsInInterfaceOrientation:(UIInterfaceOrientation)orientation
{
    GMGridViewCell *cell = nil;
    
    if ([self.childCells count]) {
        cell = self.childCells[0];
    } else {
        cell = self.footerAddChild;
    }
    
    return (CGSize){cell.frame.size.width, cell.frame.size.height};
}

- (GMGridViewCell *)GMGridView:(GMGridView *)gridView cellForItemAtIndex:(NSInteger)index
{
    ChildViewCell *cell = nil;
    
    if (index < [DataUtils childs].count &&
        index < self.childCells.count) {
        
        cell = self.childCells[index];
        
        Child *child = [DataUtils childs][index];
        
        cell.avatarImageView.image =[UIImage imageNamed:child.bigAvatar];
        cell.nameLabel.text = child.name;
        cell.deleteButtonIcon = [UIImage imageNamed:@"solving_Button_Delete.png"];
        
        if (child.gender == Female) {
            cell.backgroundNameImage.image = [UIImage imageNamed:@"Avatar_name_GIRL@2x.png"];
            cell.backgroundForChildImag.image = [UIImage imageNamed:@"Avatar_frame_GIRL@2x.png"];
        }
    } else if (self.childCells.count == index && self.needShowAddButton) {
        cell = (ChildViewCell *)[self footerAddChildView];
    }
    
    return cell;
}

// Allow a cell to be deletable. If not implemented, YES is assumed.
- (BOOL)GMGridView:(GMGridView *)gridView canDeleteItemAtIndex:(NSInteger)index
{
    return YES;
}

#pragma mark - GMGridViewActionDelegate

- (void)GMGridView:(GMGridView *)gridView didTapOnItemAtIndex:(NSInteger)position
{
    if (position < [DataUtils childs].count) {
        Child *selectedChild = [DataUtils childs][position];
        
        if (selectedChild) {
            [MBProgressHUD showHUDForWindow];
            
            [self.childManager createChildWithName:selectedChild.name
                                           success:^{
                                               [MBProgressHUD hideHUDForWindow];
                                               [self dismissToRootViewController];
                                               
                                               if (self.onFinish) {
                                                   self.onFinish();
                                               }
                                           }
                                           failure:^(NSError *error) {
                                               [MBProgressHUD hideHUDForWindow];
                                               [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                                           }];
        }
    }
}

- (void)GMGridView:(GMGridView *)gridView changedEdit:(BOOL)edit
{
    self.needShowAddButton = !edit;
    [self.childViews reloadData];
}

// Called when the delete-button has been pressed. Required to enable editing mode.
// This method wont delete the cell automatically. Call the delete method of the gridView when appropriate.
- (void)GMGridView:(GMGridView *)gridView processDeleteActionForItemAtIndex:(NSInteger)index
{
    Child *childForDelete = [DataUtils childs][index];
#warning do we really need this progress check?? not it looks very confusing when I try to delete child, but no reaction
//    if (![GameManager hasProgressChild:childForDelete]) {
        [UIAlertView showAlertViewWithTitle:nil
                                    message:[NSString stringWithFormat:NSLocalizedString(@"Remove child %@ confirmation", nil), childForDelete.name]
                          cancelButtonTitle:NSLocalizedString(@"cancel", nil)
                          otherButtonTitles:@[NSLocalizedString(@"OK", nil)] handler:^(UIAlertView *alert, NSInteger buttonIndex) {
            if (buttonIndex == 1) {
                [self removeChildAtIndex:index];
                [UIAlertView showAlertViewWithMessage:[NSString stringWithFormat:NSLocalizedString(@"Remove child %@ confirmation email", nil), childForDelete.name]];
            }
        }];
//    }
}


#pragma mark - Helper

- (void)removeChildAtIndex:(NSUInteger)index
{
//    NSLog(@"childs: %@", DataUtils.childs);
    Child *childForDelete = [DataUtils.childs objectAtIndex:index];
        
    [MBProgressHUD showHUDForWindow];
    
    [self.childManager removeChild:childForDelete success:^{
        [MBProgressHUD hideHUDForWindow];
        if (DataUtils.childs.count == 0) {
            [self resetEditingIfNeeded];
        }
        [self.childViews reloadData];
    }
                           failure:^(NSError *error) {
                               [MBProgressHUD hideHUDForWindow];
                               [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
                           }];
}

- (void)loadChilds
{
    if (!self.isChildsLoading) {
        self.isChildsLoading = YES;
        [MBProgressHUD showHUDForWindow];
        
        [[ChildManager sharedInstance] loadChildsWithSuccess:^{
            NSLog(@"childs loaded");
            
            [self updateChildsData];
            [MBProgressHUD hideHUDForWindow];
            self.isChildsLoading = NO;
        } failure:^(NSError *error) {
            [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
            [MBProgressHUD hideHUDForWindow];
            self.isChildsLoading = NO;
        }];
    }
}

- (void)updateChildsData
{
    self.childCells = nil;
    self.childCells = [NSMutableArray new];
    
    NSInteger temp = [DataUtils childs].count > 0 ? [DataUtils childs].count : 0;
    for (int i = 0; i < temp; i++) {
        ChildViewCell *cell = [[NSBundle mainBundle] loadNibNamed:@"ChildViewCell"
                                                        owner:nil
                                                      options:nil][0];
        [self.childCells addObject:cell];
    }
    
    [self.childViews reloadData];
}

- (AddChildViewCell *)footerAddChildView
{
    if (self.footerAddChild == nil) {
        self.footerAddChild = [[NSBundle mainBundle] loadNibNamed:@"AddChildViewCell"
                                                        owner:nil
                                                      options:nil][0];
    }
    
    __weak ChooseChildPopupViewController *weakSelf = self;
    
    self.footerAddChild.addChildBlock = ^{
        [weakSelf onAddChild:weakSelf.footerAddChild];
    };
    
    return self.footerAddChild;
}

@end
