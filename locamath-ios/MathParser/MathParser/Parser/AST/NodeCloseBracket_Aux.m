//
//  NodeCloseBracket_Aux.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 21.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "NodeCloseBracket_Aux.h"

@implementation NodeCloseBracket_Aux

- (BOOL)isEqual:(id)obj {
    return [obj isKindOfClass:self.class] || ([obj isKindOfClass:[NSString class]] && [obj isEqualToString:@")"]);
}


@end
