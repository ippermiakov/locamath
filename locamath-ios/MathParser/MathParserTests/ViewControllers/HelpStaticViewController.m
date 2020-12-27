//
//  Help_1_ViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 22.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "HelpStaticViewController.h"
#import "HelpPage.h"
#import "GifPlayerView.h"
#import "MTHTTPClient.h"
#import "SoundManager.h"

@interface HelpStaticViewController ()

@property (weak, nonatomic) IBOutlet UILabel *boardTitleLabel;
@property (strong, nonatomic) IBOutlet UIButton *nextOrDoneButton;

@end

@implementation HelpStaticViewController

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
    NSString *nameFileToAniation = self.help.animation;
    NSString *fileToAnimation = nil;
    
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        self.gifPlayerView.sourceBundle = [[NSBundle mainBundle] pathForResource:self.help.animation ofType:@"html"];
        //for set correct image from help;
        fileToAnimation = [nameFileToAniation substringFromIndex:nameFileToAniation.length - 3];
        if ([fileToAnimation isEqualToString:@"png"]) {
            nameFileToAniation = [nameFileToAniation substringWithRange:NSMakeRange(0, nameFileToAniation.length - 4)];
        }
        
        self.gifPlayerView.imgURL   = [[NSBundle mainBundle] URLForResource:nameFileToAniation
                                                              withExtension:@"png"];
    } else {
        self.gifPlayerView.sourceBundle = nil;
        nameFileToAniation = [nameFileToAniation substringToIndex:nameFileToAniation.length - 4];
        self.gifPlayerView.imgURL   = [[NSBundle mainBundle] URLForResource:nameFileToAniation
                                                              withExtension:@"png"];
    }
}

- (void)viewDidAppear:(BOOL)animated
{
    //it is called few times at once
    if (!self.didViewAppear) {
        
        if (self.isLastPage) {
            [self.nextOrDoneButton setTitle:NSLocalizedString(@"Done", nil) forState:UIControlStateNormal] ;
            //[self.nextOrDoneButton.titleLabel sizeToFit];
        }
        
        self.boardTitleLabel.text = self.help.boardText;
        
        //to avoid sound playing on parent dismissing
        [self performOnViewAppearAfterDelayIfNeeded:^{
            if (self.help.animation) {
                [self.gifPlayerView startAnimating];
            }
            
            NSString *girlSoundName = [NSString stringWithFormat:@"%@-G-%i", self.help.identifier, [self.help.pageNum integerValue] + 1];
            NSString *boySoundName = [NSString stringWithFormat:@"%@-B-%i", self.help.identifier, [self.help.pageNum integerValue] + 1];
            
            NSLog(@"girlSoundName: %@ boySoundName: %@ for help: %@-%@", girlSoundName, boySoundName, self.help.identifier, self.help.pageNum);
            
            [[SoundManager sharedInstance] playDialogSounds:@[girlSoundName, boySoundName]];
        }];
    }
    
    [super viewDidAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
    [[SoundManager sharedInstance] stopPlayDialogSounds];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];

    if (self.help.animation) {
        [self.gifPlayerView stopAnimating];
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    if ([self.help.exampleImages count] < 1) {
        self.example1.hidden = YES;        
    }
    if ([self.help.exampleImages count] < 2) {
        self.example2.hidden = YES;
    }
    
    if (self.help.animation) {
        [self initialize];
    }
    
    CGFloat decreaseImage = 2;
    
    if ([self.help.exampleImages count] == 2) {
        decreaseImage = 2.3;
    }
    
    //TODO: consider refactoring
    if ([self.help.exampleImages count] >= 1) {
        self.image1.image  = [UIImage imageNamed:[self.help.exampleImages objectAtIndex:0]];
        CGPoint center     = self.image1.center;
        CGRect newFrame    = self.image1.frame;
        CGSize imageSize   = self.image1.image.size;
        newFrame.size      = CGSizeMake(imageSize.width / decreaseImage, imageSize.height / decreaseImage);
        self.image1.frame  = newFrame;
        self.image1.center = center;
        
        // hack to avoid image restortion, needs images for help
        if (self.image1.frame.origin.y < 236.0f) {
            CGRect frame = self.image1.frame;
            frame.origin.y = 236.0f;
            self.image1.frame = frame;
        }
    }
    
    if ([self.help.exampleImages count] >= 2) {
        self.image2.image  = [UIImage imageNamed:[self.help.exampleImages objectAtIndex:1]];
        CGPoint center     = self.image2.center;
        CGRect newFrame    = self.image2.frame;
        CGSize imageSize   = self.image1.image.size;
        newFrame.size      = CGSizeMake(imageSize.width / decreaseImage, imageSize.height / decreaseImage);
        self.image2.frame  = newFrame;
        self.image2.center = center;
        
        CGFloat downPointForImage1 = self.image1.frame.origin.y + self.image1.frame.size.height;
        
        if (self.image2.frame.origin.y <= downPointForImage1) {
            CGFloat point_y  = self.image2.frame.origin.y + (downPointForImage1 - self.image2.frame.origin.y)/decreaseImage;
            self.image2.frame = CGRectMake(self.image2.frame.origin.x,
                                           point_y,
                                           self.image1.frame.size.width,
                                           self.image1.frame.size.height);
        }
    }
    
    self.girlPhrase.text = self.help.girlPhrase;
    self.boyPhrase.text  = self.help.boyPhrase;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setExample1:nil];
    [self setExample2:nil];
    [self setImage1:nil];
    [self setImage2:nil];
    [self setGirlPhrase:nil];
    [self setBoyPhrase:nil];
    
    [self setBoardTitleLabel:nil];
    [self setNextOrDoneButton:nil];
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onTapContinue:(id)sender
{
    [self presentNextViewController];
}

- (IBAction)onTapHome:(id)sender
{
    [self dismissToRootViewController];
}

- (IBAction)onTapBack:(id)sender
{
    [self dismiss];
}


@end
