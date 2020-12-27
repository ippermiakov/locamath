//
//  ExpressionParser.m
//  Mathematic
//
//  Created by Developer on 03.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ExpressionParser.h"
#import "Pair.h"
#import "NSArray+EnumerationBetweenObjects.h"

NSString * const kInvalidSyntaxException = @"Invalid syntax";

@implementation ExpressionParser

- (NSString *)assignWhitespaces:(NSString *)str {
    
    str = [str stringByReplacingOccurrencesOfString:@"("
                                         withString:@"( "];
    str = [str stringByReplacingOccurrencesOfString:@")"
                                         withString:@" )"];
    str = [str stringByReplacingOccurrencesOfString:@"-"
                                         withString:@" - "];
    str = [str stringByReplacingOccurrencesOfString:@"+"
                                         withString:@" + "];
    str = [str stringByReplacingOccurrencesOfString:@"/"
                                         withString:@" / "];
    str = [str stringByReplacingOccurrencesOfString:@"*"
                                         withString:@" * "];
    str = [str stringByReplacingOccurrencesOfString:@"="
                                         withString:@" = "];
    
    str = [str stringByReplacingOccurrencesOfString:@"   "
                                         withString:@" "];
    str = [str stringByReplacingOccurrencesOfString:@"  "
                                         withString:@" "];
    
    return str;
}

- (Node*)parse:(NSString*)parsingString {
    
    @try {
        parsingString = [self assignWhitespaces:parsingString];
        NSMutableArray  *tokens = [parsingString componentsSeparatedByString:@" "].mutableCopy;
        [tokens removeObject:@""];
        
        NSMutableArray *nodedTokens = [NSMutableArray new];
        for (NSString *token in tokens) {
            if ([token isEqualToString:@"="]) {
                [nodedTokens addObject:[NodeEquality new]];
            } else if ([token isEqualToString:@"*"]) {
                [nodedTokens addObject:[NodeMultiply new]];
            } else if ([token isEqualToString:@"/"]) {
                [nodedTokens addObject:[NodeDivide new]];
            } else if ([token isEqualToString:@"+"]) {
                [nodedTokens addObject:[NodePlus new]];
            } else if ([token isEqualToString:@"-"]) {
                [nodedTokens addObject:[NodeMinus new]];
            } else if ([token isEqualToString:@"("]) {
                [nodedTokens addObject:[NodeOpenBracket_Aux new]];
            } else if ([token isEqualToString:@")"]) {
                [nodedTokens addObject:[NodeCloseBracket_Aux new]];
            } else if ([[NSScanner scannerWithString:token] scanDouble:nil] == YES) {
                [nodedTokens addObject:[NodeNumber nodeWithNumber:[token doubleValue]]];
            } else {  // It's probably a letter
                [nodedTokens addObject:[NodeLetter nodeWithLetter:token]];
            }
        }
        Pair *pair = [Pair new];
        
        ComparatorBlock startComparator = ^BOOL(id obj, NSInteger index) {
            BOOL isFirst = obj == [nodedTokens objectAtIndex:0];
            BOOL isOperator = [obj isKindOfClass:[NodeOperator class]];
            BOOL isThereSubNode = [obj isThereSubNode:(Node*)[nodedTokens objectAtIndex:0]];
            
            return isFirst || (isOperator && isThereSubNode);
            
            //return obj == [nodedTokens objectAtIndex:0] || ([obj isKindOfClass:[NodeOperator class]] && [obj isThereSubNode:(Node*)[nodedTokens objectAtIndex:0]]);
        };
        ComparatorBlock endComparator   = ^BOOL(id obj, NSInteger index) {
            BOOL isLast = obj == [nodedTokens lastObject];
            BOOL isOperator = [obj isKindOfClass:[NodeOperator class]];
            BOOL isThereSubNode = [obj isThereSubNode:(Node*)[nodedTokens lastObject]];
            
            return isLast || (isOperator && isThereSubNode);
        };
        pair.firstObject  = startComparator;
        pair.secondObject = endComparator;
        
        [self realParse:nodedTokens withRange:pair];
        [nodedTokens removeObject:[NodeOpenBracket_Aux new]];
        [nodedTokens removeObject:[NodeCloseBracket_Aux new]];
        
//#ifdef UNIT_TESTS
        NodeBinaryOperator *node = nodedTokens.lastObject;
        
        if ([node isCorrect] == NO || nodedTokens.count > 1 ) {
            @throw [NSException exceptionWithName:kInvalidSyntaxException reason:nil userInfo:nil];
        }
//#endif
        
        return nodedTokens.lastObject;
    }
    @catch (NSException *exception) {
//#ifdef UNIT_TESTS
        @throw [NSException exceptionWithName:kInvalidSyntaxException reason:nil userInfo:@{@"Base exception": exception}];
//#endif
    }
    @finally {}
}

- (void)realParse:(NSMutableArray*)nodes withRange:(Pair*)range {
    __block NSUInteger              openBracketCounter  = 0;
    __block BOOL                    bracketSession      = 0;
    __block NodeOpenBracket_Aux     *openBracket        = nil;
    __block NodeCloseBracket_Aux    *closeBracket       = nil;
    __block NSInteger               startIdx;
    __block NSInteger               endIdx;
    
    [nodes enumerateBetweetWithStartComparator:range.firstObject endComparator:range.secondObject usingBlock:^(id node, NSInteger *index, BOOL *stop0) {
        Pair *subexprCmps = [Pair new];
        
        if ([node isKindOfClass:[NodeOpenBracket_Aux class]]) {
            if (bracketSession == NO) {
                bracketSession = YES;
                openBracket  = node;
                startIdx = (*index);
            }
            openBracketCounter++;
        }
        if ([node isKindOfClass:[NodeCloseBracket_Aux class]]) {
            openBracketCounter--;
            closeBracket = node;
            endIdx = (*index);
        }
        if (openBracketCounter == 0 && bracketSession == YES) {
            bracketSession = NO;
            
            Node *startExprNode = [nodes objectAtIndex:startIdx];
            Node *endExprNode   = [nodes objectAtIndex:endIdx];
            
            
            if ([startExprNode isKindOfClass:[NodeOpenBracket_Aux class]]) {
                startExprNode   = [nodes objectAtIndex:startIdx + 1];
            }
            
            if ([endExprNode isKindOfClass:[NodeCloseBracket_Aux class]]) {
                endExprNode     = [nodes objectAtIndex:endIdx - 1];
            }
                        
            ComparatorBlock startSubexprComparator = ^BOOL(id obj, NSInteger index) {
                return obj == startExprNode || ([obj isKindOfClass:[NodeBinaryOperator class]] && [(NodeBinaryOperator*)obj isThereSubNode:startExprNode]);
            };
            ComparatorBlock endSubexprComparator   = ^BOOL(id obj, NSInteger index) {
                return obj == endExprNode   || ([obj isKindOfClass:[NodeBinaryOperator class]] && [(NodeBinaryOperator*)obj isThereSubNode:endExprNode]);
            };
            
            subexprCmps.firstObject  = startSubexprComparator;
            subexprCmps.secondObject = endSubexprComparator;
            
            [self realParse:nodes withRange:subexprCmps];
        }
    }];
    
    NSMutableArray *toRemove = [NSMutableArray new];

    [nodes enumerateBetweetWithStartComparator:range.firstObject endComparator:range.secondObject usingBlock:^(id obj, NSInteger *index, BOOL *stop) {
        ComparatorBlock startComparator = (ComparatorBlock)range.firstObject;
        ComparatorBlock endComparator   = (ComparatorBlock)range.secondObject;
        if ( ! startComparator(obj, *index) && ! endComparator(obj, *index)) {
            if ([obj isKindOfClass:[NodeOpenBracket_Aux class]] || [obj isKindOfClass:[NodeCloseBracket_Aux class]]) {
                [toRemove addObject:obj];
            }
        }
    }];
    for (NodeOperator *bracket in toRemove) {
        [nodes removeObjectIdenticalTo:bracket];
    }
    
    [self nodes:nodes joinBinOperators:[NodeDivide class]   withRange:range];
    [self nodes:nodes joinBinOperators:[NodeMultiply class] withRange:range];
    
    [self nodes:nodes joinBinOperators:[NodeMinus class]    withRange:range];
    [self nodes:nodes joinBinOperators:[NodePlus class]     withRange:range];
    
    [self nodes:nodes joinBinOperators:[NodeEquality class] withRange:range];
}

- (void)nodes:(NSMutableArray*)nodes joinBinOperators:(Class)operatorCls withRange:(Pair*)range {
    [nodes enumerateBetweetWithStartComparator:range.firstObject endComparator:range.secondObject usingBlock:^(id node, NSInteger *index, BOOL *stop) {
        if ([node isKindOfClass:operatorCls] && [node firstOperand] == nil && [node secondOperand] == nil) {
            Node *prevToken = [nodes objectAtIndex:*index - 1];
            Node *nextToken = [nodes objectAtIndex:*index + 1];
            
            [node setFirstOperand:prevToken];
            [node setSecondOperand:nextToken];
            
            [nodes removeObjectAtIndex:*index + 1];
            [nodes removeObjectAtIndex:*index - 1];
        }
    }];
}


@end
