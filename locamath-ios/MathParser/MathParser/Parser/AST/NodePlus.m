//
//  NodePlus.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodePlus.h"

@implementation NodePlus

- (double)value {
    return self.firstOperand.value + self.secondOperand.value;
}

@end
