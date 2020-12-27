//
//  PopupForDefaultChildViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 26.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PopupForDefaultChildViewController.h"

static CGSize const kHomeLabelExplainSize = (CGSize){303, 226};
static CGSize const kOlympiadOrProfileLabelExplainSize = (CGSize){303, 124};
static CGSize const kStatisticLabelExplainSize = (CGSize){303, 184};

static CGPoint const kHomeLabelExplainDefaultPoint = (CGPoint){387, 440};
static CGPoint const kOlympyadOrProfileLabelExplainDefaultPoint = (CGPoint){387, 338};
static CGPoint const kStatisticLabelExplainDefaultPoint = (CGPoint){387, 360};

@interface PopupForDefaultChildViewController ()

@property (strong, nonatomic) IBOutlet UITextView *explainDefaultLabel;
@property (strong, nonatomic) IBOutlet UITextView *explainLabel;
@property (strong, nonatomic) IBOutlet UILabel *buttonLabel;

- (IBAction)onCancel:(UIButton *)sender;
- (IBAction)onTapOK:(UIButton *)sender;

@end

@implementation PopupForDefaultChildViewController

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
    // Do any additional setup after loading the view from its nib.
    [self updateContent];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setExplainDefaultLabel:nil];
    [self setExplainLabel:nil];
    [super viewDidUnload];
}

- (IBAction)onCancel:(UIButton *)sender
{
    if (self.onFinish) {
        self.onFinish();
    }
    [self dismiss];
}

- (IBAction)onTapOK:(UIButton *)sender
{
    self.isOkSelected = YES;
    [self dismiss];
}

- (void)updateContent
{
    switch (self.popupType) {
        case kPopupForDefaultTypeHome:
            [self preparePopupWithPosition:kHomeLabelExplainDefaultPoint andSize:kHomeLabelExplainSize];
            break;
        case kPopupForDefaultTypeProfile:
            [self preparePopupWithPosition:kOlympyadOrProfileLabelExplainDefaultPoint
                                   andSize:kOlympiadOrProfileLabelExplainSize];
            break;
        case kPopupForDefaultTypeStatistic:
            [self preparePopupWithPosition:kStatisticLabelExplainDefaultPoint andSize:kStatisticLabelExplainSize];
            break;
            
        default:
            break;
    }
}


- (void)preparePopupWithPosition:(CGPoint)point andSize:(CGSize)size
{
    self.explainDefaultLabel.frame = CGRectMake(point.x, point.y,
                                               self.explainDefaultLabel.frame.size.width,
                                               self.explainDefaultLabel.frame.size.height);
#warning Sanya: don't use Explain default label!
    if (self.popupType == kPopupForDefaultTypeHome) {
        self.explainDefaultLabel.text = NSLocalizedString(@"Explain default label", nil);
        self.buttonLabel.text = NSLocalizedString(@"Continue", nil);
    } else {
        self.explainDefaultLabel.text = NSLocalizedString(@"Explain default label4", nil);
        self.buttonLabel.text = NSLocalizedString(@"Cancel", nil);
    }
    
    self.explainLabel.frame = CGRectMake(self.explainLabel.frame.origin.x,
                                         self.explainLabel.frame.origin.y,
                                         size.width,
                                         size.height);
    
    NSString *tempString = [NSString stringWithFormat:@"Explain default label%i",self.popupType];
    self.explainLabel.text = NSLocalizedString(tempString, nil);

}


@end
