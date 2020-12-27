//
//  PopupOlympiadSolvedViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 09.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PopupOlympiadSolvedViewController.h"

@interface PopupOlympiadSolvedViewController ()

@end

@implementation PopupOlympiadSolvedViewController

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
    [super viewDidUnload];
}


- (IBAction)onTapOkButton:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
    
    [self presentNextViewController];
}

@end
