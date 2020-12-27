//
//  AnimationViewController.m
//  Mathematic
//
//  Created by Developer on 23.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AnimationViewController.h"
#import "GifPlayerView.h"
#import "MTHTTPClient.h"

@interface AnimationViewController ()

@property (strong, nonatomic) NSString *fileName;

@end

@implementation AnimationViewController

- (id)initWithAnimationFileName:(NSString *)name
{
    self = [super init];
    if (self) {
        self.fileName = name;
    }
    return self;
}

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
    self.gifPlayerView.imgURL = [[NSBundle mainBundle] URLForResource:kAnimationBackgoundImageName
                                                          withExtension:@"png"];
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.gifPlayerView.sourceBundle = [[NSBundle mainBundle] pathForResource:self.fileName ofType:@"html"];
    } else {
        self.gifPlayerView.sourceBundle = nil;
        self.fileName = [self.fileName substringToIndex:self.fileName.length - 4];
        self.gifPlayerView.imgURL = [[NSBundle mainBundle] URLForResource:self.fileName
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
    [self.gifPlayerView startAnimating];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    [self.gifPlayerView stopAnimating];
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

@end
