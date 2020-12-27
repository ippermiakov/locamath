//
//  Definition1ViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 13.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Definition1ViewController.h"
#import "Definition2ViewController.h"

#import "GifPlayerView.h"
#import "UIView+Transform.h"
#import "ChildManager.h"

@interface Definition1ViewController ()

@property (weak, nonatomic) IBOutlet UIButton *backButton;

@end

@implementation Definition1ViewController

- (void)initialize
{
    self.gifPlayerView.imgURL   = [[NSBundle mainBundle] URLForResource:kAnimationBackgoundImageName
                                                          withExtension:@"png"];

    if([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.gifPlayerView.sourceBundle = [[NSBundle mainBundle] pathForResource:@"speed1.swf" ofType:@"html"];
    } else {
        self.gifPlayerView.sourceBundle = nil;
        self.gifPlayerView.imgURL = [[NSBundle mainBundle] URLForResource:@"speed1_INT"
                                                            withExtension:@"png"];
    }

    self.girlLabel.rotation = -0.30;
    self.boyLabel.rotation  = 0.17;
    
    Child *currentChild = [ChildManager sharedInstance].currentChild;
    
    self.backButton.hidden = ![currentChild.isTrainingComplete boolValue];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    [self initialize];
}

- (void)viewDidUnload
{
    [self setGifPlayerView:nil];
    [self setGirlLabel:nil];
    [self setBoyLabel:nil];
    
    [self setBackButton:nil];
    [super viewDidUnload];
}

- (void)viewDidAppear:(BOOL)animated
{
    self.page = 1;
    [super viewDidAppear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    [self.gifPlayerView stopAnimating];
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

@end
