//
//  Pair.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 20.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "Pair.h"

@implementation Pair

- (NSString*)description {
    return [NSString stringWithFormat:@"<%@: %@>, %@, %@", self.class, self, self.firstObject, self.secondObject];
}

@end
