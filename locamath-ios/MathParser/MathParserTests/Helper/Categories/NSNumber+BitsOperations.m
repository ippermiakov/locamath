//
//  NSNumber+BitsOperations.m
//  Mathematic
//
//  Created by alexbutenko on 9/16/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSNumber+BitsOperations.h"

@implementation NSNumber (BitsOperations)

- (NSNumber *)numberWithSwitchedBitAtIndex:(NSUInteger)index
{
    NSUInteger bitMask = [self integerValue];
    
    NSNumber *result = nil;
    
    //is set previously?
    if ((bitMask >> index) & 1) {
        result = @(bitMask &~ (1 << index));
    } else {
        result = @(bitMask | (1 << index));
    }
    
    NSLog(@"number with switched bit: %@", result);
    
    return result;
}

@end
