//
//  MultAndDev3ClassParserTestCase2.m
//  MathParser
//
//  Created by Alex on 2/14/14.
//  Copyright (c) 2014 alexbutenko. All rights reserved.
//

#import "MultAndDev3ClassParserTestCase2.h"

@implementation MultAndDev3ClassParserTestCase2

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-U-1-2.7"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

- (void)testSuccessCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"80 * 6 = 480"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"80 * 6 = 4"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"80 / 2 = 40"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

@end
