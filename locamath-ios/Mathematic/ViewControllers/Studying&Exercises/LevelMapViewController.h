//
//  LevelMap.h
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"

@class BaseLevelViewController, MTStarView;

@interface LevelMapViewController : BaseViewController <UIScrollViewDelegate> {

}

@property (weak, nonatomic) IBOutlet UIScrollView *theScrollView;
@property (strong, nonatomic) UIViewController *currentLevel;
@property (strong, nonatomic) BaseLevelViewController * level;
@property (unsafe_unretained, nonatomic) LevelType levelType;
@property (strong, nonatomic) IBOutletCollection(MTStarView) NSArray *stars;

- (void)updateChild;
- (void)showSelectionChildForSchoolMode;

@end
