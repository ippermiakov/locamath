//
//  AlertViewManager.m
//  Mathematic
//
//  Created by alexbutenko on 7/2/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "AlertViewManager.h"
#import "AppDelegate.h"

@implementation AlertViewManager

+ (AlertViewManager *)sharedInstance
{
    static dispatch_once_t pred;
    
    static AlertViewManager *sharedInstance = nil;
    
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    
    return sharedInstance;
}

@end
