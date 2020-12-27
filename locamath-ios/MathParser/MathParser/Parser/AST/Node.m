//
//  Node.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "Node.h"

@implementation Node

@synthesize children                    = _children;
@synthesize needsToCompareJustStructure = _needsToCompareJustStructure;

- (void)setNeedsToCompareJustStructure:(BOOL)needsToCompareJustStructure {
    _needsToCompareJustStructure = needsToCompareJustStructure;
    for (Node *child in self.children) {
        child.needsToCompareJustStructure = needsToCompareJustStructure;
    }
}

- (id)init {
    self = [super init];
    if(self != nil) {
        self.children = [NSMutableArray new];
    }
    
    return self;
}

- (double)value {
    @throw [NSException exceptionWithName:NSInternalInconsistencyException reason:@"It's a abstract method: you have to implement it in child" userInfo:nil];
}

- (BOOL)isThereSubNode:(Node*)node {
    return self == node;
}

- (BOOL)isEqual:(id)object {
    return [self hash] == [object hash];
}


- (void)enumerateUsingBlock:(EnumeratingBlock)block {
    for (Node *node in self.children) {
        [node enumerateUsingBlock:block];
    }
}

- (id)copyWithZone:(NSZone *)zone
{
    Node *copy = [[self class] new];
    
    copy.children = [[NSMutableArray alloc] initWithArray:self.children copyItems:YES];
    
    return copy;
}

@end
