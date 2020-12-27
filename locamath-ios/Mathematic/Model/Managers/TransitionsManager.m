//
//  TransitionsManager.m
//  Mathematic
//
//  Created by alexbutenko on 6/27/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TransitionsManager.h"
#import "Level.h"
#import "LevelsPath.h"
#import "Task.h"
#import "OlympiadLevel.h"

static NSUInteger const kNumberOfTestTaskAllowedToFail = 2;

@implementation TransitionsManager

+ (id)sharedInstance
{
    static dispatch_once_t pred;
    static TransitionsManager *sharedInstance = nil;
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

- (BOOL)canOpenLevel:(Level *)level error:(NSError **)error
{
    NSArray *levelsPaths = [DataUtils pathsFromCurrentChildForLevelNumber:level.path.levelNumber];
   
    LevelsPath *lastOpenedLevelsPath = [DataUtils lastOpenedLevelsPathForLevelNumber:level.path.levelNumber];
    NSInteger lastOpenedLevelsPathIndex = [levelsPaths indexOfObject:lastOpenedLevelsPath];
    
    if (NSNotFound == lastOpenedLevelsPathIndex) {
        lastOpenedLevelsPathIndex = -1;
    }
    
    NSInteger currentLevelsPathIndex = [levelsPaths indexOfObject:level.path];
    
//    NSLog(@"lastOpenedLevelsPath: %@", lastOpenedLevelsPath.name);
    
    BOOL isOpened = currentLevelsPathIndex <= lastOpenedLevelsPathIndex ||
                    //next after opened, but it is required level
                    (currentLevelsPathIndex == lastOpenedLevelsPathIndex + 1 && [DataUtils isRequiredLevel:level]);
    
//    NSLog(@"lastOpenedLevelsPathIndex: %d", lastOpenedLevelsPathIndex);
//    NSLog(@"currentLevelsPathIndex: %d", currentLevelsPathIndex);
//    NSLog(@"isRequiredLevel: %@", [DataUtils isRequiredLevel:level] ? @"YES":@"NO");
//    NSLog(@"!!!LEVEL VALIDATION: isOpened: %@", isOpened ? @"YES":@"NO");

    if (!isOpened) {
        
        LevelsPath *neededToOpenLevelsPath = levelsPaths[lastOpenedLevelsPathIndex + 1];
        
//        NSLog(@"neededToOpenLevelsPath: %@", neededToOpenLevelsPath.name);
        
        NSString *errorString = nil;
        
        if (![level.isSelected boolValue]) {
            errorString = neededToOpenLevelsPath.transitionErrors[0];
            level.isSelected = @YES;
        } else {
            errorString = [neededToOpenLevelsPath.transitionErrors lastObject];
        }
        
        *error = [NSError errorWithString:errorString];
    }
        
    return isOpened;
}

- (BOOL)canOpenTask:(Task *)task error:(NSError **)error
{
    Level *level = (Level *)task.level;
    
    if ([level.isTest boolValue] && task.status != kTaskStatusError) {
        
        NSArray *tasksWithErrors = [DataUtils tasksWithErrorFromTasks:[level.tasks allObjects]];
                
        NSSet *pathLevelsWithoutTest = [level.path.levels reject:^BOOL(Level *level) {
            return [level.isTest boolValue];
        }];
        
        BOOL isAllTasksSolvedForLevelsWithoutTest = [pathLevelsWithoutTest all:^BOOL(Level *level) {
            return [DataUtils isAllTasksSolvedFromTasks:[level.tasks allObjects]];
        }];
        
        NSLog(@"!TEST TASKS VALIDATION: countOftasksWithErrors: %i isAllTasksSolvedForLevelsWithoutTest: %@",
              [tasksWithErrors count], isAllTasksSolvedForLevelsWithoutTest ? @"YES":@"NO");
        
        if ([tasksWithErrors count] >= kNumberOfTestTaskAllowedToFail && !isAllTasksSolvedForLevelsWithoutTest) {
            *error = [NSError errorWithString:NSLocalizedString(@"Oops! You made more than two mistakes. You can't continue solving the test problems until you finish solving the previous problems of this type", @"Warning on attempt to solve test tasks with 2 tasks failed already")];
            
            return NO;
        }
    }
    
    return YES;
}

- (BOOL)canOpenOlympiadLevel:(OlympiadLevel *)level error:(NSError **)error
{
    //map olympiads to paths of level 1
    NSArray *paths = [DataUtils pathsFromCurrentChildForLevelNumber:@1];
    
    if ([paths count]) {
        LevelsPath *path = paths[level.levelNumber];
        Level *testLevel = [DataUtils testLevelFromPath:path];
        BOOL isTestLevelSolved = [testLevel.isAllTasksSolved boolValue];
        
//        NSLog(@"!!!OLYMPIAD VALIDATION isTestLevelSolved: %@", isTestLevelSolved ? @"YES":@"NO");
//        NSLog(@"OLYMPIAD level index: %i", level.index);
        
        //check that previous levels are solved
        
        NSArray *levelsWithLowerIndexes = [[DataUtils olympiadLevelsFromCurrentChild] select:^BOOL(OlympiadLevel *otherLevel) {
            return otherLevel.index < level.index;
        }];
        
        BOOL arePreviousLevelsSolved = [levelsWithLowerIndexes all:^BOOL(OlympiadLevel *otherLevel) {
            return [otherLevel.isAllTasksSolved boolValue];
        }];
        
//        NSLog(@"!!!OLYMPIAD VALIDATION arePreviousLevelsSolved: %@", arePreviousLevelsSolved ? @"YES":@"NO");
        
        BOOL isOpen = isTestLevelSolved && arePreviousLevelsSolved && [level.tasks count];
        
        if (!isOpen && error) {
            *error = [NSError errorWithString:path.olympiadLocalText];
        }
        
        return isOpen;
    }
    
    return NO;
}

@end
