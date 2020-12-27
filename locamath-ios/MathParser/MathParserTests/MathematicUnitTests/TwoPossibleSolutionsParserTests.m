//
//  TwoPossibleSolutionsParserTests.m
//  Mathematic
//
//  Created by Alex on 25.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TwoPossibleSolutionsParserTests.h"

@implementation TwoPossibleSolutionsParserTests

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-A-4-1.1"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

/*
 "1-A-4-1.1": {
 "Score": "400",
 "Animation": "",
 "Hint": "км осталось пройти туристам",
 "Solutions": "Both",
 "Formula": "S = V*t",
 "Objective": "Туристы решили пройти 20 км. В 1 день они прошли 7 км, во второй день 5 км. Сколько км осталось пройти туристам.",
 "Expressions": [
 "20-7-5",
 "20-(7+5)"
 ],
 "Answer": "8"
 }
*/

- (void)testSuccessCaseWithFirstSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 7 = 13"];
    [action addSubActionWithString:@"13 - 5 = 8"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testSuccessCaseWithFirstSolution2 {
	[Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 7 - 5 = 8"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testSuccessCaseWithSecondSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"7 + 5 = 12"];
    [action addSubActionWithString:@"20 - 12 = 8"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testSuccessCaseWithSecondSolution2 {
	[Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - (7 + 5) = 8"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorWithFirstSolution {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - (7 - 5) = 18"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorWithFirstSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 7 = 13"];
    [action addSubActionWithString:@"13 - 5 = 9"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorWithFirstSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 7 = 13"];
    [action addSubActionWithString:@"12 - 5 = 8"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeStructure | kActionErrorTypeCalculation), @"Both error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorWithSecondSolution {
	[Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 12 = 8"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorWithSecondSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"7 + 5 = 12"];
    [action addSubActionWithString:@"20 - 12 = 9"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorWithSecondSolution {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"7 + 5 = 12"];
    [action addSubActionWithString:@"20 - 11 = 8"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeStructure | kActionErrorTypeCalculation), @"Both error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInTheMiddle {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"7 + 5 = 13"];
    [action addSubActionWithString:@"20 + 13 = 3"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeCalculation | kActionErrorTypeStructure), @"Both error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInTheMiddle2 {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"20 - 7 = 8"];
    [action addSubActionWithString:@"8 - 5 = 3"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Both error case is failed with error type: %i", solution.error);
}

@end
