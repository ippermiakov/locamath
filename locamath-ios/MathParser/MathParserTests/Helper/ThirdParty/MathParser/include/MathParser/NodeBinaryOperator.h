//
//  NodeBinaryOperation.h
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeOperator.h"

@interface NodeBinaryOperator : NodeOperator

@property(nonatomic)Node *firstOperand;
@property(nonatomic)Node *secondOperand;

- (id)initWithFirstOperand:(Node*)firstOperand andSecondOperand:(Node*)secondOperand;
+ (id)nodeWithFirstOperand:(Node*)firstOperand andSecondOperand:(Node*)secondOperand;

- (BOOL)isCorrect;

@end
