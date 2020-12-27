//
//  NSArray+SortViewsByXOrigin.m
//  Flixa
//
//  Created by alexbutenko on 5/31/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "NSArray+SortViewsByXOrigin.h"

@implementation NSArray (SortViewsByXOrigin)

- (NSArray *)sortedArrayByXOrigin
{
    return [self sortedArrayUsingComparator:^NSComparisonResult(id label1, id label2) {
        if ([label1 frame].origin.x < [label2 frame].origin.x) return NSOrderedAscending;
        else if ([label1 frame].origin.x > [label2 frame].origin.x) return NSOrderedDescending;
        else return NSOrderedSame;
    }];    
}
- (NSArray *)sortedArrayByYOrigin
{
    return [self sortedArrayUsingComparator:^NSComparisonResult(id label1, id label2) {
        if ([label1 frame].origin.y < [label2 frame].origin.y) return NSOrderedAscending;
        else if ([label1 frame].origin.y > [label2 frame].origin.y) return NSOrderedDescending;
        else return NSOrderedSame;
    }];
}

@end
