//
//  TwoActionParserTests.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "TwoActionParserTests.h"

@implementation TwoActionParserTests

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-B-1-2.4"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

/*
 "1-B-1-2.4": {
 "Score": "100",
 "Animation": "",
 "Hint": "",
 "Solutions": "Expressions",
 "Formula": "S = V*t",
 "Objective": "Мишка шел по лесу на север 3 минуты, а потом повернул направо шёл на восток на 15 минут больше. Сколько всего времени Мишка ходил по лесу?",
 "Expressions": [
 "3+(3+15)"
 ],
 "Answer": "21"
 },
*/
- (void)testSuccessCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 15 = 18"];
    [action addSubActionWithString:@"18 + 3 = 21"];
	
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"4 + 14 = 18"];
    [action addSubActionWithString:@"18 + 3 = 21"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 15 = 18"];
    [action addSubActionWithString:@"18 + 3 = 22"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorCase {
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 5 = 18"];
    [action addSubActionWithString:@"18 + 3 = 21"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeStructure | kActionErrorTypeCalculation), @"Both error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions {
    
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 15 = 17"];
    [action addSubActionWithString:@"17 + 3 = 20"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorInMiddleActionOfSolvingByActions2 {
    
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 15 = 17"];
    [action addSubActionWithString:@"3 + 17 = 20"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error in middle action of solving by actions case is failed with error type: %i", solution.error);
}


@end
