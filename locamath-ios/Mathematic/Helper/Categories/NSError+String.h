//
//  NSError+String.h
//  Flixa
//
//  Created by alexbutenko on 5/21/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSError (String)

+ (NSError *)errorWithString:(NSString *)errorString;
+ (NSError *)errorWithString:(NSString *)errorString andCode:(NSUInteger)code;

@end
