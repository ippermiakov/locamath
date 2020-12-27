//
//  NSManagedObject+Achievement.m
//  Mathematic
//
//  Created by alexbutenko on 7/5/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NSManagedObject+Achievement.h"
#import "AbstractAchievement.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"

@implementation NSManagedObject (Achievement)

- (BOOL)importLastChangeDate:(id)data
{
    NSLog(@"id: %@ date: %@", [(id)self identifier], data);
    if ([(NSNumber *)data doubleValue] > 0) {
        NSTimeInterval dateAttr = [(NSNumber *)data doubleValue];
        
        id<AbstractAchievement> achievement = (id<AbstractAchievement>)self;
        
        achievement.lastChangeDate = [NSDate dateWithTimeIntervalSince1970GMT:dateAttr];
        
        if (dateAttr > 0) {
           NSLog(@"id: %@ date: %@", [(id)self identifier], achievement.lastChangeDate);
        }
    }
    return YES;
}

@end
