//
//  ThreeActionparserTests2.m
//  Mathematic
//
//  Created by SanyaIOS on 22.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ThreeActionparserTests2.h"

@implementation ThreeActionparserTests2
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

- (void)testSuccessCase {
	Action *action = [Action actionOfType:kActionTypeSolution task:self.task withString:@"5 + 7 = 12"];
    [action addSubActionWithString:@"5 + 7 = 12"];
    [action addSubActionWithString:@"20 - 12 = 8"];
    [action addSubActionWithString:@"8 - 1 = 7"];
    
    
    [[ParserWrapper new] parseWithActions:self.actions withEtalons:self.task.expressions];
	Action *solution = [self.actions objectAtIndex:0];
    STAssertTrue(solution.error == kActionErrorTypeStructure, @"Success case is failed with error type: %i", solution.error);
}

@end
