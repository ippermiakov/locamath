//
//  LogoutAlertViewController.m
//  Mathematic
//
//  Created by alexbutenko on 8/6/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "LogoutAlertViewController.h"
#import "GameManager.h"
#import "BaseViewController+RegistrationAndLogin.h"
#import "ChildManager.h"
#import "ChooseChildPopupViewController.h"

@interface LogoutAlertViewController ()

- (IBAction)onLogOff:(id)sender;
- (IBAction)onChangeChild:(id)sender;

@end

@implementation LogoutAlertViewController

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
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Actions

- (IBAction)onLogOff:(id)sender
{
    [[GameManager sharedInstance] logOffParent];
    [self dismiss];
}

- (IBAction)onChangeChild:(id)sender
{
    [[ChildManager sharedInstance] logoutCurrentChild];
    
    [self.seguesStructure addLink:[ChooseChildPopupViewController class]];
    [self presentNextViewController];
}

@end
