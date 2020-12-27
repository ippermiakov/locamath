//
//  AboutPopupViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 21.10.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AboutPopupViewController.h"
#import "TTTAttributedLabel+ColorString.h"
#import "MFMailComposeViewController+BlocksKit.h"

@interface AboutPopupViewController () <MFMailComposeViewControllerDelegate>

- (IBAction)onTapClose:(id)sender;
- (IBAction)onHelpEmail:(id)sender;
- (IBAction)onLikeLocaMath:(id)sender;
- (IBAction)onGoWWW:(id)sender;

@property (strong, nonatomic) IBOutlet TTTAttributedLabel *colorLabel1;
@property (strong, nonatomic) IBOutlet TTTAttributedLabel *colorLabel2;
@property (strong, nonatomic) IBOutlet TTTAttributedLabel *colorLabel3;

@end

@implementation AboutPopupViewController

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
    
    [self.colorLabel1 setColoredStrings:[NSArray arrayWithObjects:@"LocaMath", @"#Loca_Math", nil]];
    [self.colorLabel2 setColoredStrings:[NSArray arrayWithObjects:@"LocaMath", @"#Loca_Math", nil]];
    [self.colorLabel3 setColoredStrings:[NSArray arrayWithObjects:@"LocaMath", @"#Loca_Math", nil]];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)onTapClose:(id)sender
{
    [self dismiss];
}

- (IBAction)onHelpEmail:(id)sender
{
    if ([MFMailComposeViewController canSendMail])
    {
        MFMailComposeViewController *mailCompose = [[MFMailComposeViewController alloc] init];
        [mailCompose setSubject:@"Message"];
        NSArray *toRecipients = [NSArray arrayWithObjects:@"locamath@loca-app.com", nil];
        [mailCompose setToRecipients:toRecipients];
        
        mailCompose.completionBlock = ^(MFMailComposeViewController *mailComposeController, MFMailComposeResult result, NSError *error){
            switch (result)
            {
                case MFMailComposeResultCancelled:
                    NSLog(@"Mail cancelled: you cancelled the operation and no email message was queued.");
                    break;
                case MFMailComposeResultSaved:
                    NSLog(@"Mail saved: you saved the email message in the drafts folder.");
                    break;
                case MFMailComposeResultSent:
                    NSLog(@"Mail send: the email message is queued in the outbox. It is ready to send.");
                    break;
                case MFMailComposeResultFailed:
                    NSLog(@"Mail failed: the email message was not saved or queued, possibly due to an error.");
                    break;
                default:
                    NSLog(@"Mail not sent.");
                    break;
            }
        };
        
        [self presentModalViewController:mailCompose animated:NO];
    }
}

- (IBAction)onLikeLocaMath:(id)sender
{
    if ([[UIApplication sharedApplication] canOpenURL:[NSURL URLWithString: @"fb://"]]){
        [[UIApplication sharedApplication] openURL:[NSURL URLWithString:NSLocalizedString(@"fb_Locamath", nil)]];
    } else {
        NSString *urlString = [NSLocalizedString(@"https://www.facebook.com/Locamath", nil)
                               stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
        [[UIApplication sharedApplication] openURL:[NSURL URLWithString:urlString]];
    }
}

- (IBAction)onGoWWW:(id)sender
{
    [[UIApplication sharedApplication] openURL:[NSURL URLWithString:@"http://www.locamath.com"]];
}

@end
