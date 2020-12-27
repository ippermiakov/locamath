//
//  BaseLevelViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 24.07.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseLevelViewController.h"
#import "ConcretLevelViewController.h"
#import "DefinitionPresenter.h"
#import "MTLevelView.h"
#import "DataUtils.h"
#import "ChildManager.h"
#import "GameManager.h"
#import "Game.h"
#import "Level.h"
#import "TransitionsManager.h"
#import "GameAlertViewController.h"
#import "NSException+AbstractMethods.h"
#import "LevelMapViewController.h"

@interface BaseLevelViewController ()

@end

@implementation BaseLevelViewController

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
    [self changePointsForCharacters];
//    [self updateLevelsView];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
//    [self updateLevelsView];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)changePointsForCharacters
{
    @throw [NSException exceptionForAbstractMethod:_cmd];
}

- (void)updateLevelsView
{
    @throw [NSException exceptionForAbstractMethod:_cmd];
}

- (IBAction)onTapCharacters:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[4] loop:NO];
    
    DefinitionPresenter *definitionPresenter = [[DefinitionPresenter alloc] init];
    [GameManager.levelMap presentViewController:definitionPresenter animated:YES completion:nil];
}

- (void)openLevel:(Level *)level withDataLevelView:(NSData *)dataLevelView
{
    @throw [NSException exceptionForAbstractMethod:_cmd];
}

@end
