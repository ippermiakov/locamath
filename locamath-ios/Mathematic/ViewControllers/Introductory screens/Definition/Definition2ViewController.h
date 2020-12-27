//
//  Definition2ViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 13.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DefinitionBaseViewController.h"

@class GifPlayerView;

@interface Definition2ViewController : DefinitionBaseViewController

@property (strong, nonatomic) IBOutlet GifPlayerView *gifPlayerView;
@property (strong, nonatomic) IBOutlet UILabel *girlLabel;
@property (strong, nonatomic) IBOutlet UILabel *boyLabel;

- (IBAction)onTapContinue:(id)sender;
- (IBAction)onTapBack:(id)sender;

@end
