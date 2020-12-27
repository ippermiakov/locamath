//
//  StatisticBarGraph.h
//  Mathematic
//
//  Created by Developer on 28.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class StatisticManager;

@interface StatisticBarGraph : CPTGraphHostingView <CPTBarPlotDataSource, CPTBarPlotDelegate>

- (void)configurateWithDateType:(DateType)dateType andTaskStatus:(TaskStatus)taskStatus withConcretError:(ActionErrorType)actionError;

@end
