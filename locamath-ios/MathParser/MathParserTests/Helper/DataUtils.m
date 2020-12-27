//
//  DataUtils.m
//  Mathematic
//
//  Created by Developer on 25.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DataUtils.h"
#import "ChildManager.h"
#import "Child.h"
#import "Level.h"
#import "Task.h"
#import "Game.h"
#import "CoreData+MagicalRecord.h"
#import "Action.h"
#import "OlympiadTask.h"
#import "OlympiadLevel.h"
#import "LevelsPath.h"
#import "NSArray+ExtractInnerSet.h"
#import "TransitionsManager.h"
#import "Parent.h"
#import "TaskError.h"

@implementation DataUtils

#pragma mark - Achievements

+ (NSArray *)achievementsFromCurrentChild
{
    NSArray *achievements = [[DataUtils allTasksFromCurrentChild] arrayByAddingObjectsFromArray:[DataUtils allSolvedLevelsFromCurrentChild]];
    
    achievements = [achievements arrayByAddingObjectsFromArray:[DataUtils solvedPathsFromCurrentChild]];
    
    achievements = [achievements sortedArrayUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1, id<AbstractAchievement> obj2) {
        return [obj2.lastChangeDate compare:obj1.lastChangeDate];
    }];
    
    return achievements;
}

#pragma mark - Paths

+ (NSArray *)pathsFromCurrentChildForLevelNumber:(NSNumber *)levelNumber
{
    NSArray *paths = [[DataUtils pathsFromCurrentChild] select:^BOOL(LevelsPath *path) {
        return [path.levelNumber isEqualToNumber:levelNumber];
    }];
    
    return paths;
}

+ (NSArray *)pathsFromCurrentChild
{
    if (![[ChildManager sharedInstance] currentChild]) {
        return nil;
    }
    
    DKPredicateBuilder *builder = [DKPredicateBuilder new];
    
    [builder where:@"child" equals:[[ChildManager sharedInstance] currentChild]];
    
    return [LevelsPath findAllSortedBy:@"identifier"
                             ascending:YES
                         withPredicate:[builder compoundPredicate]];
}

+ (NSArray *)solvedPathsFromCurrentChild
{
    DKPredicateBuilder *builder = [DKPredicateBuilder new];
    
    [builder where:@"lastChangeDate" between:[NSDate distantPast] andThis:[NSDate date]];
    [builder where:@"child.name" equals:[ChildManager sharedInstance].currentChild.name ?: @""];
    [builder where:@"isAllLevelsSolved" equals:@YES];
    
    return [LevelsPath findAllSortedBy:@"lastChangeDate"
                             ascending:NO
                         withPredicate:[builder compoundPredicate]];
}

+ (LevelsPath *)pathWithColorName:(NSString *)colorName levelNumber:(NSNumber *)levelNumber
{
    LevelsPath *path = [DataUtils.pathsFromCurrentChild match:^BOOL(LevelsPath *path) {
        return [path.color isEqualToString:colorName] && [path.levelNumber isEqualToNumber:levelNumber];
    }];
    
    return path;
}

+ (LevelsPath *)lastOpenedLevelsPathForLevelNumber:(NSNumber *)levelNumber
{
    NSArray *levelsPaths = [DataUtils pathsFromCurrentChildForLevelNumber:levelNumber];
    
    NSArray *openedPaths = [levelsPaths select:^BOOL(LevelsPath *path) {
        return [path isOpened];
    }];
    
    openedPaths = [openedPaths sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] compare:[obj2 identifier]];
    }];
    
    return [openedPaths lastObject];
}

+ (NSArray *)notOpenedLevelsPathsForLevelNumber:(NSNumber *)levelNumber
{
    NSArray *levelsPaths = [DataUtils pathsFromCurrentChildForLevelNumber:levelNumber];
    
    //reject if is opened and animated already
    NSArray *notOpenedPaths = [levelsPaths reject:^BOOL(LevelsPath *path) {
        return [path isOpened] && [path.isGrowingAnimated boolValue];
    }];
    
    notOpenedPaths = [notOpenedPaths sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] compare:[obj2 identifier]];
    }];
        
    return notOpenedPaths;
}

+ (NSArray *)openedLevelsPathsForLevelNumber:(NSNumber *)levelNumber
{
    NSArray *levelsPaths = [DataUtils pathsFromCurrentChildForLevelNumber:levelNumber];
    
    //select if is opened and animated already
    NSArray *openedPaths = [levelsPaths select:^BOOL(LevelsPath *path) {
        return [path isOpened] && [path.isGrowingAnimated boolValue];
    }];
    
    openedPaths = [openedPaths sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] compare:[obj2 identifier]];
    }];
    
    return openedPaths;
}

+ (LevelsPath *)firstNotOpenedLevelsPathForLevelNumber:(NSNumber *)levelNumber
{    
    NSArray *notOpenedPaths = [DataUtils notOpenedLevelsPathsForLevelNumber:levelNumber];
    
    LevelsPath *firstNotOpenedPath = [notOpenedPaths count] ? notOpenedPaths[0] : nil;
    
    return firstNotOpenedPath;
}

+ (LevelsPath *)pathFollowingPath:(LevelsPath *)path
{
    LevelsPath *followingPath = nil;
    
    if (path) {
        NSArray *levelsPaths = [DataUtils pathsFromCurrentChildForLevelNumber:path.levelNumber];
        
        levelsPaths = [levelsPaths sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
            return [[obj1 identifier] compare:[obj2 identifier]];
        }];
        
        NSUInteger currentPathIdx = [levelsPaths indexOfObject:path];
        
        if (currentPathIdx < [levelsPaths count] - 1) {
            followingPath = levelsPaths[currentPathIdx + 1];
        }
    }

    return followingPath;
}

#pragma mark - Levels

+ (Level *)testLevelFromPath:(LevelsPath *)path
{
    Level *testLevel = [path.levels match:^BOOL(Level *level) {
            return [level.isTest boolValue];
    }];
    
    return testLevel;
}

+ (NSArray *)levelsFromCurrentChild
{
    if (![[ChildManager sharedInstance] currentChild]) {
        return nil;
    }
    
    DKPredicateBuilder *builder = [DKPredicateBuilder new];
    
    [builder where:@"child" equals:[[ChildManager sharedInstance] currentChild]];
    
    return [Level findAllSortedBy:@"identifier"
                        ascending:YES
                    withPredicate:[builder compoundPredicate]];
}

+ (NSArray *)olympiadLevelsFromCurrentChild
{
    NSArray *olympiadLevels = [[ChildManager sharedInstance].currentChild.olympiadLevels allObjects];
    
    //level id format: i.e. "9-0-1-3"
    olympiadLevels = [olympiadLevels sortedArrayUsingComparator:^NSComparisonResult(OlympiadLevel *obj1,
                                                                                    OlympiadLevel *obj2) {
        NSArray *num1 = [obj1.identifier componentsSeparatedByString:@"-"];
        //i.e. 1*10 + 3 = 13
        NSInteger index1 = [num1[2] integerValue] * 10 + [num1[3] integerValue];
        NSArray *num2 = [obj2.identifier componentsSeparatedByString:@"-"];
        NSInteger index2 = [num2[2] integerValue] * 10 + [num2[3] integerValue];
        return index1 > index2;
    }];
    
    return olympiadLevels;
}

+ (NSArray *)olympiadLevelsWithTasksFromCurrentChild
{
    return [[DataUtils olympiadLevelsFromCurrentChild] select:^BOOL(Level *level) {
        return [level.tasks count];
    }];
}

+ (NSArray *)allLevelsFromCurrentChild
{
    NSArray *levels = [DataUtils levelsFromCurrentChild];
    NSArray *olympiadLevels = [DataUtils olympiadLevelsFromCurrentChild];
    NSArray *allLevels = [levels arrayByAddingObjectsFromArray:olympiadLevels];
    
    return allLevels;
}

+ (NSArray *)unsolvedLevelsFromCurrentChild
{
    return [Level findAllSortedBy:@"lastChangeDate"
                        ascending:NO
                    withPredicate:[DataUtils solvedLevelsByDatePredicate:NO]];
}

+ (NSArray *)solvedLevelsFromCurrentChild
{
    return [Level findAllSortedBy:@"lastChangeDate"
                        ascending:NO
                    withPredicate:[DataUtils solvedLevelsByDatePredicate:YES]];
}

+ (NSArray *)solvedOlympiadLevelsFromCurrentChild
{
    NSArray *olympiadLevels = [OlympiadLevel findAllSortedBy:@"lastChangeDate"
                                                   ascending:NO
                                               withPredicate:[DataUtils solvedLevelsByDatePredicate:YES]];
    
    return olympiadLevels;
}

+ (NSArray *)allSolvedLevelsFromCurrentChild
{
    NSArray *solvedLevels = [DataUtils solvedLevelsFromCurrentChild];
    NSArray *olympiadSolvedLevels = [DataUtils solvedOlympiadLevelsFromCurrentChild];
    NSArray *allLevels = [solvedLevels arrayByAddingObjectsFromArray:olympiadSolvedLevels];
    
    return allLevels;
}

+ (NSArray *)solvedTestLevelsFromCurrentChild
{
    NSArray *solvedLevels = [DataUtils solvedLevelsFromCurrentChild];
    
    solvedLevels = [solvedLevels select:^BOOL(Level *level) {
        return [level.isTest boolValue];
    }];
    
    return solvedLevels;
}

+ (NSArray *)unsolvedTestLevelsFromCurrentChild
{
    //get either unsolved or solved, but not animated yet
    NSArray *unsolvedLevels = [[[ChildManager sharedInstance].currentChild.levels reject:^BOOL(Level *level) {
        //update if not updated yet
        level.isAllTasksSolved = @([DataUtils isAllTasksSolvedForLevelId:level.identifier]);
        return [level.isAllTasksSolved boolValue] && [level.path.isStarAnimated boolValue];
    }] allObjects];
    
    unsolvedLevels = [unsolvedLevels select:^BOOL(Level *level) {
        return [level.isTest boolValue];
    }];
    
    return unsolvedLevels;
}

+ (BOOL)isAllLevelsSolvedForPathId:(NSNumber *)path_id
{
    NSArray *levelPaths = DataUtils.pathsFromCurrentChild;
    
    LevelsPath *path = [levelPaths match:^BOOL(LevelsPath *path) {
        return [path.identifier isEqualToNumber:path_id];
    }];
    
    BOOL isAllLevelsSolved = [path.levels all:^BOOL(Level *level) {
        return [DataUtils isAllTasksSolvedForLevelId:level.identifier];
    }];
    
    return isAllLevelsSolved;
}

+ (BOOL)isRequiredLevel:(Level *)level
{
    return [level.identifier isEqualToString:level.path.requiredLevel.identifier];
}

+ (NSArray *)sortedArrayOfLevels:(NSArray *)levels
{
    NSArray *sortedLevels = [levels sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        NSString *firstValue   = [((Level *)obj1).identifier componentsSeparatedByString:@"-"][1];
        NSString *secondValue  = [((Level *)obj2).identifier componentsSeparatedByString:@"-"][1];
        return [firstValue compare:secondValue];
    }];
    
    return sortedLevels;
}

+ (BOOL)isAnyOlympiadLevelOpen
{
    BOOL isOpen = [[DataUtils openOlympiadLevels] count] > 0;
    
    NSLog(@"isAnyOlympiadLevelOpen: %@", isOpen ? @"YES":@"NO");
    
    return isOpen;
}

+ (NSArray *)openOlympiadLevels
{
    NSArray *olympiads = [DataUtils olympiadLevelsFromCurrentChild];
    
    olympiads = [olympiads select:^(OlympiadLevel *level) {
        return [[TransitionsManager sharedInstance] canOpenOlympiadLevel:level error:nil];
    }];
    
    return olympiads;
}

+ (NSArray *)unsolvedLevelsFromLevels:(NSArray *)levels
{
    NSArray *unsolvedLevels = [levels reject:^BOOL(id<AbstractLevel> abstractLevel) {
        return [abstractLevel.isAllTasksSolved boolValue];
    }];
    
    return unsolvedLevels;
}

+ (BOOL)isAnyOlympiadLevelUnsolved
{
    NSArray *openOlympiadLevels = [DataUtils openOlympiadLevels];
    NSArray *unsolvedLevels = [DataUtils unsolvedLevelsFromLevels:openOlympiadLevels];
    
    return [unsolvedLevels count] > 0;
}

#pragma mark - Tasks

+ (NSArray *)tasksWithPredicate:(NSPredicate *)predicate
{
    NSArray *tasks = [Task findAllSortedBy:@"identifier" ascending:YES withPredicate:predicate];
    
    return tasks;
}

+ (BOOL)isAllTasksSolvedForLevelId:(NSString *)level_id
{
    NSArray *levels = [DataUtils allLevelsFromCurrentChild];
    
    id<AbstractLevel> level = [levels match:^BOOL(id<AbstractLevel> level) {
        return [level.identifier isEqualToString:level_id];
    }];
        
    return [DataUtils isAllTasksSolvedFromTasks:[level.tasks allObjects]];
}

+ (BOOL)isAllTasksSolvedFromTasks:(NSArray *)tasks
{
    BOOL isAllTasksSolved = [tasks all:^BOOL(id<AbstractTask> task) {
        return task.status == kTaskStatusSolved || task.status == kTaskStatusSolvedNotAll;
    }];
    
//    if (!isAllTasksSolved) {
//        NSLog(@"%@ tasks statuses: %@", [[((Task *)[tasks lastObject]) level] identifier], [tasks valueForKey:@"statusNumber"]);
//    }
    
    return isAllTasksSolved;
}

+ (BOOL)isAllTrainingTasksSolvedForLevel:(Level *)level
{
    NSArray *trainingTasks = [DataUtils tasksOfType:kTaskTypeTraining forLevel:level];
    
    BOOL isAllTasksSolved = [DataUtils isAllTasksSolvedFromTasks:trainingTasks];
    
    return isAllTasksSolved;
}

+ (NSArray *)unsolvedTasksFromTasks:(NSArray *)tasks
{
    NSArray *unsolvedTasks = [tasks reject:^BOOL(id<AbstractTask> task) {
        return task.status == kTaskStatusSolved || task.status == kTaskStatusSolvedNotAll;
    }];
    
    return unsolvedTasks;
}

//TODO: move to Task
+ (NSNumber *)scoreForTask:(Task *)task withActions:(NSArray *)actions
{
    NSInteger score = [task.score intValue];
    
    NSInteger expressionsScore = 0;
    
    NSInteger solutionsScore = 0;
    
    NSInteger expressionsCount = [task.expressions count];
    
    if ([task.solutions isEqualToString:kBothSolutionsType]) {
        for (Action *action in actions) {
            if (action.error == kActionErrorTypeNone && [action.answer length]) {
                if (action.type == kActionTypeExpression) {
                    expressionsScore += score * 0.6 / expressionsCount;
                }
                if (action.type == kActionTypeSolution) {
                    solutionsScore += score * 0.4 / expressionsCount;
                }
            }
        }
    } else if ([task.solutions isEqualToString:@"Expressions"] && expressionsCount > 1) {
        for (Action *action in actions) {
            if (action.error == kActionErrorTypeNone && [action.answer length]) {
                if (action.type == kActionTypeExpression) {
                    expressionsScore += score / expressionsCount;
                }
            }
        }
    }
    
    if (expressionsCount == 1 && expressionsScore == 0 &&
        task.status == kTaskStatusSolved && solutionsScore == 0) {
        expressionsScore = [task.score intValue];
    }
    
    NSInteger sum = expressionsScore + solutionsScore;
    
    return [NSNumber numberWithInteger:sum];
}

+ (NSArray *)tasksWithActionErrorType:(ActionErrorType)errorType
{
    return [DataUtils tasksWithActionErrorType:errorType fromTasks:DataUtils.tasksFromCurrentChild];
}

+ (NSArray *)tasksWithActionErrorType:(ActionErrorType)errorType fromTasks:(NSArray *)tasks
{
    NSMutableArray *array = [[NSMutableArray alloc] init];
    
    for (Task *task in tasks) {
        [task.taskErrors each:^(TaskError *taskError) {
            if ([taskError.errorType integerValue] == errorType) {
                [array addObject:taskError];
            }
        }];
    }
    
    return array;
}

+ (NSInteger)solvedTaskErrorCountWithErrorType:(ActionErrorType)errorType
{
    
    NSInteger count = 0;
    
    for (Task *task in DataUtils.tasksFromCurrentChild) {
        NSArray *actionWithError = nil;
        
        if ((task.status == kTaskStatusSolvedNotAll || task.status == kTaskStatusSolved) && [task.taskErrors count] > 0) {
            actionWithError = [[task.taskErrors allObjects] select:^BOOL(TaskError *taskError) {
                return [taskError.errorType integerValue] == errorType ||
                [taskError.errorType integerValue] ==  (kActionErrorTypeCalculation | kActionErrorTypeStructure);
            }];
        }
                               
        count += [actionWithError count];
    }
                               
    return count;
}

+ (NSArray *)solvedTasksForTask:(NSArray *)tasks withErrorType:(ActionErrorType)errorType
{
    NSMutableArray *array = [[NSMutableArray alloc] init];
    
    for (Task *task in tasks) {
        if ((task.status == kTaskStatusSolvedNotAll || task.status == kTaskStatusSolved) && [task.taskErrors count] > 0) {
            NSArray *actionWithError = [[task.taskErrors allObjects] select:^BOOL(TaskError *taskError) {
                return [taskError.errorType integerValue] == errorType ||
                [taskError.errorType integerValue] ==  (kActionErrorTypeCalculation | kActionErrorTypeStructure);
            }];
            if (actionWithError.count > 0) [array addObject:task];
        }
    }
    return array;
}

+ (NSArray *)allErrorsForTasks:(NSArray *)tasks withErrorType:(ActionErrorType)errorType
{
    NSMutableArray *array = [[NSMutableArray alloc] init];
    
    for (Task *task in tasks) {
        if ([task.taskErrors count] > 0) {
            NSArray *actionWithError = [[task.taskErrors allObjects] select:^BOOL(TaskError *taskError) {
                return [taskError.errorType integerValue] == errorType || [taskError.errorType integerValue] == (kActionErrorTypeStructure | kActionErrorTypeCalculation);
            }];
            if (actionWithError.count > 0) {
                [array addObjectsFromArray:actionWithError];
            }
        }
    }
    return array;
}

+ (NSArray *)tasksWithErrorFromTasks:(NSArray *)tasks
{
    NSArray *actions = [[tasks valueForKey:@"actions"] arrayByExtractingInnerSets];
    
    actions = [actions reject:^BOOL(Action *action) {
        return action.error == kActionErrorTypeNone;
    }];
    
    return actions;
}

+ (NSArray *)tasksFromCurrentChild
{
    NSArray *tasks = [Task findAllSortedBy:@"lastChangeDate"
                                 ascending:NO
                             withPredicate:[DataUtils tasksByDatePredicate]];
    
    return tasks;
}

+ (NSArray *)olympiadTasksFromCurrentChild
{
    NSArray *olympiadTasks = [OlympiadTask findAllSortedBy:@"lastChangeDate"
                                                 ascending:NO
                                             withPredicate:[DataUtils tasksByDatePredicate]];
    
    return olympiadTasks;
}

+ (NSArray *)allTasksFromCurrentChild
{
    NSArray *tasks = [DataUtils tasksFromCurrentChild];
    NSArray *olympiadTasks = [DataUtils olympiadTasksFromCurrentChild];
    
    NSArray *allTasks = [tasks arrayByAddingObjectsFromArray:olympiadTasks];
        
    return allTasks;
}

+ (NSArray *)tasksOfType:(TaskType)type forLevel:(Level *)level
{
    NSSet *tasks = [level.tasks select:^BOOL(Task *task) {
        return [task.taskType integerValue] == type;
    }];
    
    return [tasks allObjects];
}

+ (Task *)firstUnsolvedTrainingTaskForLevel:(Level *)level
{
    Task *firstUnsolvedTask = nil;
    
    NSArray *trainingTasks = [DataUtils tasksOfType:kTaskTypeTraining forLevel:level];
    NSArray *unsolvedTrainingTasks = [DataUtils unsolvedTasksFromTasks:trainingTasks];

    if ([unsolvedTrainingTasks count]) {
        unsolvedTrainingTasks = [DataUtils sortedArrayOfTasks:unsolvedTrainingTasks];
        firstUnsolvedTask = unsolvedTrainingTasks[0];
    }
    
    return firstUnsolvedTask;
}

+ (NSArray *)sortedArrayOfTasks:(NSArray *)tasks
{    
    NSArray *sortedTasks = [tasks sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        double firstValue   = [[[[((Task *)obj1).identifier componentsSeparatedByString:@"-"] lastObject] stringByReplacingOccurrencesOfString:@"." withString:@""] doubleValue];
        double secondValue  = [[[[((Task *)obj2).identifier componentsSeparatedByString:@"-"] lastObject] stringByReplacingOccurrencesOfString:@"." withString:@""] doubleValue];
        return firstValue > secondValue;
    }];
    
    return sortedTasks;
}

#pragma mark - Actions

+ (NSNumber *)countOfSolvedActionsFromActions:(NSArray *)actions
{
    NSInteger count = 0;
    
    for (Action *action in actions) {
        if (action.error == kActionErrorTypeNone) {
            count++;
        }
    }
    
    return [NSNumber numberWithInteger:count];
}

+ (NSArray *)actionsOfType:(ActionType)actionType fromActions:(NSArray *)actions
{
    return [actions select:^BOOL(Action *action) {
        return action.type == actionType;
    }];
}

#pragma mark - Child

+ (NSArray *)childs
{
    return [Child findAllSortedBy:@"name" ascending:YES];
}

+ (Child *)childWithName:(NSString *)name
{
    Child *child = [[DataUtils childs] match:^BOOL(Child *obj) {
        return [obj.name isEqualToString:name];
    }];
    
    return child;
}

+ (BOOL)isCurrentChildDefault
{
   // NSLog(@" >> current Child :%@", [ChildManager sharedInstance].currentChild);
    BOOL isDefault = NO;
    
    if ([ChildManager sharedInstance].currentChild && [ChildManager sharedInstance].currentChild.parent == nil) {
        isDefault = YES;
    }
    
    return isDefault;
}

#pragma mark - Parent

+ (Parent *)currentParent
{
    Parent *currentPrent = [Parent findFirst];
    
    return currentPrent;
}

#pragma mark - Helper

+ (NSPredicate *)tasksByDatePredicate
{
    DKPredicateBuilder *builder = [DKPredicateBuilder new];
    
    [builder where:@"child" equals:[ChildManager sharedInstance].currentChild ?: [NSNull null]];
    [builder where:@"statusNumber" doesntEqual:@(kTaskStatusUndefined)];
    
    return [builder compoundPredicate];
}

+ (NSPredicate *)solvedLevelsByDatePredicate:(BOOL)isSolved
{
    DKPredicateBuilder *builder = [DKPredicateBuilder new];
    
    [builder where:@"child" equals:[ChildManager sharedInstance].currentChild ?: [NSNull null]];
    [builder where:@"isAllTasksSolved" equals:@(isSolved)];
    
    return [builder compoundPredicate];
}

@end
