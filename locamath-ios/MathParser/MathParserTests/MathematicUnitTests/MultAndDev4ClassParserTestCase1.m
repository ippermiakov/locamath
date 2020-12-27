//
//  MultAndDev4ClassParserTestCase1.m
//  MathParser
//
//  Created by Alex on 2/14/14.
//  Copyright (c) 2014 alexbutenko. All rights reserved.
//

#import "MultAndDev4ClassParserTestCase1.h"

@implementation MultAndDev4ClassParserTestCase1

- (void)setUp
{
    [super setUp];
    
    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-U-1-2.6"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

- (void)testSuccessCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"84 / 7 = 12"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"84 / 7 = 19"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
    
    [Action actionOfType:kActionTypeSolution task:self.task withString:@"84 * 7 = 588"];
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

@end
