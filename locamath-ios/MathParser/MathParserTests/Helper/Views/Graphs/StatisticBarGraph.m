//
//  StatisticBarGraph.m
//  Mathematic
//
//  Created by Developer on 28.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StatisticBarGraph.h"
#import "StatisticManager.h"

@interface StatisticBarGraph ()

@property (strong, nonatomic) NSArray *values;
@property (strong, nonatomic) NSArray *titles;

@end

@implementation StatisticBarGraph

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        self.allowPinchScaling = NO;
    }
    return self;
}

- (void)configurateWithDateType:(DateType)dateType andTaskStatus:(TaskStatus)taskStatus withConcretError:(ActionErrorType)actionError;
{
    StatisticManager *statisticManager = [[StatisticManager alloc] initWithDateType:dateType
                                                                         taskStatus:taskStatus
                                                                           taskType:kTaskTypeCommon
                                                                              error:actionError];
    
    self.values = statisticManager.values;
    self.titles = statisticManager.titles;
    
    [self configureGraph];
    [self configurePlots];
    [self configureAxes];
}

#pragma mark - Chart Methods

- (void)configureGraph
{
    NSInteger max = 0;
    
    for (NSNumber *number in self.values) {
        NSInteger num = [number integerValue];
        if (num > max) {
            max = num;
        }
    }
    
    CPTGraph *graph = [[CPTXYGraph alloc] initWithFrame:self.bounds];
    
    graph.plotAreaFrame.masksToBorder = NO;
    
    self.hostedGraph = graph;
    
    graph.paddingBottom = 80.0f;
    graph.paddingLeft  = 0.0f;
    graph.paddingTop    = 50.0f;
    graph.paddingRight  = 0.0f;
    
    CGFloat xMin = -0.5f;
    CGFloat xMax = [self.titles count];
    
    if (xMax < 7.0) {
        xMax = 7.0;
    }
    
    CGFloat yMin = 0.0f;
    CGFloat yMax = max + 25;
    
    CPTXYPlotSpace *plotSpace = (CPTXYPlotSpace *) graph.defaultPlotSpace;
    plotSpace.xRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(xMin) length:CPTDecimalFromFloat(xMax)];
    plotSpace.yRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(yMin) length:CPTDecimalFromFloat(yMax)];
}

- (void)configurePlots
{
    CPTBarPlot *plot = [[CPTBarPlot alloc] init];
    plot.fill = [CPTFill fillWithColor:[CPTColor colorWithComponentRed:190.0f/255.0f
                                                                 green:203.0f/255.0f
                                                                  blue:103.0f/255.0f
                                                                 alpha:1.0]];
    plot.lineStyle = nil;
    plot.barCornerRadius = 1.0;
    plot.dataSource = self;
    plot.delegate = self;
    
    CPTGraph *graph = self.hostedGraph;
    [graph addPlot:plot toPlotSpace:graph.defaultPlotSpace];
}

#pragma mark - CPTPlotDataSource methods

- (NSUInteger)numberOfRecordsForPlot:(CPTPlot *)plot
{
	return [self.titles count];
}

- (NSNumber *)numberForPlot:(CPTPlot *)plot field:(NSUInteger)fieldEnum recordIndex:(NSUInteger)index
{
	if ((fieldEnum == CPTBarPlotFieldBarTip) && (index < [self.titles count])) {
        return [self.values objectAtIndex:index];
    }
    
    return [NSDecimalNumber numberWithUnsignedInteger:index];
}

- (CPTLayer *)dataLabelForPlot:(CPTPlot *)plot recordIndex:(NSUInteger)idx
{
    plot.labelOffset = 0;
    
    CPTMutableTextStyle *style = [CPTMutableTextStyle textStyle];
    style.color = [[CPTColor whiteColor] colorWithAlphaComponent:1];
    style.fontName = @"Helvetica-Bold";
    style.fontSize = 12.0f;
    
    NSString *valueString = [NSString stringWithFormat:@"%@", [self.values objectAtIndex:idx]];
    
    return [[CPTTextLayer alloc] initWithText:valueString style:style];
}

- (void)configureAxes
{
    CPTXYAxisSet *axisSet = (CPTXYAxisSet *) self.hostedGraph.axisSet;
    axisSet.xAxis.hidden = YES;
    axisSet.yAxis.hidden = YES;
    
    CPTMutableTextStyle *style = [CPTMutableTextStyle textStyle];
    style.color = [[CPTColor whiteColor] colorWithAlphaComponent:1];
    style.fontName = @"Helvetica-Bold";
    style.fontSize = 12.0f;
    
    NSMutableArray *labels = [[NSMutableArray alloc] initWithCapacity:5];
    int idx =0;
    
    for (NSString *string in self.titles) {
        CPTAxisLabel *label = [[CPTAxisLabel alloc] initWithText:string textStyle:style];
        label.rotation = M_PI/2;
        label.tickLocation = CPTDecimalFromInt(idx);
        label.offset = 10.0f;
        [labels addObject:label];
        idx+=1;
    }
    
    axisSet.xAxis.axisLabels = [NSSet setWithArray:labels];
}


@end
