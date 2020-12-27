//
//  ThreeActionParserTests.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ThreeActionParserTests.h"

@implementation ThreeActionParserTests

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-B-4-2.3"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

/*
"1-B-4-2.3": {
    "Score": "400",
    "Animation": "",
    "Hint": "",
    "Solutions": "Expressions",
    "Formula": "S = V*t",
    "Objective": "Автомобиль проехал по городу 6 км, по шоссе на 2 км меньше, а по проселочной дороге столько, сколько проехал по городу и по шоссе вместе. Сколько всего проехал автомобиль?",
    "Expressions": [
                    "6+(6-2)+6+(6-2)"
                    ],
    "Answer": "20"
}*/
- (void)testSuccessCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 10 = 20"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 10 = 20"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 10 = 21"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 9 = 20"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeStructure | kActionErrorTypeCalculation), @"Both error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"6 + 4 = 9"];
    [action addSubActionWithString:@"9 + 10 = 19"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions2 {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 3"];
    [action addSubActionWithString:@"6 + 3 = 9"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 10 = 20"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions3 {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 3"];
    [action addSubActionWithString:@"6 + 3 = 9"];
    [action addSubActionWithString:@"6 + 4 = 10"];
    [action addSubActionWithString:@"10 + 9 = 19"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions4 {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"6 - 2 = 4"];
    [action addSubActionWithString:@"6 - 2 = 3"];
    [action addSubActionWithString:@"6 + 3 = 9"];
    [action addSubActionWithString:@"6 + 4 = 9"];
    [action addSubActionWithString:@"9 + 9 = 18"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}

@end
