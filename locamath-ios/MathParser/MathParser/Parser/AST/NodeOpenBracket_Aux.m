//
//  NodeBracket_Aux.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 21.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "NodeOpenBracket_Aux.h"

@implementation NodeOpenBracket_Aux

- (BOOL)isEqual:(id)obj {
    return [obj isKindOfClass:self.class] || ([obj isKindOfClass:[NSString class]] && [obj isEqualToString:@"("]);
}


@end
