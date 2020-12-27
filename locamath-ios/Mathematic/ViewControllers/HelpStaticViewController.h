//
//  Help_1_ViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 22.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PresentableViewController.h"

@class HelpPage;
@class GifPlayerView;

@interface HelpStaticViewController : PresentableViewController

@property (strong, nonatomic) HelpPage *help;


@property (strong, nonatomic) IBOutlet UILabel *example1;
@property (strong, nonatomic) IBOutlet UILabel *example2;

@property (strong, nonatomic) IBOutlet UIImageView *image1;
@property (strong, nonatomic) IBOutlet UIImageView *image2;

@property (strong, nonatomic) IBOutlet UILabel *girlPhrase;
@property (strong, nonatomic) IBOutlet UILabel *boyPhrase;
@property (unsafe_unretained, nonatomic) BOOL isLastPage;

@property (strong, nonatomic) IBOutlet GifPlayerView *gifPlayerView;

- (IBAction)onTapContinue:(id)sender;
- (IBAction)onTapHome:(id)sender;

@end
