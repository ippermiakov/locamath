//
//  ExpressionSyntaxTest.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 25.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ExpressionSyntaxTest.h"
#import "ExpressionParser.h"

@interface ExpressionSyntaxTest ()

@property (strong, nonatomic) ExpressionParser *parser;

@end

@implementation ExpressionSyntaxTest

- (void)setUp
{
    self.parser = [ExpressionParser new];
    [super setUp];
}

- (void)tearDown
{
    [super tearDown];
}

- (void)test0 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"6 --"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }
    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }    
}

- (void)test1 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"6 - +"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }
    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test2 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"6 -+ 2 = 4"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }
    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test3 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"= 6 - 2 + 4"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test4 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"+"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test5 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"+ / ="];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test6 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"+ = /"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test7 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"= + /"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test8 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"+ / 6 - 2 = 4"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test9 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"6 - 2 = 4 +"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

- (void)test10 {
    BOOL isReallyFailed = NO;
    @try {
        [self.parser parse:@"+ 7 + = 7"];
    }
    @catch (NSException *exception) {
        if ([exception.name isEqualToString:kInvalidSyntaxException]) {
            isReallyFailed = YES;
        }    }
    @finally {
        STAssertTrue(isReallyFailed == YES, @"Fail case is failed, exception was not raised as it was expected");
    }
}

@end
