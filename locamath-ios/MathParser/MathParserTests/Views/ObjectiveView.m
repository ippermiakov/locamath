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
#define OBJECTIVE_MINSIZEFONT 16.0f

@interface ObjectiveView ()

@property (nonatomic, copy) NSString *text;
@property (strong, nonatomic) NSArray *words;
@property (strong, nonatomic) NSMutableArray *elements;
@property (unsafe_unretained, nonatomic) BOOL isTest;

@end

@implementation ObjectiveView

- (id)initWithTask:(id<AbstractTask>)task
{
    self = [super initWithFrame:CGRectMake(110.0f, 90.0f, 382.0f, 90.0f)];
    
    if (self) {
        self.elements = [NSMutableArray new];
        self.text = task.objective;
        self.task = task;
        [self createElements];
        self.scrollEnabled = YES;
    }
    return self;
}

- (void)createElements
{
    TTTAttributedLabel *label = [[TTTAttributedLabel alloc] initWithFrame:CGRectMake(0, 0, 350, 80)];
    [label setFont:[UIFont fontWithName:[UIView defaultBoldFontName] size:OBJECTIVE_MINSIZEFONT]];
    [label setBackgroundColor:[UIColor clearColor]];
    [label setTextColor:[UIColor blackColor]];
    
    label.numberOfLines = 0;
    
    [label MTTextViewWithLabel:label withTask:self.task forView:self];
}

@end
