//
//  LevelsPath.m
//  Mathematic
//
//  Created by alexbutenko on 6/26/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "LevelsPath.h"
#import "Level.h"
#import "DataUtils.h"

@implementation LevelsPath

@dynamic identifier;
@dynamic name;
@dynamic color;
@dynamic isAllLevelsSolved;
@dynamic levels;
@dynamic child;
@dynamic lastChangeDate;
@dynamic transitionErrors;
@dynamic levelNumber;
@dynamic olympiadLocalText;
@dynamic isGrowingAnimated;
@dynamic isStarAnimated;

#pragma mark - Setters&Getters

- (Level *)requiredLevel
{
    Level *level = [self.levels match:^BOOL(Level *level) {
        NSString *minorID = [[level.identifier componentsSeparatedByString:@"-"] lastObject];
        return [minorID isEqualToString:@"1"];
    }];
    
    return level;
}

#pragma mark - Abstract

- (NSString *)statisticDescription
{
    if (self.isAllLevelsSolved) {
        NSUInteger unitFlags = NSDayCalendarUnit | NSMonthCalendarUnit | NSYearCalendarUnit;
        NSDateComponents *components = [[NSCalendar currentCalendar] components:unitFlags
                                                                       fromDate:self.lastChangeDate];
        
        NSInteger day = [components day];
        NSInteger month = [components month];
        
        //just solved, add additional statuses if needed
        NSString *description =   description = [NSString stringWithFormat:@"%02i.%02i %@ (%@)", day, month, self.name, NSLocalizedString(@"Solved", @"Solved level/path")];

        return description;
    }
    
    return nil;
}

- (Class)controllerClass
{
    return nil;
}

#pragma mark - Helper methods

- (BOOL)isOpened
{
    //check that training tasks are solved for requiredLevel from level's Path
    NSArray *trainingTasks = [DataUtils tasksOfType:kTaskTypeTraining forLevel:self.requiredLevel];
    return [DataUtils isAllTasksSolvedFromTasks:trainingTasks];
}

@end
