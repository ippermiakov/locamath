//
//  NSError+String.m
//  Flixa
//
//  Created by alexbutenko on 5/21/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import "NSError+String.h"

@implementation NSError (String)

+ (NSError *)errorWithString:(NSString *)errorString
{
    NSDictionary *userInfoDict = @{NSLocalizedDescriptionKey: errorString};
    NSError *error = [[NSError alloc] initWithDomain:NSCocoaErrorDomain code:-1 userInfo:userInfoDict];
    
    return error;
}

+ (NSError *)errorWithString:(NSString *)errorString andCode:(NSUInteger)code
{
    NSDictionary *userInfoDict = @{NSLocalizedDescriptionKey: errorString};
    NSError *error = [[NSError alloc] initWithDomain:NSCocoaErrorDomain code:code userInfo:userInfoDict];

    return error;
}


@end
