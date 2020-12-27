//
//  EtalonCalculationTest.m
//  MathParser
//
//  Created by alexbutenko on 11/26/13.
//  Copyright (c) 2013 alexbutenko. All rights reserved.
//

#import "EtalonCalculationTest.h"

@implementation EtalonCalculationTest

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-A-4-1.3"];
}

- (void)testSuccessCase {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 - 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 - 3 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];

    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testParenthesisSuccessCase {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 - 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + (7 - 3) = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCase2 {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 - 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 + 7 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testFailureCase {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 + 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 - 3 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testFailureCase2 {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 + 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 + 7 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCaseWithStructureAndCalculationErrors {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCaseWithStructureAndCalculationErrors2 {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"3 + 7 - 7 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testFailureCaseWithStructureAndCalculationErrors {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 6 + 3 = 17"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"23 - 7 - 7 = 17"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCaseWithStructureError {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 3"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCaseWithStructureError2 {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 + 3 = 3"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"3 + 7 - 7 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testFailureCaseWithStructureError {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"27 - 7 + 3 = 23"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"17 + 7 - 3 = 21"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testSuccessCaseWithCalculationError {
	[Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 + 3 = 16"];
    [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 + 3 = 16"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

//Operations

- (void)testOperationsSuccessCase {
	Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action1 addSubActionWithString:@"14 - 3 = 11"];

	Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action2 addSubActionWithString:@"14 - 3 = 11"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCase2 {
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 = 4"];
    [action1 addSubActionWithString:@"4 + 7 = 11"];
    
	Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action2 addSubActionWithString:@"14 - 3 = 11"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsFailureCase {
	Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action1 addSubActionWithString:@"14 + 3 = 17"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action2 addSubActionWithString:@"14 - 3 = 11"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsFailureCase2 {
	Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action1 addSubActionWithString:@"14 + 3 = 17"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 = 4"];
    [action2 addSubActionWithString:@"4 + 7 = 11"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCaseWithStructureAndCalculationErrors {
    
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action1 addSubActionWithString:@"14 + 3 = 27"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 + 7 = 14"];
    [action2 addSubActionWithString:@"14 + 3 = 27"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCaseWithStructureAndCalculationErrors2 {
    
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"8 - 7 = 1"];
    [action1 addSubActionWithString:@"1 + 3 = 4"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"3 + 8 = 11"];
    [action2 addSubActionWithString:@"11 - 7 = 4"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsFailureCaseWithStructureAndCalculationErrors {
    
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 6 = 0"];
    [action1 addSubActionWithString:@"1 + 3 = 27"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"23 - 7 = 16"];
    [action2 addSubActionWithString:@"16 - 7 = 27"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCaseWithStructureError {
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 = 0"];
    [action1 addSubActionWithString:@"0 + 3 = 3"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 = 0"];
    [action2 addSubActionWithString:@"0 + 3 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCaseWithStructureError2 {
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 7 = 0"];
    [action1 addSubActionWithString:@"0 + 3 = 3"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"3 + 7 = 10"];
    [action2 addSubActionWithString:@"10 - 7 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsFailureCaseWithStructureError {
    
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"27 - 7 = 20"];
    [action1 addSubActionWithString:@"20 + 3 = 23"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"17 + 7 = 24"];
    [action2 addSubActionWithString:@"24 - 3 = 21"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue(![solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

- (void)testOperationsSuccessCaseWithCalculationError {
    Action *action1 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 = 4"];
    [action1 addSubActionWithString:@"4 + 7 = 23"];
    
    Action *action2 = [Action actionOfType:kActionTypeExpression task:self.task withString:@"7 - 3 = 4"];
    [action2 addSubActionWithString:@"4 + 7 = 23"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    
	Action *solution1 = [self.actions objectAtIndex:0];
    Action *solution2 = [self.actions objectAtIndex:1];
    
    STAssertTrue([solution1 isActionEqualToAction:solution2], @"Success case is failed with solution1 etalon: %@\
                 solution 2 etalon: %@", solution1.etalon, solution2.etalon);
}

@end
