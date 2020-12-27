//
//  NodeUnorderedOperation.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeUnorderedOperator.h"

@implementation NodeUnorderedOperator

- (NSUInteger)subnodesHash { // Unordered version
    long long result = 0;
    
    for (Node *node in self.children) {
        result += node.hash;
    }
    
    return result % UINT32_MAX;
}

@end
