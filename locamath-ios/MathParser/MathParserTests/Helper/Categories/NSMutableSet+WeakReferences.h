//
//  NSMutableSet+WeakReferences.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 05.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSMutableSet (WeakReferences)

+ (id)mutableSetUsingWeakReferences;
+ (id)mutableSetUsingWeakReferencesWithCapacity:(NSUInteger)capacity;

@end
