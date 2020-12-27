//
//  ChooseLocationPopupViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChooseLocationExplanationPopupViewController.h"
#import "ChooseLocationPopupViewController.h"
#import "Child.h"
#import "Definition1ViewController.h"

@interface ChooseLocationExplanationPopupViewController ()

@end

@implementation ChooseLocationExplanationPopupViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
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

#pragma mark - Actions

- (IBAction)onTapContinue:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self presentNextViewController];
}

@end
