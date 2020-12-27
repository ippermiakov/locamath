//
//  DefinitionBaseViewController.m
//  Mathematic
//
//  Created by Developer on 11.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DefinitionBaseViewController.h"
#import "GifPlayerView.h"
#import "GameManager.h"

@interface DefinitionBaseViewController ()

@end

@implementation DefinitionBaseViewController

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
    
    if (![self.view superview]) {
        if ([self.parentVC isViewLoaded]) {
            [self.parentVC.view addSubview:self.view];
        } else {
            [[GameManager levelMap].presentedViewController.view addSubview:self.view];
        }
    }
}

- (void)viewDidAppear:(BOOL)animated
{
    if (!self.didViewAppear) {
        
        //avoid double sound playing on parent dismiss
        [self performOnViewAppearAfterDelayIfNeeded:^{
            if (self.page != 2) {
                [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
            }
            
            [self.gifPlayerView startAnimating];
            
            NSString *girlSoundName = [NSString stringWithFormat:@"Def-%i-G", self.page];
            NSString *boySoundName = [NSString stringWithFormat:@"Def-%i-B", self.page];
            
            [[SoundManager sharedInstance] playDialogSounds:@[girlSoundName, boySoundName]];
        }];
    }
    
    [super viewDidAppear:animated];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
    [[SoundManager sharedInstance] stopPlayDialogSounds];
}

@end
