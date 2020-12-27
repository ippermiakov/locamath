//
//  MultAndDevisionParserTestCase.m
//  MathParser
//
//  Created by Alex on 2/6/14.
//  Copyright (c) 2014 alexbutenko. All rights reserved.
//

#import "MultAndDevisionParserTestCase.h"

@implementation MultAndDevisionParserTestCase

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-U-1-2.3"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

- (void)testSuccessCase {
    
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"2 * 2 = 4"];
    [action addSubActionWithString:@"2 * 2 = 4"];
    [action addSubActionWithString:@"4 / 2 = 2"];
     
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"2 * 2 = 4"];
    [action addSubActionWithString:@"2 * 2 = 5"];
    [action addSubActionWithString:@"5 / 2 = 2"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
    
    Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"5 * 2 = 10"];
    [action addSubActionWithString:@"5 * 2 = 10"];
    [action addSubActionWithString:@"10 / 2 = 5"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

@end
