//
//  TowActionDivisionParserTestCase.m
//  MathParser
//
//  Created by Alex on 2/6/14.
//  Copyright (c) 2014 alexbutenko. All rights reserved.
//

#import "TowActionDivisionParserTestCase.h"

@implementation TowActionDivisionParserTestCase

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-U-1-2.5"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

- (void)testSuccessCase {
    
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"8 / 2 = 4"];
    [action addSubActionWithString:@"8 / 2 = 4"];
    [action addSubActionWithString:@"4 / 2 = 2"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"8 / 2 = 4"];
    [action addSubActionWithString:@"8 / 2 = 6"];
    [action addSubActionWithString:@"6 / 2 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"12 / 2 = 6"];
    [action addSubActionWithString:@"12 / 2 = 6"];
    [action addSubActionWithString:@"6 / 2 = 3"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

@end
