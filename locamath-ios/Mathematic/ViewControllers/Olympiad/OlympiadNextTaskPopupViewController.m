//
//  OlympiadNextTaskPopupViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 16.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadNextTaskPopupViewController.h"

@interface OlympiadNextTaskPopupViewController ()

@property (strong, nonatomic) IBOutlet UIButton *redoButton;

@property (strong, nonatomic) IBOutlet UILabel *explainForFailLabel;
@property (strong, nonatomic) IBOutlet UILabel *questionForFailLabel;
@property (strong, nonatomic) IBOutlet UILabel *scoreLabel;

@property (strong, nonatomic) IBOutlet UIImageView *titleImageView;
@property (strong, nonatomic) IBOutlet UIButton *backButton;
@property (strong, nonatomic) IBOutlet UIButton *nextButton;
@property (strong, nonatomic) IBOutlet UILabel *nextLabel;

@end

@implementation OlympiadNextTaskPopupViewController

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
    
    if (!self.isFailPopup) {
        
        self.redoButton.hidden = YES;
        self.explainForFailLabel.hidden = YES;
        self.questionForFailLabel.hidden = YES;
        
        //correction buttons position
        self.backButton.frame = CGRectMake(474, 591, self.backButton.frame.size.width, self.backButton.frame.size.height);
        self.nextButton.frame = CGRectMake(418, 449, self.nextButton.frame.size.width, self.nextButton.frame.size.height);
        self.nextLabel.frame = CGRectMake(434, 460, self.nextLabel.frame.size.width, self.nextLabel.frame.size.height);
        
        self.scoreLabel.text = [NSString stringWithFormat:NSLocalizedString(@"%@ points from %@", @"Exercises page"),
                           self.task.currentScore, self.task.points];
    } else {
        self.scoreLabel.hidden = YES;
        self.titleImageView.image = [UIImage imageNamed:@"Button_Error@2x.png"];
    }
    // Do any additional setup after loading the view from its nib.
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setRedoButton:nil];
    [self setScoreLabel:nil];
    [self setExplainForFailLabel:nil];
    [self setQuestionForFailLabel:nil];
    [self setBackButton:nil];
    [self setNextButton:nil];
    [self setNextLabel:nil];
    [super viewDidUnload];
}

#pragma mark IBActions

- (IBAction)onTapRedo:(id)sender
{
    self.actionType = NextActionRedo;
    [self dismiss];
}

- (IBAction)onTapNextTask:(id)sender
{
    self.actionType = NextActionTask;
    [self dismiss];
}

- (IBAction)onTapBack:(id)sender
{
    self.actionType = NextActionBack;
    [self dismiss];
}

@end
