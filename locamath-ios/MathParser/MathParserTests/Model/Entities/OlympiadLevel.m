//
//  OlympiadLevel.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 27.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadLevel.h"
#import "OlympiadTask.h"
#import "ConcretOlympiadViewController.h"

@implementation OlympiadLevel

@dynamic identifier;
@dynamic image;
@dynamic name;
@dynamic tasks;
@dynamic child;
@dynamic isAllTasksSolved;
@dynamic lastChangeDate;

#pragma mark - Setters&Getters

- (NSUInteger)index
{
    NSArray *identifierComponents = [self.identifier componentsSeparatedByString:@"-"];
    NSString *lastNumber = [identifierComponents lastObject];
    NSString *previousBeforeLastNumber = identifierComponents[[identifierComponents count] - 2];
    
    NSString *indexString = [previousBeforeLastNumber stringByAppendingString:lastNumber];
    
    return [indexString integerValue];
}

- (NSUInteger)levelNumber
{
    return (self.index/10 - 1);
}

#pragma mark - Abstract

- (Class)controllerClass
{
    return [ConcretOlympiadViewController class];
}

- (NSString *)statisticDescription
{
    if ([self.isAllTasksSolved boolValue]) {
        
        NSUInteger unitFlags = NSDayCalendarUnit | NSMonthCalendarUnit | NSYearCalendarUnit;
        NSDateComponents *components = [[NSCalendar currentCalendar] components:unitFlags
                                                                       fromDate:self.lastChangeDate];
        
        NSInteger day = [components day];
        NSInteger month = [components month];
        
        NSString *description = [NSString stringWithFormat:@"%02i.%02i %@ (%@)", day, month, self.name, NSLocalizedString(@"Solved", @"Solved level/path")];
        
        return description;
    }
    
    return nil;
}

- (NSArray *)sortedArrayOfTasks
{
    NSArray *tasks = [self.tasks.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    return tasks;
}

@end
