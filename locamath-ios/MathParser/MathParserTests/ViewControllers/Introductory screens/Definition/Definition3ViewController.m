//
//  Definition3ViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 13.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Definition3ViewController.h"
#import "ChildManager.h"
#import "GifPlayerView.h"
#import "UIView+Transform.h"
#import "MTHTTPClient.h"
#import "MBProgressHUD.h"
#import "GameManager.h"
#import "MBProgressHUD+Mathematic.h"

@interface Definition3ViewController ()

@end

@implementation Definition3ViewController

- (void)initialize
{
    self.gifPlayerView.imgURL   = [[NSBundle mainBundle] URLForResource:kAnimationBackgoundImageName
                                                          withExtension:@"png"];
    if([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.gifPlayerView.sourceBundle = [[NSBundle mainBundle] pathForResource:@"speed2all.swf" ofType:@"html"];
    } else {
        self.gifPlayerView.sourceBundle = nil;
        self.gifPlayerView.imgURL = [[NSBundle mainBundle] URLForResource:@"speed2all"
                                                            withExtension:@"png"];
    }
    
    self.girlLabel.rotation = -0.30;
    self.boyLabel.rotation  = 0.17;
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
    
    [super viewDidUnload];
}

- (void)viewDidAppear:(BOOL)animated
{
    self.page = 3;
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
    
    [[ChildManager sharedInstance] currentChild].isTrainingComplete = @YES;
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    if (![DataUtils isCurrentChildDefault]) {
        [MBProgressHUD showHUDForWindow];
    } else {
        [self dismissToRootViewController];
        return;
    }
    
    [[ChildManager sharedInstance] updateChildWithSuccess:^{
        NSLog(@"Child update completed");
        [MBProgressHUD hideHUDForWindow];
        [self dismissToRootViewController];
        
    } failure:^(NSError *error) {
        /* error on iOS 5 with presenting viewcontroller on viewcontroller's view, which view has been directly added instead of presented. => let other viewcontroller
            present auth/selection viewcontroller         */
       /* if ([error code] != kAuthorizationFailureCode &&
            [error code] != kChildSelectionFailureCode) {*/
            
      //  }
        [MBProgressHUD hideHUDForWindow];
        [self dismissToRootViewController];
    }];
}

- (IBAction)onTapBack:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [self dismiss];
}

@end
