//
//  NodeEquality.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 04.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "NodeEquality.h"

@implementation NodeEquality

- (double)value {
    return self.firstOperand.value == self.secondOperand.value;
}

@end
