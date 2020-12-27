//
//  StatisticScatterGraph.m
//  Mathematic
//
//  Created by Developer on 28.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StatisticScatterGraph.h"
#import "StatisticManager.h"

@interface StatisticScatterGraph ()

@property (strong, nonatomic) NSArray *greenValues;
@property (strong, nonatomic) NSArray *blueValues;
@property (strong, nonatomic) NSArray *yellowValues;
@property (strong, nonatomic) NSArray *greenTitles;
@property (strong, nonatomic) NSArray *blueTitles;
@property (strong, nonatomic) NSArray *yellowTitles;
@property (strong, nonatomic) NSArray *maxTitles;

@end

@implementation StatisticScatterGraph

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    
    if (self) {
        self.allowPinchScaling = NO;
    }
    
    return self;
}

- (void)configurateWithDateType:(DateType)dateType
{
    StatisticManager *commonStatisticManager = [[StatisticManager alloc] initWithDateType:dateType
                                                                               taskStatus:kTaskStatusStarted
                                                                                 taskType:kTaskTypeCommon
                                                                                    error:nil];
    self.greenValues = commonStatisticManager.values;
    self.greenTitles = commonStatisticManager.titles;
    
    StatisticManager *trainingStatisticManager = [[StatisticManager alloc] initWithDateType:dateType
                                                                                 taskStatus:kTaskStatusStarted
                                                                                   taskType:kTaskTypeTraining
                                                                                      error:nil];
    self.blueValues = trainingStatisticManager.values;
    self.blueTitles = trainingStatisticManager.titles;
    
    StatisticManager *testStatisticManager = [[StatisticManager alloc] initWithDateType:dateType
                                                                             taskStatus:kTaskStatusStarted
                                                                               taskType:kTaskTypeTest
                                                                                  error:nil];
    self.yellowValues = testStatisticManager.values;
    self.yellowTitles = testStatisticManager.titles;
    
    
    [self configureGraph];
    [self configurePlots];
    [self configureAxes];
}


- (void)configureGraph
{
    CPTXYGraph *graph=[[CPTXYGraph alloc] initWithFrame:self.bounds];
    
    self.hostedGraph = graph;
    
    graph.paddingBottom = 0.0f;
    graph.paddingLeft   = 5.0f;
    graph.paddingTop    = 50.0f;
    graph.paddingRight  = 5.0f;
}

- (void)configurePlots
{
    NSInteger maxX = 0;
    
    NSMutableArray *allTitles = [[NSMutableArray alloc] initWithObjects:self.greenTitles, self.blueTitles, self.yellowTitles, nil];
    
    for (NSArray *arr in allTitles) {
        NSInteger num = [arr count];
        if (num > maxX) {
            maxX = num;
            self.maxTitles = arr;
        }
    }
    
    
    NSInteger maxY = 0;
    
    NSMutableArray *allValues = [NSMutableArray new];
    [allValues addObjectsFromArray:self.greenValues];
    [allValues addObjectsFromArray:self.blueValues];
    [allValues addObjectsFromArray:self.yellowValues];
    
    for (NSNumber *number in allValues) {
        NSInteger num = [number integerValue];
        if (num > maxY) {
            maxY = num;
        }
    }
    
    CPTGraph *graph = self.hostedGraph;
    
    
    CPTXYPlotSpace *plotSpace = (CPTXYPlotSpace *)graph.defaultPlotSpace;
    plotSpace.allowsUserInteraction = NO;
    
    CGFloat xMin = -0.5f;
    CGFloat xMax = maxX;
    if (xMax < 7.0) {
        xMax = 7.0;
    }
    CGFloat yMin = -10.0f;
    CGFloat yMax = maxY + 25;
    
    plotSpace.xRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(xMin) length:CPTDecimalFromFloat(xMax)];
    plotSpace.yRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(yMin) length:CPTDecimalFromFloat(yMax)];
    
    // Green plot
    CPTScatterPlot *greenLinePlot = [[CPTScatterPlot alloc] init];
    
    greenLinePlot.identifier = @"Green Plot";
    greenLinePlot.delegate = self;
    greenLinePlot.dataSource     = self;
    greenLinePlot.cachePrecision = CPTPlotCachePrecisionDecimal;
    greenLinePlot.interpolation  = CPTScatterPlotInterpolationCurved;
    [graph addPlot:greenLinePlot];
    
    // Blue plot
    CPTScatterPlot *blueLinePlot = [[CPTScatterPlot alloc] init];
    blueLinePlot.identifier = @"Blue Plot";
    blueLinePlot.delegate = self;
    blueLinePlot.dataSource     = self;
    blueLinePlot.cachePrecision = CPTPlotCachePrecisionDecimal;
    blueLinePlot.interpolation  = CPTScatterPlotInterpolationCurved;
    [graph addPlot:blueLinePlot];
    
    // Yellow plot
    CPTScatterPlot *yellowLinePlot = [[CPTScatterPlot alloc] init];
    yellowLinePlot.identifier = @"Yellow Plot";
    yellowLinePlot.delegate = self;
    yellowLinePlot.dataSource     = self;
    yellowLinePlot.cachePrecision = CPTPlotCachePrecisionDecimal;
    yellowLinePlot.interpolation  = CPTScatterPlotInterpolationCurved;
    [graph addPlot:yellowLinePlot];
    
    //graph.backgroundColor = [[UIColor grayColor] CGColor];
    //graph.plotAreaFrame.fill = [CPTFill fillWithColor:[CPTColor redColor]];
    
    
    graph.plotAreaFrame.paddingBottom = 80;
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
    for (NSString *string in self.maxTitles)
    {
        CPTAxisLabel *label = [[CPTAxisLabel alloc] initWithText:string textStyle:style];
        label.rotation = M_PI/2;
        label.tickLocation = CPTDecimalFromInt(idx);
        label.offset = 10.0f;
        //label.alignment = CPTAlignmentLeft;
        [labels addObject:label];
        idx+=1;
    }
    
    axisSet.xAxis.axisLabels = [NSSet setWithArray:labels];
}


#pragma mark - CPTPlotDataSource methods

- (NSUInteger)numberOfRecordsForPlot:(CPTPlot *)plot
{
    if ([plot.identifier isEqual:@"Green Plot"]) {
        return [self.greenTitles count];
    }
    
    if ([plot.identifier isEqual:@"Blue Plot"]) {
        return [self.blueTitles count];
    }
    
    if ([plot.identifier isEqual:@"Yellow Plot"]) {
        return [self.yellowTitles count];
    }
    
	return 0;
}

- (NSNumber *)numberForPlot:(CPTPlot *)plot field:(NSUInteger)fieldEnum recordIndex:(NSUInteger)index
{
    NSInteger maxTitles = 0;
    
    if ([plot.identifier isEqual:@"Green Plot"]) {
        maxTitles = [self.greenTitles count];
    }
    
    if ([plot.identifier isEqual:@"Blue Plot"]) {
        maxTitles = [self.blueTitles count];
    }
    
    if ([plot.identifier isEqual:@"Yellow Plot"]) {
        maxTitles = [self.yellowTitles count];
    }
    
	if ((fieldEnum == CPTBarPlotFieldBarTip) && (index < maxTitles)) {
        
        if ([plot.identifier isEqual:@"Green Plot"]) {
            return [self.greenValues objectAtIndex:index];
        }
        
        if ([plot.identifier isEqual:@"Blue Plot"]) {
            return [self.blueValues objectAtIndex:index];
        }
        
        if ([plot.identifier isEqual:@"Yellow Plot"]) {
            return [self.yellowValues objectAtIndex:index];
        }
        
    }
    
    return [NSDecimalNumber numberWithUnsignedInteger:index];
}

- (CPTPlotSymbol *)symbolForScatterPlot:(CPTScatterPlot *)plot recordIndex:(NSUInteger)index
{
    CPTMutableLineStyle *lineStyle = [CPTMutableLineStyle lineStyle];
    CPTPlotSymbol *plotSymbol = [CPTPlotSymbol ellipsePlotSymbol];
    
    if ([plot.identifier isEqual:@"Green Plot"])
    {
        lineStyle.lineColor = [CPTColor greenColor];
        
        plotSymbol.lineStyle = lineStyle;
        plotSymbol.symbolType = CPTPlotSymbolTypeEllipse;
        plotSymbol.size = CGSizeMake(8, 8);
        plotSymbol.fill = [CPTFill fillWithColor:[CPTColor greenColor]];
        
        plot.dataLineStyle = lineStyle;
    }
    
    if ([plot.identifier isEqual:@"Blue Plot"])
    {
        lineStyle.lineColor = [CPTColor blueColor];
        
        plotSymbol.lineStyle = lineStyle;
        plotSymbol.symbolType = CPTPlotSymbolTypeEllipse;
        plotSymbol.size = CGSizeMake(8, 8);
        plotSymbol.fill = [CPTFill fillWithColor:[CPTColor blueColor]];
        
        plot.dataLineStyle = lineStyle;
    }
    
    if ([plot.identifier isEqual:@"Yellow Plot"])
    {
        lineStyle.lineColor = [CPTColor yellowColor];
        
        plotSymbol.lineStyle = lineStyle;
        plotSymbol.symbolType = CPTPlotSymbolTypeEllipse;
        plotSymbol.size = CGSizeMake(8, 8);
        plotSymbol.fill = [CPTFill fillWithColor:[CPTColor yellowColor]];
        
        plot.dataLineStyle = lineStyle;
    }
    
    return plotSymbol;
}


@end
