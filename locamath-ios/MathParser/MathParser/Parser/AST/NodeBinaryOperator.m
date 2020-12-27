//
//  NodeBinaryOperation.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeBinaryOperator.h"
#import "NodeNumber.h"

@implementation NodeBinaryOperator

@dynamic firstOperand;
@dynamic secondOperand;

- (void)setFirstOperand:(Node*)operand {
    if (self.children.count < 1) {
        [self.children addObject:operand];
    } else {
        [self.children replaceObjectAtIndex:0 withObject:operand];
    }
}

- (void)setSecondOperand:(Node*)operand {
    if (self.children.count < 2) {
        if (self.children.count < 1) {
            [self.children addObject:[NSNull null]];
        }
        [self.children addObject:operand];
    } else {
        [self.children replaceObjectAtIndex:1 withObject:operand];
    }
}

- (Node*)firstOperand {
    Node *result = nil;
    if (self.children.count > 0) {
        result = [self.children objectAtIndex:0];
    }
    
    return result;
}

- (Node*)secondOperand {
    Node *result = nil;
    if (self.children.count > 1) {
        result = [self.children objectAtIndex:1];
    }
    
    return result;
}

- (id)initWithFirstOperand:(Node*)firstOperand andSecondOperand:(Node*)secondOperand {
    self = [super init];
    if (self != nil) {
        [self.children addObject:firstOperand];
        [self.children addObject:secondOperand];
    }
    
    return self;
}

+ (id)nodeWithFirstOperand:(Node*)firstOperand andSecondOperand:(Node*)secondOperand {
    return [[self alloc] initWithFirstOperand:firstOperand andSecondOperand:secondOperand];
}

- (NSString*)description {
    return [NSString stringWithFormat:@"<%@>, children: {%@, %@}", self.class, self.firstOperand, self.secondOperand];
}

- (BOOL)isThereSubNode:(Node*)node {
    return
    self == node ||
    [self.firstOperand  isThereSubNode:node] ||
    [self.secondOperand isThereSubNode:node];
}

- (BOOL)isCorrect {
    return
    self.firstOperand != nil && self.secondOperand != nil && 
    ([self.firstOperand isKindOfClass:[NodeNumber class]] || [(NodeBinaryOperator*)self.firstOperand isCorrect]) &&
    ([self.secondOperand isKindOfClass:[NodeNumber class]] || [(NodeBinaryOperator*)self.secondOperand isCorrect]);
}

@end
