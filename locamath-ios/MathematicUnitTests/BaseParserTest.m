//
//  BaseParserTest.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseParserTest.h"
#import "MTFileParser.h"
#import "ChildManager.h"

@implementation BaseParserTest

- (void)setUp
{
    [super setUp];
    
    [MagicalRecord setDefaultModelFromClass:[self class]];
    [MagicalRecord setupCoreDataStackWithInMemoryStore];
        
    Child *child = [Child createEntity];
    child.name = @"Test";
    child.isCurrent = @YES;
    
    [[ChildManager sharedInstance] setValue:child forKey:@"currentChild"];

    [[MTFileParser sharedInstance] parseLocalFilesToCoreDataForChild:child completion:nil];
}

- (void)tearDown
{
    // Tear-down code here.
    [super tearDown];
    
    [[ChildManager sharedInstance] logoutCurrentChild];
    
    [MagicalRecord cleanUp];
}

@end
