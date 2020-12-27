//
//  ParenthesisParserTests.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ParenthesisParserTests.h"

@implementation ParenthesisParserTests

- (void)setUp
{
    [super setUp];

    self.task = [Task findFirstByAttribute:@"identifier" withValue:@"1-B-4-1.1"];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
}

/*
"1-B-4-1.1": {
    "Score": "300",
    "Animation": "",
    "Hint": "км преодолели туристы",
    "Solutions": "Expressions",
    "Formula": "S = V*t",
    "Objective": "Туристы прошли пешком 3 км, проплыли на лодке на 2 км больше, а проехали на велосипеде столько, сколько прошли пешком и проплыли на лодке вместе. Какое расстояние преодолели туристы?",
    "Expressions": [
                    "3+(3+2)+(3+(3+2))"
                    ],
    "Answer": "16"
}*/

- (void)testSuccessCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 2 = 5"];
    [action addSubActionWithString:@"5 + 3 = 8"];
    [action addSubActionWithString:@"8 + 8 = 16"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeNone, @"Success case is failed with error type: %i", solution.error);
}

- (void)testStructureErrorCase {
	[Action actionOfType:kActionTypeSolution task:self.task withString:@"8 + 8 = 16"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Structure error case is failed with error type: %i", solution.error);
}

- (void)testCalculationErrorCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 2 = 5"];
    [action addSubActionWithString:@"3 + 2 = 5"];
    [action addSubActionWithString:@"5 + 3 = 8"];
    [action addSubActionWithString:@"5 + 3 = 8"];
    [action addSubActionWithString:@"8 + 8 = 17"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];

    STAssertTrue(solution.error == kActionErrorTypeCalculation, @"Calculation error case is failed with error type: %i", solution.error);
}

- (void)testBothErrorCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"3 + 2 = 5"];
    [action addSubActionWithString:@"3 + 2 = 5"];
    [action addSubActionWithString:@"5 + 3 = 8"];
    [action addSubActionWithString:@"5 + 3 = 8"];
    [action addSubActionWithString:@"8 + 7 = 16"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == (kActionErrorTypeStructure | kActionErrorTypeCalculation), @"Both error case is failed with error type: %i", solution.error);
}


@end
