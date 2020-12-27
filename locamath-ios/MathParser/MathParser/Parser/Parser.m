//
//  Parser.m
//  Mathematic
//
//  Created by Developer on 25.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "Parser.h"
#import "Merger.h"
#import "ExpressionParser.h"

@implementation Parser

- (void)parseWithActions:(NSArray *)actions withEtalons:(NSArray *)expressions
{
    // Get trees from etalons (mostly 1 etalon)
    NSMutableArray *etalons = [NSMutableArray new];
    BOOL __block isError = NO;
    BOOL __block isExprOfLetterType = NO;
    
    for (NSString *rawEtalon in expressions) {
        ExpressionParser *expParser = [ExpressionParser new];
        [etalons addObject:[expParser parse:rawEtalon]];
    }
    
    for (id<ParselableActionProtocol> action in actions) {
        action.error = kActionErrorTypeNone;
        
        NSMutableArray *nodes = [NSMutableArray new];
        
        for (id<ParselableActionProtocol> subAction in action.subActions) {
            @try {
                ExpressionParser *expParser = [ExpressionParser new];
                NodeEquality *expr = (NodeEquality*)[expParser parse:subAction.string];
                
                [expr enumerateUsingBlock:^(NodeNumber *node) {
                    if ([node isKindOfClass:[NodeLetter class]]) {
                        isExprOfLetterType = YES;
                    }
                }];
                
                action.etalon = @(expr.hash);

                if (expr.value == NO && isExprOfLetterType == NO) {
                    // Calculation error.
                    subAction.error = kActionErrorTypeCalculation;
                    action.error = kActionErrorTypeCalculation;
                    isError = YES;
                } else {
                    subAction.error = kActionErrorTypeNone;
                }
                
                [nodes addObject:expr];
            }
            @catch (NSException *exception) {
                NSLog(@"Handled exception on EXPRESSION PARSE: %@\nStack: %@", exception, exception.callStackSymbols);
            }
            @finally {}
        }
        
        NSArray *variants = nil;
        
        @try {
            variants = [[Merger new] merge:nodes];
        }
        @catch (NSException *exception) {
            NSLog(@"Handled exception on MERGE: %@\nStack: %@", exception, exception.callStackSymbols);
            variants = nil;
        }
        @finally {}
        
        BOOL success = NO;
        
        
        for (NodeEquality *fullExpr in variants) {
            
            action.etalon = @(fullExpr.hash);

            for (Node *etalon in etalons) {
                
                if ([fullExpr.firstOperand isEqual:etalon] || (isExprOfLetterType && [fullExpr isEqual:etalon])) {
                    action.etalon = @(etalon.hash);

                    success = YES;
                    goto cycleEnd;
                }
            }
        }
            
        cycleEnd:
        
        if (success == NO) {
            // Structure error
            action.error |= kActionErrorTypeStructure;
            isError = YES;
        }
        
        if (isError) {
            [self.delegate didFailedParsingAction:action];
        }
    }
}

@end
