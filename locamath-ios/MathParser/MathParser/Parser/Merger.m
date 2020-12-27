//
//  Merger.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 04.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "Merger.h"
#import "AST.h"
#import "NSArray+EnumerationBetweenObjects.h"

@interface Merger()

@property (strong, nonatomic) NSMutableArray *expressions;
@property (strong, nonatomic) NSMutableArray *finalExtensionExpressions;

@end

@implementation Merger

- (NSArray *)merge:(NSMutableArray *)expressions
{
    self.expressions = expressions;
    
    id currentNode = [self.expressions firstObject];
    
    while (nil != currentNode) {
        [self extendWithNode:currentNode];
        currentNode = [self.expressions nextObjectToObject:currentNode];
//        NSLog(@"currentNode: %@", currentNode);
    }
    
    return [self.finalExtensionExpressions count] ? self.finalExtensionExpressions:self.expressions;
}

//TODO: could be moved to category or Nodes
+ (NodeBinaryOperator *)leftPart:(NodeBinaryOperator *)eqExpr
{
    NodeBinaryOperator  *theExpr  = nil;
    
    if ([eqExpr isKindOfClass:[NodeEquality class]]) {
        theExpr = (NodeBinaryOperator*)eqExpr.firstOperand;
        NSAssert([theExpr isKindOfClass:[NodeBinaryOperator class]], @"Left part of equality operator should be of NodeBinaryOperator class");
    } else {
        theExpr = eqExpr;
    }

    return theExpr;
}

+ (NodeNumber *)rightPart:(NodeEquality *)eqExpr
{
    NodeNumber          *value    = nil;
    
    if ([eqExpr isKindOfClass:[NodeEquality class]]) {
        value = (NodeNumber*)eqExpr.secondOperand;
        NSAssert([value isKindOfClass:[NodeNumber class]], @"Right part of equality operator should be of NodeNumber class");
    }
    
    return value;
}

- (NSArray *)upAct:(id)currentNode
          toExtend:(NodeBinaryOperator *)toExtend
        processNum:(NodeNumber *)processNum
{
    NSMutableArray *processedNodesArray  = [NSMutableArray new];
    
    NodeBinaryOperator *leftPartToExtend = [Merger leftPart:toExtend];
    
    id nodeToCorrectCalculationErrors = currentNode;
    
    BOOL isExtended = [self addExtendedOperator:leftPartToExtend
                                     processNum:processNum
                               startingFromNode:currentNode
                            processedNodesArray:processedNodesArray];

    while (!isExtended && nodeToCorrectCalculationErrors != nil) {
        NodeEquality *previousActionNode = [self.expressions prevObjectToObject:nodeToCorrectCalculationErrors]/*iterToCorrectCalculationErrors.prev.obj*/;
        NodeEquality *currentActionNode = nodeToCorrectCalculationErrors/*.obj*/;

        NodeNumber *previousActionAnswer = [Merger rightPart:previousActionNode];
        NodeNumber *currentActionAnswerBeforeFix = [Merger rightPart:currentActionNode];
        
        //check that we found calculation error fix
        if (previousActionAnswer && processNum.value == previousActionAnswer.value) {
            
            processNum = [NodeNumber nodeWithNumber:previousActionNode.firstOperand.value];
            
            //fix calculation error in previous action
            previousActionNode.secondOperand = processNum.copy;
            
            NodeBinaryOperator *currentExpressionToFix = [Merger leftPart:nodeToCorrectCalculationErrors/*.obj*/];

//            NSLog(@"currentExpresionToReplace before: %@", currentExpressionToFix);
            
            //fix calculation error in current action
            if (currentExpressionToFix.firstOperand.value == previousActionAnswer.value) {
                currentExpressionToFix.firstOperand = processNum.copy;
            } else if (currentExpressionToFix.secondOperand.value == previousActionAnswer.value) {
                currentExpressionToFix.secondOperand = processNum.copy;
            }
            
            NodeNumber *currentActionAnswerAfterFix = [[NodeNumber alloc] initWithNumber:currentExpressionToFix.value];

            currentActionNode.secondOperand = currentActionAnswerAfterFix;
            
//            NSLog(@"prevExpressionToReplace: %@ \n\n currentExpressionToReplace: %@", previousActionNode, currentActionNode);
            
            //fix calculation error in further action, in which incorrect answer is used

            id nodeToCorrectFutherActionCalculationErrors = [self.expressions nextObjectToObject:nodeToCorrectCalculationErrors];
            
            //fix just one occurence
            BOOL isFixedErrorInFurtherAction = NO;
            
            while (!isFixedErrorInFurtherAction && nodeToCorrectFutherActionCalculationErrors != nil) {
                
                NodeEquality *furtherActionToFix = nodeToCorrectFutherActionCalculationErrors;
                NodeBinaryOperator *furtherActionExpressionToFix  = [Merger leftPart:nodeToCorrectFutherActionCalculationErrors];
                
//                NSLog(@"check %@ for %@ to fix with %@", furtherActionExpressionToFix, currentActionAnswerBeforeFix, currentActionAnswerAfterFix);
                
                if (furtherActionExpressionToFix.firstOperand.value == currentActionAnswerBeforeFix.value) {
//                    NSLog(@"need to fix %@ with %@", furtherActionToFix, currentActionAnswerAfterFix);
                    furtherActionExpressionToFix.firstOperand = currentActionAnswerAfterFix.copy;
                    furtherActionToFix.secondOperand = [NodeNumber nodeWithNumber:furtherActionExpressionToFix.value];
                    isFixedErrorInFurtherAction = YES;

//                    NSLog(@"fixed %@ with %@", furtherActionToFix, currentActionAnswerAfterFix);
                } else if (furtherActionExpressionToFix.secondOperand.value == currentActionAnswerBeforeFix.value) {
//                    NSLog(@"need to fix %@ with %@", furtherActionExpressionToFix, currentActionAnswerAfterFix);
                    furtherActionExpressionToFix.secondOperand = currentActionAnswerAfterFix.copy;
                    furtherActionToFix.secondOperand = [NodeNumber nodeWithNumber:furtherActionExpressionToFix.value];
//                    NSLog(@"fixed %@ with %@", furtherActionExpressionToFix, currentActionAnswerAfterFix);
                    isFixedErrorInFurtherAction = YES;
                }
                
                nodeToCorrectFutherActionCalculationErrors = [self.expressions nextObjectToObject:nodeToCorrectFutherActionCalculationErrors];
            }
            
            return [self upAct:nodeToCorrectCalculationErrors
                      toExtend:currentActionNode
                    processNum:processNum];
        }
        
        nodeToCorrectCalculationErrors = [self.expressions prevObjectToObject:nodeToCorrectCalculationErrors] /*iterToCorrectCalculationErrors.prev*/;
    };
    
    return processedNodesArray;
}

- (BOOL)addExtendedOperator:(NodeBinaryOperator *)toExtend
                 processNum:(NodeNumber *)processNum
           startingFromNode:(id)currentNode
        processedNodesArray:(NSMutableArray *)processedNodesArray
{
    BOOL isAdded = NO;
    
    // Идем вверх (пацанчик к успеху идет)
    while (currentNode != nil) {
        // Здесь мы кроваво расчлениваем наше выражения - для нашего блюда нам нужна его левая часть
        NodeBinaryOperator *expr = [Merger leftPart:[self.expressions prevObjectToObject:currentNode]];
        
        // Проверяем, так ли она хороша, как нам нужно (а нужно нам, чтобы она была равна исходному значению)
        if (expr && fabs(processNum.value - expr.value) == 0) {
            
//            NSLog(@"processNum: %@ toExtend: %@ with: %@", processNum, toExtend, expr);
            
            NodeBinaryOperator *copyToAdd = toExtend.copy;
            
            if ([toExtend.firstOperand isKindOfClass:[NodeNumber class]] && toExtend.firstOperand.value == processNum.value) {
                copyToAdd.firstOperand  = expr.copy;
            } else {
                copyToAdd.secondOperand = expr.copy;
            }
            
            NodeEquality *eqOperator = [NodeEquality new];
            NodeNumber *number = [[NodeNumber alloc] initWithNumber:copyToAdd.value];
            
            eqOperator.firstOperand  = copyToAdd;
            eqOperator.secondOperand = number;
            
            [processedNodesArray addObject:eqOperator];
            isAdded = YES;
        }
        
        currentNode = [self.expressions prevObjectToObject:currentNode];
    }
    
    return isAdded;
}

- (void)extendWithNode:(id)processNode
{
    NodeBinaryOperator  *theExpr = [Merger leftPart:processNode];
    
    self.finalExtensionExpressions = [NSMutableArray new];
    
    // Здесь мы будем обрабатывать левый элемент текущего выражения
    [self.finalExtensionExpressions addObjectsFromArray:[self upAct:processNode
                                              toExtend:processNode
                                            processNum:(NodeNumber *)theExpr.firstOperand]];
    
    // Теперь мы обойдем полученный массив и применим обработку правого элемента к каждому полученному значению
    NSMutableArray *auxArr = [NSMutableArray new];
    
    for (NodeBinaryOperator *addedExpr in self.finalExtensionExpressions) {
        [auxArr addObjectsFromArray:[self upAct:processNode
                                       toExtend:addedExpr
                                     processNum:(NodeNumber *)theExpr.secondOperand]];
    }
    
    [self.finalExtensionExpressions addObjectsFromArray:auxArr];
    
    // А теперь короночка: обработаем правый элемент и дело в шляпе!
    [self.finalExtensionExpressions addObjectsFromArray:[self upAct:processNode
                                              toExtend:processNode
                                            processNum:(NodeNumber *)theExpr.secondOperand]];

    //push front merged expressions
    self.expressions = [[self.finalExtensionExpressions arrayByAddingObjectsFromArray:self.expressions] mutableCopy];
}

@end
