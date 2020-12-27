//
//  NodeOrderedOperation.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeOrderedOperator.h"

@implementation NodeOrderedOperator

- (NSUInteger)subnodesHash { // Ordered version
    long long result = 0;
    
    NSUInteger nodeMultiplyer = 1;
    
    for (Node *node in self.children) {
        result += node.hash * nodeMultiplyer;
        nodeMultiplyer++;
    }
    
    return result % UINT32_MAX;
}

@end
