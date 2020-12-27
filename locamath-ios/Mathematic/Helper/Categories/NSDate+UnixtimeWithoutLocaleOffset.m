//
//  UnixtimeWithoutLocaleOffset.m
//  Mathematic
//
//  Created by alexbutenko on 9/5/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSDate+UnixtimeWithoutLocaleOffset.h"

@implementation NSDate (UnixtimeWithoutLocaleOffset)

- (NSTimeInterval)timeIntervalSince1970GMT
{
    NSTimeInterval localUnixTime = [self timeIntervalSince1970];
    NSTimeInterval GMTUnixTime = localUnixTime - [[NSTimeZone localTimeZone] secondsFromGMT];
        
    return GMTUnixTime;
}

+ (NSDate *)dateWithTimeIntervalSince1970GMT:(NSTimeInterval)timeInterval
{
    NSTimeInterval localUnixTimeInterval = timeInterval + [[NSTimeZone localTimeZone] secondsFromGMT];
    
    return [NSDate dateWithTimeIntervalSince1970:localUnixTimeInterval];
}

@end
