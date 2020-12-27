//
//  OlympiadTaskTest.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 02.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadTaskTest.h"
#import "OlympiadLevel.h"
#import "OlympiadTask.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"



@implementation OlympiadTaskTest

- (OlympiadTask *)getTaskByLevelIndex:(NSUInteger)lIdx andTaskIndex:(NSUInteger)tIdx
{    
    OlympiadLevel *level = [OlympiadLevel findAllSortedBy:@"identifier" ascending:YES][lIdx];
    
    NSArray *tasks = [level.tasks.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadTask  *task  = tasks[tIdx];
    
    return task;
}


- (void)testSuccessLevel0Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action1 = actions[1];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[1] setUserInput:@"+"];
    [hints0[3] setUserInput:@"-"];
    [hints0[5] setUserInput:@"-"];
    [hints0[7] setUserInput:@"+"];
    
    [hints1[1] setUserInput:@"+"];
    [hints1[3] setUserInput:@"-"];
    [hints1[5] setUserInput:@"-"];
    [hints1[7] setUserInput:@"-"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
    STAssertTrue([action1.isCorrect boolValue], @"Second action should be completed");
}

- (void)testSuccessLevel0Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"2"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel0Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action = actions[0];
    OlympiadHint   *hint0 = action.hints.anyObject;
    OlympiadHint   *hint1 = action.hints.anyObject;
    OlympiadHint   *hint2 = action.hints.anyObject;
    
    //test using en locale
    [hint0 setUserInput:NSLocalizedString(@"salmon", nil)];
    [hint1 setUserInput:NSLocalizedString(@"trout", nil)];
    [hint2 setUserInput:NSLocalizedString(@"tuna", nil)];
    
    [action updateIsCorrect];
    
    STAssertTrue([action.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel0Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:NSLocalizedString(@"the same length", nil)];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}


- (void)testSuccessLevel1Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    OlympiadAction *action1 = actions[0];
    NSArray *hints1 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"16"];
    [hints1[0] setUserInput:@"4"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
    STAssertTrue([action1.isCorrect boolValue], @"First action should be completed");
    
    [hints0[0] setUserInput:@"4"];
    [hints1[0] setUserInput:@"16"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
    STAssertTrue([action1.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel1Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"4"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel1Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"3"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel1Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"3"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}


- (void)testSuccessLevel2Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"6"];
    [hints1[0] setUserInput:@"9"];
    [hints2[0] setUserInput:@"14"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel2Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[1] setUserInput:@"9"];
    [hints0[2] setUserInput:@"8"];
    [hints0[3] setUserInput:@"3"];
    [hints0[4] setUserInput:@"9"];
    [hints0[5] setUserInput:@"8"];
    [hints0[6] setUserInput:@"3"];
    
    [action0 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
}

- (void)testSuccessLevel2Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"6"];
    [hints0[1] setUserInput:@"11"];
    
    [hints1[0] setUserInput:@"12"];
    [hints1[1] setUserInput:@"4"];
    
    [hints2[0] setUserInput:@"10"];
    [hints2[1] setUserInput:@"9"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    [action2 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
    STAssertTrue([action1.isCorrect boolValue], @"Second action should be completed");
    STAssertTrue([action2.isCorrect boolValue], @"Third action should be completed");
}

- (void)testSuccessLevel2Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    OlympiadAction *action3 = actions[3];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints3 = [action3.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    
    [hints0[0] setUserInput:@"1L"];
    [hints0[1] setUserInput:@"6L"];
    
    [hints1[0] setUserInput:@"2L"];
    [hints1[1] setUserInput:@"5L"];
    
    [hints2[0] setUserInput:@"3L"];
    [hints2[1] setUserInput:@"4L"];
    
    [hints3[0] setUserInput:@"7L"];
    
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    [action2 updateIsCorrect];
    [action3 updateIsCorrect];
    
    STAssertTrue([action0.isCorrect boolValue], @"First action should be completed");
    STAssertTrue([action1.isCorrect boolValue], @"Second action should be completed");
    STAssertTrue([action2.isCorrect boolValue], @"Third action should be completed");
    STAssertTrue([action3.isCorrect boolValue], @"Third action should be completed");
}

- (void)testFailLevel0Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action1 = actions[1];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"+"];
    [hints0[1] setUserInput:@"-"];
    [hints0[2] setUserInput:@"-"];
    [hints0[3] setUserInput:@"-"];
    
    [hints1[0] setUserInput:@"+"];
    [hints1[1] setUserInput:@"-"];
    [hints1[2] setUserInput:@"+"];
    [hints1[3] setUserInput:@"-"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
    STAssertFalse([action1.isCorrect boolValue], @"Second action should not be completed");
}

- (void)testFailLevel0Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"3"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel0Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action = actions[0];
    OlympiadHint   *hint0 = action.hints.anyObject;
    OlympiadHint   *hint1 = action.hints.anyObject;
    OlympiadHint   *hint2 = action.hints.anyObject;
    
    [hint0 setUserInput:NSLocalizedString(@"карася", nil)];
    [hint1 setUserInput:NSLocalizedString(@"плотвичку", nil)];
    [hint2 setUserInput:NSLocalizedString(@"окуня", nil)];
    
    [action updateIsCorrect];
    
    STAssertFalse([action.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel0Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:0 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:NSLocalizedString(@"1-ый короче", nil)];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}


- (void)testFailLevel1Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    OlympiadAction *action1 = actions[0];
    NSArray *hints1 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"15"];
    [hints1[0] setUserInput:@"5"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
    STAssertFalse([action1.isCorrect boolValue], @"First action should not be completed");
    
    [hints0[0] setUserInput:@"2"];
    [hints1[0] setUserInput:@"18"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
    STAssertFalse([action1.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel1Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"7"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel1Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"1"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel1Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:1 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"97"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}


- (void)testFailLevel2Task0
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:0];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"14"];
    [hints1[0] setUserInput:@"6"];
    [hints2[0] setUserInput:@"9"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel2Task1
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:1];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"1"];
    [hints0[1] setUserInput:@"9"];
    [hints0[2] setUserInput:@"8"];
    [hints0[3] setUserInput:@"3"];
    [hints0[4] setUserInput:@"9"];
    [hints0[5] setUserInput:@"8"];
    [hints0[6] setUserInput:@"3"];
    [hints0[7] setUserInput:@"9"];
    
    [action0 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
}

- (void)testFailLevel2Task2
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:2];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    [hints0[0] setUserInput:@"6"];
    [hints0[1] setUserInput:@"12"];
    
    [hints1[0] setUserInput:@"11"];
    [hints1[1] setUserInput:@"4"];
    
    [hints2[0] setUserInput:@"10"];
    [hints2[1] setUserInput:@"8"];
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    [action2 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
    STAssertFalse([action1.isCorrect boolValue], @"Second action should not be completed");
    STAssertFalse([action2.isCorrect boolValue], @"Third action should not be completed");
}

- (void)testFailLevel2Task3
{
    OlympiadTask *task = [self getTaskByLevelIndex:2 andTaskIndex:3];
    
    NSArray *actions = [task.actions.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] < [[obj2 identifier] integerValue];
    }];
    
    OlympiadAction *action0 = actions[0];
    OlympiadAction *action1 = actions[1];
    OlympiadAction *action2 = actions[2];
    OlympiadAction *action3 = actions[3];
    
    NSArray *hints0 = [action0.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints1 = [action1.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints2 = [action2.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    NSArray *hints3 = [action3.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [[obj1 identifier] integerValue] > [[obj2 identifier] integerValue];
    }];
    
    
    [hints0[0] setUserInput:@"1л"];
    [hints0[1] setUserInput:@"5л"];
    
    [hints1[0] setUserInput:@"2л"];
    [hints1[1] setUserInput:@"6л"];
    
    [hints2[0] setUserInput:@"3ж"];
    [hints2[1] setUserInput:@"4л"];
    
    [hints3[0] setUserInput:@"6л"];
    
    
    [action0 updateIsCorrect];
    [action1 updateIsCorrect];
    [action2 updateIsCorrect];
    [action3 updateIsCorrect];
    
    STAssertFalse([action0.isCorrect boolValue], @"First action should not be completed");
    STAssertFalse([action1.isCorrect boolValue], @"Second action should not be completed");
    STAssertFalse([action2.isCorrect boolValue], @"Third action should not be completed");
    STAssertFalse([action3.isCorrect boolValue], @"Third action should not be completed");
}

@end
