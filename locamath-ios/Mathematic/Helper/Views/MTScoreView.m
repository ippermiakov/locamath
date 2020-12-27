//
//  MTScoreView.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 18.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTScoreView.h"
#import "OlympiadTask.h"

@implementation MTScoreView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setTask:(OlympiadTask *)task
{
    _task = task;
    
    self.bottomLabel.text = [task.points stringValue];
    
    if (task.isCorrect) {
        self.topLabel.text = [task.currentScore stringValue];
    }
}

/*
// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect
{
    // Drawing code
}
*/

@end
