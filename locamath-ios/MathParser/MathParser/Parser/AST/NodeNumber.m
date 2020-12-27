//
//  NodeNumber.m
//  ExpressionParser
//
//  Created by Dmitriy Gubanov on 20.09.12.
//  Copyright (c) 2012 Dmitriy Gubanov. All rights reserved.
//

#import "NodeNumber.h"
#import "NSString+MD5.h"

@implementation NodeNumber

@synthesize number = _number;

- (id)initWithNumber:(double)number {
    self = [super init];
    if (self != nil) {
        self.number = [NSNumber numberWithDouble:number];
    }
    
    return self;
}

+ (id)nodeWithNumber:(double)number {
    return [[self alloc] initWithNumber:number];
}

- (NSUInteger)hash {
    if (self.needsToCompareJustStructure == NO) {
        NSString *numberString = [NSString stringWithFormat:@"%f", [self.number doubleValue]];
        NSString *md5String = [numberString stringFromMD5];
        return [md5String hash];
    } else {
        return 1;
    }
}

- (NSString*)description {
    return [NSString stringWithFormat:@"<%@>, value:%@", self.class, self.number];
}

- (double)value {
    return self.number.doubleValue;
}


- (void)enumerateUsingBlock:(EnumeratingBlock)block {
    if (block != nil) {
        block(self);
    }
}

- (id)copyWithZone:(NSZone *)zone
{
    NodeNumber *copy = [NodeNumber new];
    
    copy.number = self.number.copy;
    
    return copy;
}

@end
