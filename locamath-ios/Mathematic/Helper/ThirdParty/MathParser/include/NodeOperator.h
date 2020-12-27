//
//  NodeOperation.h
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "Node.h"

@interface NodeOperator : Node // An abstract class

- (NSUInteger)subnodesHash;     // Pure virtual method - you have to implement it in derived class

@end
