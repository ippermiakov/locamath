//
//  NSObject+AbstractMethods.m
//  Flixa
//
//  Created by alexbutenko on 6/3/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "NSException+AbstractMethods.h"

@implementation NSException (AbstractMethods)

+ (NSException *)exceptionForAbstractMethod:(SEL)method
{
    return [NSException exceptionWithName:NSInternalInconsistencyException
                                   reason:[NSString stringWithFormat:@"%@ abstract method: you have to implement it in child", NSStringFromSelector(method)]
                                 userInfo:nil];
}

@end
