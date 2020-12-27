//
//  Task.m
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Task.h"
#import "Action.h"
#import "SolvingViewController.h"
#import "Level.h"

@implementation Task

@dynamic identifier;
@dynamic score;
@dynamic animation;
@dynamic hint;
@dynamic solutions;
@dynamic formula;
@dynamic objective;
@dynamic expressions;
@dynamic answer;
@dynamic lastChangeDate;
@dynamic literal;
@dynamic letters;
@dynamic statusNumber;
@dynamic actions;
@dynamic currentScore;
@dynamic countSolvedActions;
@dynamic numberTask;
@dynamic level;
@dynamic taskType;
@dynamic secondsPerTask;
@dynamic child;
@dynamic status;
@dynamic isAnimationSelected;
@dynamic isHelpSelected;
@dynamic isSchemeSelected;
@dynamic isPencilSelected;
@dynamic taskErrors;

- (NSArray *)actionsWithError
{
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"error != %i", kActionErrorTypeNone];
    NSSet *actionsWithError = [self.actions filteredSetUsingPredicate:predicate];
    return [actionsWithError allObjects];
}

#pragma mark - Abstract

- (Class)controllerClass
{
    return [SolvingViewController class];
}

- (NSString *)statisticDescription
{
    return [self taskStatisticDescription];
}

- (NSString *)errorTaskDescription
{
    NSDateFormatter *df = [[NSDateFormatter alloc] init];
    [df setDateStyle:NSDateFormatterLongStyle];
    [df setTimeStyle:NSDateFormatterNoStyle];
    NSString *dateString = [df stringFromDate:self.lastChangeDate];
    return [NSString stringWithFormat:@"%@; %@; №%@", dateString, self.level.name, self.numberTask];
}

- (NSString *)taskStatisticFixOrErrorDescription
{
    id<AbstractTask>task = self;
    
    NSDateFormatter *df = [[NSDateFormatter alloc] init];
    [df setDateStyle:NSDateFormatterLongStyle];
    [df setTimeStyle:NSDateFormatterNoStyle];
    NSString *dateString = [df stringFromDate:self.lastChangeDate];
    
    NSString *description = [NSString stringWithFormat:@"%@; %@; №%@ (%@)", dateString, task.level.name, task.numberTask, NSLocalizedString(@"Solved", @"Solved level/path")];
    
    return description;
}

- (void)setStatus:(TaskStatus)status
{
    [super setStatus:status];
    
    self.currentScore = [DataUtils scoreForTask:self withActions:[self.actions allObjects]];
    self.countSolvedActions = [DataUtils countOfSolvedActionsFromActions:[self.actions allObjects]];
    
    NSInteger solved = 0;
    NSInteger started = 0;
    
    Level *level = self.level;
    
    for (Task *task in level.tasks) {
        
        if (task.status == kTaskStatusSolved || task.status == kTaskStatusSolvedNotAll) {
            solved++;
        }
        
        if (task.status == kTaskStatusStarted || task.status == kTaskStatusError) {
            started++;
        }
    }
    
    level.countSolvedTasks = [NSNumber numberWithInteger:solved];
    level.countStartedTasks = [NSNumber numberWithInteger:started];
    
    NSInteger totalCurrentScore = 0;
    
    for (Task *task in level.tasks) {
        totalCurrentScore += [task.currentScore integerValue];
    }
    
    level.currentScore = [NSNumber numberWithInteger:totalCurrentScore];
}

#pragma mark - Setters&Getters

- (NSString *)schemeImageName
{
    NSArray *IDPartsSeparatedByDot = [self.identifier componentsSeparatedByString:@"."];
    
    NSString *leftHandPart = [IDPartsSeparatedByDot[0] lowercaseString];
    NSString *rightHandPart = IDPartsSeparatedByDot[1];
    
    NSString *baseImageName = [NSString stringWithFormat:@"%@_%@_f", leftHandPart, rightHandPart];
    
    NSString *currentLanguage = [NSLocale preferredLanguages][0];
    
    NSString *localizedImageName = [NSString stringWithFormat:@"%@_%@_%@_f", leftHandPart, rightHandPart, [currentLanguage uppercaseString]];
    
    if ([UIImage imageNamed:localizedImageName]) {
        NSLog(@"localized image name: %@", localizedImageName);
        return localizedImageName;
    }
    
    return baseImageName;
}

@end
