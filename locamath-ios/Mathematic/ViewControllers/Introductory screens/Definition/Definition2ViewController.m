//
//  Definition2ViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 13.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Definition2ViewController.h"
#import "Definition3ViewController.h"

#import "GifPlayerView.h"
#import "UIView+Transform.h"
#import "MTHTTPClient.h"

@interface Definition2ViewController ()

@end

@implementation Definition2ViewController

- (void)initialize
{
    self.gifPlayerView.imgURL   = [[NSBundle mainBundle] URLForResource:kAnimationBackgoundImageName
                                                          withExtension:@"png"];
    
    if([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.gifPlayerView.sourceBundle = [[NSBundle mainBundle] pathForResource:@"speed3.swf" ofType:@"html"];
    } else {
        self.gifPlayerView.sourceBundle = nil;
        self.gifPlayerView.imgURL = [[NSBundle mainBundle] URLForResource:@"speed3" withExtension:@"png"];
    }
    
    self.girlLabel.rotation = -0.30;
    self.boyLabel.rotation  = 0.17;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    [self initialize];
}

- (void)viewDidAppear:(BOOL)animated
{
    self.page = 2;
    [super viewDidAppear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];

    [self.gifPlayerView stopAnimating];
}

- (void)viewDidUnload
{
    [self setGifPlayerView:nil];
    [self setGirlLabel:nil];
    [self setBoyLabel:nil];
    
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

#pragma mark - Actions

- (IBAction)onTapContinue:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self presentNextViewController];
}

- (IBAction)onTapBack:(id)sender
{
//    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];    
    [self dismiss];
}

@end
