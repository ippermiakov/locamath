//
//  NSMutableSet+WeakReferences.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 05.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSMutableSet+WeakReferences.h"

@implementation NSMutableSet (WeakReferences)

+ (id)mutableSetUsingWeakReferences
{
    return [self mutableSetUsingWeakReferencesWithCapacity:0];
}

+ (id)mutableSetUsingWeakReferencesWithCapacity:(NSUInteger)capacity
{
    CFSetCallBacks callbacks = {0, NULL, NULL, CFCopyDescription, CFEqual};
    return (id)CFBridgingRelease(CFSetCreateMutable(0, capacity, &callbacks));
}

@end
