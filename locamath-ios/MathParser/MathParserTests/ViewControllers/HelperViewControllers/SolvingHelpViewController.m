//
//  SolvimgHelpViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 12.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SolvingHelpViewController.h"
#import "GifPlayerView.h"
#import "GameManager.h"
#import "SoundManager.h"

@interface SolvingHelpViewController ()

@end

@implementation SolvingHelpViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)initialize
{
    self.animationPlayer.imgURL = [[NSBundle mainBundle] URLForResource:kAnimationBackgoundImageName
                                                        withExtension:@"png"];
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.animationPlayer.sourceBundle = [[NSBundle mainBundle] pathForResource:@"Solving.swf" ofType:@"html"];
        self.animationPlayer.onAnimationStart = ^{
            [[SoundManager sharedInstance] playDialogSounds:@[@"Solving"]];
        };
    } else {
        self.animationPlayer.sourceBundle = nil;
        self.animationPlayer.imgURL = [[NSBundle mainBundle] URLForResource:@"Solving.swf"
                                                            withExtension:@"png"];
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    [self initialize];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [self.animationPlayer startAnimating];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    
    [self.animationPlayer stopAnimating];
    
    [[SoundManager sharedInstance] stopPlayDialogSounds];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Actions

- (IBAction)onTapClose:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
    [self dismissModalViewControllerAnimated:YES];
}

- (void)viewDidUnload
{
    [self setAnimationPlayer:nil];
    [super viewDidUnload];
}

@end
