//
//  Level.m
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Level.h"
#import "Task.h"
#import "LevelsPath.h"    
#import "DataUtils.h"
#import "ConcretLevelViewController.h"

@implementation Level

@dynamic identifier;
@dynamic name;
@dynamic image;
@dynamic levelScore;
@dynamic countStartedTasks;
@dynamic currentScore;
@dynamic countSolvedTasks;
@dynamic tasks;
@dynamic pointX;
@dynamic pointY;
@dynamic isTest;
@dynamic isSelected;
@dynamic child;
@dynamic game;
@dynamic path;
@dynamic isAllTasksSolved;
@dynamic lastChangeDate;

- (void)setIsAllTasksSolved:(NSNumber *)isAllTasksSolved
{
    [self willChangeValueForKey:@"isAllTasksSolved"];
    [self setPrimitiveValue:isAllTasksSolved forKey:@"isAllTasksSolved"];
    [self didChangeValueForKey:@"isAllTasksSolved"];
    
    if (self.path) {
        self.path.isAllLevelsSolved = @([DataUtils isAllLevelsSolvedForPathId:self.path.identifier]);
    }
}

- (void)setLastChangeDate:(NSDate *)lastChangeDate
{
    [self willChangeValueForKey:@"lastChangeDate"];
    [self setPrimitiveValue:lastChangeDate forKey:@"lastChangeDate"];
    [self didChangeValueForKey:@"lastChangeDate"];
    
    self.path.lastChangeDate = [NSDate date];
    
    NSLog(@"levels path %@ %@ solved: %@", self.path.name, self.path.color, self.path.isAllLevelsSolved);
}

#pragma mark - Abstract

- (Class)controllerClass
{
    return [ConcretLevelViewController class];
}

- (NSString *)statisticDescription
{    
    if ([self.isAllTasksSolved boolValue]) {
        
        NSUInteger unitFlags = NSDayCalendarUnit | NSMonthCalendarUnit | NSYearCalendarUnit;
        NSDateComponents *components = [[NSCalendar currentCalendar] components:unitFlags
                                                                       fromDate:self.lastChangeDate];
        
        NSInteger day = [components day];
        NSInteger month = [components month];
        
        NSString *description = nil;
        
        if (![self.isTest boolValue]) {
            //just solved, add additional statuses if needed
            description = [NSString stringWithFormat:NSLocalizedString(@"%02i.%02i %@ (Solved)", nil), day, month, self.name];
        } else {
            description = [NSString stringWithFormat:NSLocalizedString(@"%02i.%02i %@ (Got star)", nil), day, month, self.name];
        }
        
        return description;
    }
    
    return nil;
}

#pragma mark - Helper

- (NSArray *)sortedArrayOfTasks
{        
    return [[DataUtils sortedArrayOfTasks:[self.tasks allObjects]] copy];
}

@end