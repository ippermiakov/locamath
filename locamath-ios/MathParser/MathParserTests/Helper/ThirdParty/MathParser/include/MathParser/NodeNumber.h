//
//  NodeNumber.h
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "Node.h"

@interface NodeNumber : Node

@property(strong, nonatomic)NSNumber *number;

- (id)initWithNumber:(double)number;
+ (id)nodeWithNumber:(double)number;

@end
