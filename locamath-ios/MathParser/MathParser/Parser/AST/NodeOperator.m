//
//  NodeOperation.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeOperator.h"

@implementation NodeOperator

- (NSUInteger)subnodesHash {
    return 0; // Ordered and unordered operations shall implement it
}

- (NSUInteger)hash {
    long long result = [NSStringFromClass(self.class) hash]; // Objects equals when they are of the same class
    
    result += [self subnodesHash];         // and subnodes are same
    
    return result % UINT32_MAX;
}

@end
