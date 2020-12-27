//
//  NSDictionary+Bidirection.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 07.03.13.
//  Copyright (c) 2013 Alexander Matrosov. All rights reserved.
//

#import "NSDictionary+Bidirection.h"

@implementation NSDictionary (Bidirection)

- (id)keyForObject:(id)object {
    id retKey = nil;
    
    for (id key in self.allKeys) {
        if ([self objectForKey:key] == object) {
            retKey = key;
        }
    }
    
    return retKey;
}

@end
