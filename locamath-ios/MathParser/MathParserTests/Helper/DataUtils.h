//
//  DataUtils.h
//  Mathematic
//
//  Created by Developer on 25.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Action.h"

@class Task, LevelsPath, Level, Parent;

@interface DataUtils : NSObject

#pragma mark - Achievements

+ (NSArray *)achievementsFromCurrentChild;

#pragma mark - Paths

+ (NSArray *)pathsFromCurrentChildForLevelNumber:(NSNumber *)levelNumber;
+ (NSArray *)pathsFromCurrentChild;
+ (LevelsPath *)pathWithColorName:(NSString *)colorName levelNumber:(NSNumber *)levelNumber;
+ (LevelsPath *)lastOpenedLevelsPathForLevelNumber:(NSNumber *)levelNumber;
+ (NSArray *)notOpenedLevelsPathsForLevelNumber:(NSNumber *)levelNumber;
+ (NSArray *)openedLevelsPathsForLevelNumber:(NSNumber *)levelNumber;

+ (LevelsPath *)firstNotOpenedLevelsPathForLevelNumber:(NSNumber *)levelNumber;
+ (LevelsPath *)pathFollowingPath:(LevelsPath *)path;

#pragma mark - Levels

+ (Level *)testLevelFromPath:(LevelsPath *)path;
+ (NSArray *)levelsFromCurrentChild;
+ (NSArray *)olympiadLevelsFromCurrentChild;
+ (NSArray *)olympiadLevelsWithTasksFromCurrentChild;
+ (NSArray *)allLevelsFromCurrentChild;
+ (NSArray *)unsolvedLevelsFromCurrentChild;
+ (NSArray *)solvedLevelsFromCurrentChild;
+ (NSArray *)solvedOlympiadLevelsFromCurrentChild;
+ (NSArray *)allSolvedLevelsFromCurrentChild;
+ (NSArray *)solvedTestLevelsFromCurrentChild;
+ (NSArray *)unsolvedTestLevelsFromCurrentChild;
+ (BOOL)isAllLevelsSolvedForPathId:(NSNumber *)path_id;
+ (BOOL)isRequiredLevel:(Level *)level;
+ (NSArray *)sortedArrayOfLevels:(NSArray *)levels;
+ (BOOL)isAnyOlympiadLevelOpen;
+ (NSArray *)openOlympiadLevels;
+ (NSArray *)unsolvedLevelsFromLevels:(NSArray *)levels;
+ (BOOL)isAnyOlympiadLevelUnsolved;

#pragma mark - Tasks

+ (NSArray *)tasksWithPredicate:(NSPredicate *)predicate;
+ (BOOL)isAllTasksSolvedForLevelId:(NSString *)level_id;
+ (BOOL)isAllTasksSolvedFromTasks:(NSArray *)tasks;
+ (BOOL)isAllTrainingTasksSolvedForLevel:(Level *)level;
+ (NSNumber *)scoreForTask:(Task *)task withActions:(NSArray *)actions;
+ (NSArray *)tasksWithActionErrorType:(ActionErrorType)errorType;
+ (NSArray *)tasksWithActionErrorType:(ActionErrorType)errorType fromTasks:(NSArray *)tasks;
+ (NSArray *)tasksWithErrorFromTasks:(NSArray *)tasks;

+ (NSArray *)tasksFromCurrentChild;
+ (NSArray *)olympiadTasksFromCurrentChild;
+ (NSArray *)allTasksFromCurrentChild;
+ (NSArray *)tasksOfType:(TaskType)type forLevel:(Level *)level;
+ (Task *)firstUnsolvedTrainingTaskForLevel:(Level *)level;
+ (NSArray *)sortedArrayOfTasks:(NSArray *)tasks;
+ (NSArray *)unsolvedTasksFromTasks:(NSArray *)tasks;

#pragma mark - Actions

+ (NSNumber *)countOfSolvedActionsFromActions:(NSArray *)actions;
+ (NSArray *)actionsOfType:(ActionType)actionType fromActions:(NSArray *)actions;

#pragma mark - Child

+ (NSArray *)childs;
+ (Child *)childWithName:(NSString *)name;
+ (BOOL)isCurrentChildDefault;

#pragma mark - Parent

+ (Parent *)currentParent;

#pragma mark - errors

+ (NSArray *)solvedTasksForTask:(NSArray *)tasks withErrorType:(ActionErrorType)errorType;
+ (NSArray *)allErrorsForTasks:(NSArray *)tasks withErrorType:(ActionErrorType)errorType;
+ (NSInteger)solvedTaskErrorCountWithErrorType:(ActionErrorType)errorType;

@end
