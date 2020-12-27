//
//  Objective.m
//  Mathematic
//
//  Created by Alexander on 10/30/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ObjectiveView.h"
#import "MTMovableView.h"
#import "TTTAttributedLabel+MTTextView.h"
#import "UIView+LWAutoFont.h"

#define TKOBJECTIVEVIEW_WIDTH    352.0f
#define TKOBJECTIVE_MARGIN_WIDTH 5.0f
#define TKOBJECTIVE_MARGIN_HEIGHT 1.0f

#define OBJECTIVE_MAXSIZEFONT 24.0f
#define OBJECTIVE_MINSIZEFONT 20.0f
#define OBJECTIVE_MINSIZEFONT_SMALL 15.0f
@interface ObjectiveView ()

@property (nonatomic, copy) NSString *text;
@property (strong, nonatomic) NSArray *words;
@property (strong, nonatomic) NSMutableArray *elements;
@property (unsafe_unretained, nonatomic) BOOL isTest;
@property (unsafe_unretained, nonatomic) BOOL withBigFont;;
@property (strong, nonatomic) UIColor *textColor;

@end

@implementation ObjectiveView

- (id)initWithTask:(id<AbstractTask>)task
{
    self = [super initWithFrame:CGRectMake(110.0f, 80.0f, 350.0f, 80.0f)];
    
    if (self) {
        self.elements = [NSMutableArray new];
        self.text = task.objective;
        self.task = task;
        [self createElements];
        self.scrollEnabled = YES;
    }
    return self;
}

- (id)initWithTask:(id<AbstractTask>)task frame:(CGRect)rect
{
    self = [super initWithFrame:rect];
    
    if (self) {
        self.elements = [NSMutableArray new];
        self.text = task.objective;
        self.task = task;
        [self createElements];
        self.scrollEnabled = YES;
    }
    return self;
}

- (id)initWithTask:(id<AbstractTask>)task andColor:(UIColor *)textColor
{
    self = [super initWithFrame:CGRectMake(43.0f, 117.0f, 500.0f, 150.0f)];
    
    if (self) {
        self.elements = [NSMutableArray new];
        self.text = task.objective;
        self.task = task;
        self.textColor = textColor;
        self.withBigFont = YES;
        [self createElements];
        self.scrollEnabled = YES;
    }
    return self;
}


- (void)createElements
{
    TTTAttributedLabel *label = [[TTTAttributedLabel alloc] initWithFrame:CGRectMake(0, 0, self.frame.size.width, self.frame.size.height)];
    if (self.withBigFont) {
        [label setFont:[UIFont fontWithName:[UIView defaultBoldFontName] size:OBJECTIVE_MINSIZEFONT]];
    } else {
        [label setFont:[UIFont fontWithName:[UIView defaultBoldFontName] size:OBJECTIVE_MINSIZEFONT_SMALL]];
    }
    
    [label setBackgroundColor:[UIColor clearColor]];
    [label setTextColor:[UIColor blackColor]];
    if (self.textColor) {
        [label setTextColor:self.textColor];
    }
    
    label.numberOfLines = 0;
    label.tag = kRightTextPositionsTag;
    
    [label MTTextViewWithLabel:label withTask:self.task forView:self];
    //if label was truncated, make sure that it is still cover whole line (especially for arabic to be right aligned)
    label.frame = (CGRect){label.frame.origin, self.frame.size.width, label.frame.size.height};
}

@end
