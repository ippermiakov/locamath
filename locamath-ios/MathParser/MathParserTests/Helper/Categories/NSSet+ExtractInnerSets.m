//
//  NSSet+ExtractInnerSets.m
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSSet+ExtractInnerSets.h"

@implementation NSSet (ExtractInnerSets)

- (NSSet *)setByExtractingInnerSets
{
    NSSet *set = [self reject:^BOOL(id obj) {
        return ![obj count];
    }];
    
    set = [set reduce:[NSSet set] withBlock:^id(NSSet *sum, id obj) {
        return [sum setByAddingObjectsFromSet:obj];
    }];
    
    return set;
}

@end
