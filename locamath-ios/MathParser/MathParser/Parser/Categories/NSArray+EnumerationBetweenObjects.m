//
//  NSArray+EnumerationBetweenObjects.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 20.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "NSArray+EnumerationBetweenObjects.h"
#import "Node.h"


@implementation NSArray (EnumerationBetweenObjects)

- (NSInteger)indexOfObjectUsingComparator:(ComparatorBlock)comparator {
    NSInteger retIndex = NSNotFound;
    for (NSInteger i = 0; i < self.count; ++i) {
        if (comparator([self objectAtIndex:i], i)) {
            retIndex = i;
            break;
        }
    }
    return retIndex;
}

- (void)enumerateBetweetWithStartComparator:(ComparatorBlock)startComparator
                              endComparator:(ComparatorBlock)endComparator
                                 usingBlock:(EnumBlock)block {
    NSInteger startIndex = [self indexOfObjectUsingComparator:startComparator];
    NSInteger endIndex   = [self indexOfObjectUsingComparator:endComparator];
    
    if (startIndex == NSNotFound || endIndex == NSNotFound) {
        @throw [NSException exceptionWithName:NSInvalidArgumentException reason:@"There are no that objects in this array" userInfo:nil];
    }
    id currObj = nil;
    id nextObj = nil;
    
    for (NSInteger i = startIndex; i < self.count && ! endComparator([self objectAtIndex:MAX(i - 1, 0)], i);) {
        BOOL stop = NO;
        NSInteger changingIndex = i;
        
//        @try {
        currObj = [self objectAtIndex:i];
        
        if (self.count > i + 1) {
            nextObj = [self objectAtIndex:i + 1];
        } else {
            nextObj = nil;
            stop = YES;
        }
//        }
//        @catch (NSException *exception) {
//            nextObj = nil;
//        }
//        @finally {}
        
        block(currObj, &changingIndex, &stop);
        NSInteger newI = [self indexOfObjectPassingTest:^BOOL(id obj, NSUInteger idx, BOOL *stop) {
            BOOL isItThat = obj == nextObj || [obj isThereSubNode:nextObj];
            if (isItThat) {
                *stop = YES;
            }
            return isItThat;
        }];
        
        if (newI != NSNotFound) {
            i = newI;
        } else {
            stop = YES;
        }
        
        if (stop == YES) {
            break;
        }
    }
}

- (id)nextObjectToObject:(id)object
{
    id result = nil;
    
    NSUInteger idx = [self indexOfObjectIdenticalTo:object];
    
    if (NSNotFound != idx) {
        
        if (idx + 1 < [self count]) {
            result = self[idx + 1];
        }
    }
    
    return result;
}


- (id)prevObjectToObject:(id)object
{
    id result = nil;
    
    NSInteger idx = [self indexOfObjectIdenticalTo:object];
    
    if (NSNotFound != idx) {
        
        if (idx - 1 >= 0) {
            result = self[idx - 1];
        }
    }
    
    return result;
}

- (id)firstObject
{
	if ([self count] == 0) {
	    return nil;
	}
	
	return [self objectAtIndex:0];
}

@end




@implementation NSMutableArray (SortingWithRange)

- (void)sortRange:(NSRange)range usingComparator:(NSComparator)cmptr withIngoringObject:(id)toIgnore {
    for (NSInteger i = 0; i < range.length; ++i) {
        for (NSInteger i = range.location; i < range.location + range.length; ++i) {
            NSInteger next = i + 1;
            while ([[self objectAtIndex:next] isEqual:toIgnore])
                ++next;
            if (next >= range.location + range.length)
                break;
            if (cmptr([self objectAtIndex:i], [self objectAtIndex:next]) == NSOrderedDescending) {
                id temp = [self objectAtIndex:i];
                [self replaceObjectAtIndex:i    withObject:[self objectAtIndex:next]];
                [self replaceObjectAtIndex:next withObject:temp];
            }
        }
    }
}

@end
