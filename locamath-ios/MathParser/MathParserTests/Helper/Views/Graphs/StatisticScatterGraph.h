//
//  StatisticScatterGraph.h
//  Mathematic
//
//  Created by Developer on 28.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class StatisticManager;

@interface StatisticScatterGraph : CPTGraphHostingView <CPTBarPlotDataSource, CPTBarPlotDelegate>

- (void)configurateWithDateType:(DateType)dateType;

@end
