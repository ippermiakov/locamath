//
//  GameAlertViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 16.07.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "GameAlertViewController.h"

@interface GameAlertViewController ()

@property (strong, nonatomic) IBOutlet UITextView *textMessage;
@property (strong, nonatomic) IBOutlet UITextView *bigTextNessage;
@property (strong, nonatomic) IBOutlet UILabel *onOK;

@property (strong, nonatomic) IBOutlet UIView *littelAlert;
@property (strong, nonatomic) IBOutlet UIView *bigAlert;

- (IBAction)onOk:(id)sender;

@property (strong, nonatomic) UIView *presenterView;
@end

@implementation GameAlertViewController

+ (GameAlertViewController *)sharedInstance
{
    static dispatch_once_t pred;
    static GameAlertViewController *sharedInstance = nil;
    dispatch_once(&pred, ^{
        sharedInstance = [self new];
    });
    
    return sharedInstance;
}


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
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setTextMessage:nil];
    [self setOnOK:nil];
    [self setLittelAlert:nil];
    [self setBigAlert:nil];
    [self setBigTextNessage:nil];
    [super viewDidUnload];
}

- (IBAction)onOk:(id)sender
{
    if (self.onFinish) {
        self.onFinish();
        self.onFinish = nil;
    }
    
    [self.view removeFromSuperview];
}

+ (void)showGameAlertWithMessageError:(NSError *)error withPresenter:(UIView *)view
{
    [[GameAlertViewController sharedInstance] presentOnView:view withString:[error localizedDescription]];
}

+ (void)showGameAlertWithMessage:(NSString *)message withPresenter:(UIView *)view
{
    [[GameAlertViewController sharedInstance] presentOnView:view withString:message];
}

- (void)presentOnView:(UIView *)view withString:(NSString *)string
{
    self.presenterView = view;
    
    [view addSubview:self.view];
    
    if ([string length] > 20) {
        self.littelAlert.hidden = YES;
        self.bigAlert.hidden = NO;
    } else {
        self.bigAlert.hidden = YES;
        self.littelAlert.hidden = NO;
    }
    
    self.textMessage.text = string;
    self.bigTextNessage.text = string;
}

@end
