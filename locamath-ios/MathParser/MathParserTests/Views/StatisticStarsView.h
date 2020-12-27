//
//  StatisticStarsView.h
//  Mathematic
//
//  Created by alexbutenko on 7/1/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface StatisticStarsView : UIView

@property (unsafe_unretained, nonatomic) BOOL isParentsStatistic;

- (void)reloadData;

@end
