//
//  MTToolsView.h
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface MTToolsView : UIView

@property (strong, nonatomic) IBOutletCollection(UIView) NSMutableArray *tools;
@property (strong, nonatomic) NSMutableArray *displayedTools;
@property (unsafe_unretained, nonatomic) CGSize distanceBetweenElements;
@property (unsafe_unretained, nonatomic) CGSize distanceBetweenElementCenters;
@property (unsafe_unretained, nonatomic) CGFloat rowWidth;
@property (unsafe_unretained, nonatomic) BOOL isTaskCompleted;
@property (weak, nonatomic) UIView *overlayView;

- (void)excludeAllCharacters;
- (void)excludeDisplayingCharacters:(NSArray *)characters;
- (void)displayAdditionalViews:(NSArray *)views;
- (void)reloadDataWithViews:(NSArray *)views;

@end