//
//  OlympiadTask.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 01.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadTask.h"
#import "OlympiadAction.h"
#import "OlympiadLevel.h"
#import "OlympiadSolvingViewController.h"

@implementation OlympiadTask

@dynamic identifier;
@dynamic isAnyAnswerApplicable;
@dynamic alignmentTypeNumber;
@dynamic numberTask;
@dynamic tryCounter;
@dynamic objective;
@dynamic points;
@dynamic tools;
@dynamic baseTools;
@dynamic actions;
@dynamic level;
@dynamic child;
@dynamic lastChangeDate;
@dynamic statusNumber;
@dynamic currentScore;
@dynamic status;
@dynamic solutionHint;

- (BOOL)isCorrect
{
    BOOL success = YES;
    
    for (OlympiadAction *action in self.actions) {
        [action updateIsCorrect];
        if ( ! [[action isCorrect] boolValue]) {
            success = NO;
        }
    }
    
    return success;
}

- (NSUInteger)calculateCurrentScore
{
    CGFloat rawPoints = 0;
    NSUInteger resultPoints = 0;
    if ([self.tryCounter integerValue] > 0) {
        rawPoints = [self.points integerValue] / [self.tryCounter integerValue];
        
        CGFloat auxPoints = rawPoints / 10;
        auxPoints += 0.5;
        NSUInteger roundedPoints = auxPoints;
        resultPoints = roundedPoints * 10;
    } else {
        resultPoints = [self.points integerValue];
    }

    return resultPoints;
}

- (NSUInteger)longestToolsLength
{
    NSUInteger longestLength = 0;
    
    for (NSString *tool in self.tools) {
        if (tool.length > longestLength) {
            longestLength = tool.length;
        }
    }
//    NSLog(@" tools length : %i", longestLength * [[[self.actions anyObject] numOfToolsToFill] integerValue]);

    return longestLength * [[[self.actions anyObject] numOfToolsToFill] integerValue];
}

#pragma mark - Setters&Getters

- (void)setTryCounter:(NSNumber *)tryCounter
{
    if (self.status != kTaskStatusSolved) {
        [self willChangeValueForKey:@"tryCounter"];
        [self setPrimitiveValue:tryCounter forKey:@"tryCounter"];
        [self didChangeValueForKey:@"tryCounter"];
    }
}

- (HintsAlignmentType)alignmentType
{
    return [self.alignmentTypeNumber integerValue];
}

- (void)setAlignmentType:(HintsAlignmentType)alignmentType
{
    [self willChangeValueForKey:@"alignmentTypeNumber"];
    [self setPrimitiveValue:@(alignmentType) forKey:@"alignmentTypeNumber"];
    [self didChangeValueForKey:@"alignmentTypeNumber"];
}

- (NSNumber *)isOneToolToOneAnswerMapping
{
    return @(self.alignmentType != HintsAlignmentTypeNone);
}

#pragma mark - Abstract

- (Class)controllerClass
{
    return [OlympiadSolvingViewController class];
}

- (NSString *)statisticDescription
{
    return [self taskStatisticDescription];
}

- (void)setStatus:(TaskStatus)status
{
    [super setStatus:status];

    if (status == kTaskStatusSolved) {
        self.currentScore = @([self calculateCurrentScore]);
    }
}

- (NSString *)errorTaskDescription
{
    NSDateFormatter *df = [[NSDateFormatter alloc] init];
    [df setDateStyle:NSDateFormatterLongStyle];
    [df setTimeStyle:NSDateFormatterNoStyle];
    NSString *dateString = [df stringFromDate:self.lastChangeDate];
    return [NSString stringWithFormat:@"%@; %@; #%@", dateString, self.level.name, self.numberTask];
}

@end
