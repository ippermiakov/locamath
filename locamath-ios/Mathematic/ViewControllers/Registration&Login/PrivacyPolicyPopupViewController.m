//
//  CheckYearPopupViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 04.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PrivacyPolicyPopupViewController.h"
#import "MTHTTPClient.h"

@interface PrivacyPolicyPopupViewController ()

@property (weak, nonatomic) IBOutlet UITextField *answerTextField;
@property (weak, nonatomic) IBOutlet UITextField *taskTextField;
@property (unsafe_unretained, nonatomic) NSUInteger correctAnswerValue;

- (IBAction)onCancel:(id)sender;
- (IBAction)onOk:(id)sender;

- (IBAction)onChangeAnswer:(UITextField *)sender;

@end

static NSUInteger const kAnswerNumberCount = 3;

@implementation PrivacyPolicyPopupViewController

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
    
    const NSUInteger lowerBound = 10;
    const NSUInteger upperBound = 100;
    
    NSUInteger firstItem = 0;
    NSUInteger secondItem = 0;
    NSUInteger thirdItem = 0;
    
    do {
        firstItem = arc4random_uniform(100);
        secondItem = arc4random_uniform(100);
        thirdItem = arc4random_uniform(100);
        self.correctAnswerValue = firstItem + secondItem - thirdItem;
//        NSLog(@"firstItem: %i secondItem: %i thirdItem: %i answer: %i", firstItem, secondItem, thirdItem, self.correctAnswerValue);

    } while (firstItem + secondItem < thirdItem ||
             firstItem < lowerBound || secondItem < lowerBound || thirdItem < lowerBound ||
             self.correctAnswerValue >= upperBound);
    
    self.taskTextField.text = [NSString stringWithFormat:@"%i + %i - %i = ", firstItem, secondItem, thirdItem];
    
#ifdef DEBUG
    self.answerTextField.text = [NSString stringWithFormat:@"%i", self.correctAnswerValue];
#endif
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
     if (![[MTHTTPClient sharedMTHTTPClient] isReachable] && self.shouldPassDefaultChildCheck) {
         self.onEnd = nil;
         [self dismiss];
//uncomment to check if message have localizations
//         
//         [[MTHTTPClient sharedMTHTTPClient] parentUpdateLocationWithSuccess:^(BOOL finished, NSError *error) {
//             [self presentNextViewController];
//         } failure:^(BOOL finished, NSError *error) {
//             NSLog(@"%@", [error localizedDescription]);
//         }];
         
         [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"The Internet connection appears to be offline.", nil)];
     }
}

- (void)viewDidUnload
{
    [super viewDidUnload];
}

- (IBAction)onCancel:(id)sender
{
    self.onEnd = nil;
    [self dismiss];
}

- (IBAction)onOk:(id)sender
{
    self.isPassedCheck = [self.answerTextField.text integerValue] == self.correctAnswerValue;
    [self dismissToRootViewController];
    
    if (self.isPassedCheck) {
        if (self.onFinish) {
            self.onFinish();
        }
    }
    //NSLog(@"self.isPassedCheck : %@", self.isPassedCheck ? @"YES" : @"NO");
}

- (IBAction)onChangeAnswer:(UITextField *)sender
{
    if ([sender.text length] > kAnswerNumberCount) {
        sender.text = [sender.text substringFromIndex:kAnswerNumberCount];
    }
}

@end
