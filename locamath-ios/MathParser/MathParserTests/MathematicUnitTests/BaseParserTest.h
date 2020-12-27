//
//  BaseParserTest.h
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <SenTestingKit/SenTestingKit.h>
#import "Task.h"
#import "ParserWrapper.h"
#import "Action.h"
#import	"Action+Creation.h"
#import "Level.h"

@interface BaseParserTest : SenTestCase

@property (strong, nonatomic) Task *task;
@property (readonly, nonatomic) NSArray *actions;

@end
