//
//  ParserTests.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OneActionParserTests.h"

@implementation OneActionParserTests 

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-E-1-1.1"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

/*
 "1-E-1-1.1": {
 "Score": "50",
 "Animation": "1-E-1-1.1",
 "Hint": "",
 "Solutions": "Expressions",
 "Formula": "S = V*t",
 "Objective": "Два зайца, испугавшись друг друга, побежали в противоположных направлениях. Один заяц бежал со скоростью 3 км/ч, а другой 2 км/ч. На сколько они удаляются за  1 час?",
 "Expressions": [
 "3+2"
 ],
 "Answer": "5"
 }

*/

- (void)testSuccessCase {
	[Action actionOfType:kActionTypeSolution task:self.task withString:@"2 + 3 = 5"];
      
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"2 + 3 = 4"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase0 {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"1 + 4 = 5"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase1 {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"4 = 4"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorCase0 {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"4 = 5"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeCalculation | kActionErrorTypeStructure), @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorCase1 {
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"2 + 4 = 7"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeCalculation | kActionErrorTypeStructure), @"Both error case is failed with error type: %i", solution.error);
}

@end
