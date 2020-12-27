//
//  NSArray+ExtractInnerSet.m
//  Flixa
//
//  Created by alexbutenko on 5/1/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "NSArray+ExtractInnerSet.h"

@implementation NSArray (ExtractInnerSet)

- (NSArray *)arrayByExtractingInnerSets
{
    NSArray *array = [self reject:^BOOL(id obj) {
        return ![obj count];
    }];

    array = [array reduce:@[] withBlock:^id(NSArray *sum, id obj) {
        return [sum arrayByAddingObjectsFromArray:[obj allObjects]];
    }];

    return array;
}

@end
