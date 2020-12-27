//
//  NSArray+EnumerationBetweenObjects.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 20.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSArray (EnumerationBetweenObjects)

typedef void(^EnumBlock)(id obj, NSInteger *index, BOOL *stop);
typedef BOOL(^ComparatorBlock)(id obj, NSInteger index);

- (NSInteger)indexOfObjectUsingComparator:(ComparatorBlock)comparator;
- (void)enumerateBetweetWithStartComparator:(ComparatorBlock)startComparator endComparator:(ComparatorBlock)endComparator usingBlock:(EnumBlock)block;
- (id)nextObjectToObject:(id)object;
- (id)prevObjectToObject:(id)object;
- (id)firstObject;

@end

@interface NSMutableArray (SortingWithRange)

- (void)sortRange:(NSRange)range usingComparator:(NSComparator)cmptr withIngoringObject:(id)toIgnore;

@end