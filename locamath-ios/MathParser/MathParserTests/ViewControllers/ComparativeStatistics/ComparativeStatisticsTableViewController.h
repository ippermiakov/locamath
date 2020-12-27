//
//  ComparativeStatisticsTableViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 27.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseTableViewController.h"

typedef void(^ComparativeStatisticsFinishBlock)();

@interface ComparativeStatisticsTableViewController : BaseTableViewController

- (void)selectWithKey:(NSString *)key andValue:(id)value;
- (void)updateRateChildsWithFinishBlock:(ComparativeStatisticsFinishBlock)finishBlock;

@end
