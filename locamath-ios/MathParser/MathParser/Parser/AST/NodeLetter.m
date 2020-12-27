//
//  NodeLetter.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 07.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NodeLetter.h"

@implementation NodeLetter

- (id)initWithLetter:(NSString*)letter {
    self = [super init];
    if (self != nil) {
        self.number = [NSNumber numberWithChar:[letter characterAtIndex:0]]; // Assigning ascii code of a letter as a number
    }
    
    return self;
}

+ (id)nodeWithLetter:(NSString*)letter {
    return [[self alloc] initWithLetter:letter];
}

@end
