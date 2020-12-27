//
//  MTStarView.h
//  Mathematic
//
//  Created by alexbutenko on 8/14/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@class Level;

@interface MTStarView : UIView

@property (strong, nonatomic) Level *level;
@property (unsafe_unretained, nonatomic) BOOL isCompleted;

- (void)updateView;

@end
