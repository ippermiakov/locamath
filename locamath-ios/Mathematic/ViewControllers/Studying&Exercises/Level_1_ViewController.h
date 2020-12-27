//
//  Level_1.h
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "BaseLevelViewController.h"
#import "MTLevelViewDelegate.h"


@interface Level_1_ViewController : BaseLevelViewController

@property (strong, nonatomic) IBOutletCollection(UIView) NSArray *levelsViews;

- (void)updateLevelsView;

@end
