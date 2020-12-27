//
//  Parent.m
//  Mathematic
//
//  Created by alexbutenko on 9/2/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Parent.h"
#import "Child.h"
#import "NSString+MD5.h"

@implementation Parent

@dynamic email;
@dynamic password;
@dynamic fbID;
@dynamic childs;
@dynamic identifier;
@dynamic latitude;
@dynamic longitude;
@dynamic city;
@dynamic country;

- (void)setPassword:(NSString *)password
{
    [self willChangeValueForKey:@"password"];
    [self setPrimitiveValue:[password stringFromMD5] forKey:@"password"];
    [self didChangeValueForKey:@"password"];
}

- (void)setPasswordString:(NSString *)password
{
    [self willChangeValueForKey:@"password"];
    [self setPrimitiveValue:password forKey:@"password"];
    [self didChangeValueForKey:@"password"];
}

- (BOOL)isPasswordEqualToPassword:(NSString *)password
{
    NSString *passwordHash = [password stringFromMD5];
    
    return [self.password isEqualToString:passwordHash];
}

@end
