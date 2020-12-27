//
//  MTLevelView.h
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "MTLevelViewDelegate.h"
@class Level;

@interface MTLevelView : UIView

@property (strong, nonatomic) Level *level;
@property (weak, nonatomic) id <MTLevelViewDelegate> delegate;
@property (weak, nonatomic) IBOutlet UILabel *title;
@property (weak, nonatomic) IBOutlet UILabel *currentScoreLabel;
@property (weak, nonatomic) IBOutlet UILabel *levelScoreLabel;
@property (weak, nonatomic) IBOutlet UILabel *solvedTasksLabel;
@property (weak, nonatomic) IBOutlet UILabel *startedTasksLabel;
@property (weak, nonatomic) IBOutlet UILabel *totalTasksLabel;
@property (unsafe_unretained, nonatomic) CGSize originalSize;
@property (weak, nonatomic) IBOutlet UIButton *levelButton;

- (void)updateViewWithLevelInfo;
- (IBAction)onTapButton:(id)sender;


@end
