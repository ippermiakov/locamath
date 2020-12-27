//
//  NodeMinus.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeMinus.h"

@implementation NodeMinus

- (double)value {
    return self.firstOperand.value - self.secondOperand.value;
}

@end
