//
//  StatisticPieGraph.m
//  Mathematic
//
//  Created by Developer on 01.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StatisticPieGraph.h"
#import "StatisticManager.h"

@interface StatisticPieGraph ()

@property (strong, nonatomic) NSMutableArray *values;

@end

@implementation StatisticPieGraph

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        
        self.values = [NSMutableArray new];
        
        NSMutableArray * arrayErrorTypeCalculation = [NSMutableArray new];
        [arrayErrorTypeCalculation addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation]];
        [arrayErrorTypeCalculation addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation | kActionErrorTypeStructure]];
        
        NSMutableArray * arrayErrorTypeStructure = [NSMutableArray new];
        [arrayErrorTypeStructure addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeStructure]];
        [arrayErrorTypeStructure addObjectsFromArray:[DataUtils tasksWithActionErrorType:kActionErrorTypeCalculation | kActionErrorTypeStructure]];
        
        [self.values addObject:arrayErrorTypeCalculation];
        [self.values addObject:arrayErrorTypeStructure];
        
        if ([[self.values objectAtIndex:0] count] == 0 &&
            [[self.values objectAtIndex:1] count] == 0)
        {
            [self setHidden:YES];
        }
    
        self.allowPinchScaling = NO;
        [self initPlot];
    }
    return self;
}

#pragma mark - Chart behavior

- (void)initPlot
{
    [self configureGraph];
    [self configureChart];
}

- (void)configureGraph
{
	CPTGraph *graph = [[CPTXYGraph alloc] initWithFrame:self.bounds];
	self.hostedGraph = graph;
	graph.paddingLeft = 0.0f;
	graph.paddingTop = 0.0f;
	graph.paddingRight = 0.0f;
	graph.paddingBottom = 0.0f;
	graph.axisSet = nil;
	CPTMutableTextStyle *textStyle = [CPTMutableTextStyle textStyle];
	textStyle.color = [CPTColor grayColor];
	textStyle.fontName = @"Helvetica-Bold";
	textStyle.fontSize = 16.0f;
}

- (void)configureChart
{
	CPTGraph *graph = self.hostedGraph;
	CPTPieChart *pieChart = [[CPTPieChart alloc] init];
	pieChart.dataSource = self;
	pieChart.delegate = self;
	pieChart.pieRadius = (self.bounds.size.height * 0.7) / 2;
	pieChart.identifier = graph.title;
	pieChart.startAngle = M_PI_4;
	pieChart.sliceDirection = CPTPieDirectionClockwise;
	[graph addPlot:pieChart];
}


#pragma mark - CPTPlotDataSource methods

- (CPTFill *)sliceFillForPieChart:(CPTPieChart *)pieChart recordIndex:(NSUInteger)index
{
    if (index == 0) {
        CPTColor *sliceColor = [CPTColor colorWithComponentRed:37.0f/255.0f
                                                         green:175.0f/255.0f
                                                          blue:223.0f/255.0f
                                                         alpha:1.0f];
        return [CPTFill fillWithColor:sliceColor];
    } else {
        CPTColor *sliceColor = [CPTColor colorWithComponentRed:252.0f/255.0f
                                                         green:13.0f/255.0f
                                                          blue:27.0f/255.0f
                                                         alpha:1.0f];
        return [CPTFill fillWithColor:sliceColor];
    }
}


- (NSUInteger)numberOfRecordsForPlot:(CPTPlot *)plot
{
	return 2;
}

- (NSNumber *)numberForPlot:(CPTPlot *)plot field:(NSUInteger)fieldEnum recordIndex:(NSUInteger)index
{
	if (CPTPieChartFieldSliceWidth == fieldEnum) {
		return [NSNumber numberWithInteger:[[self.values objectAtIndex:index] count]];
	}
	return [NSDecimalNumber zero];
}

- (CPTLayer *)dataLabelForPlot:(CPTPlot *)plot recordIndex:(NSUInteger)idx
{
    plot.labelOffset = 5;
    CPTMutableTextStyle *style = [CPTMutableTextStyle textStyle];
    style.color = [[CPTColor whiteColor] colorWithAlphaComponent:1];
    style.fontName = @"Helvetica-Bold";
    style.fontSize = 12.0f;
    NSString *valueString = [NSString stringWithFormat:@"%@",
                             [NSNumber numberWithInteger:[[self.values objectAtIndex:idx] count]]];
    valueString = [valueString integerValue] == 0 ? @"" : valueString;
        
    return [[CPTTextLayer alloc] initWithText:valueString style:style];
}

@end
