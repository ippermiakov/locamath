//
//  UnixtimeWithoutLocaleOffset.h
//  Mathematic
//
//  Created by alexbutenko on 9/5/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSDate (UnixtimeWithoutLocaleOffset)

- (NSTimeInterval)timeIntervalSince1970GMT;
+ (NSDate *)dateWithTimeIntervalSince1970GMT:(NSTimeInterval)timeInterval;

@end
