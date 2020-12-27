//
//  AnimationViewController.h
//  Mathematic
//
//  Created by Developer on 23.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"

@class GifPlayerView;

@interface AnimationViewController : BaseViewController

@property (strong, nonatomic) IBOutlet GifPlayerView *gifPlayerView;

- (id)initWithAnimationFileName:(NSString *)name;
- (IBAction)onTapClose:(id)sender;

@end
