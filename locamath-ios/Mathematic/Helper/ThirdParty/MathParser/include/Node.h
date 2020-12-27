//
//  Node.h
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import <Foundation/Foundation.h>


@class NodeNumber;

typedef void(^EnumeratingBlock)(NodeNumber *node);


@interface Node : NSObject <NSCopying>

@property(strong, nonatomic)NSMutableArray  *children;
@property(unsafe_unretained, nonatomic)BOOL needsToCompareJustStructure;

- (double)value;
- (BOOL)isThereSubNode:(Node*)node;
- (void)enumerateUsingBlock:(EnumeratingBlock)block;

@end
