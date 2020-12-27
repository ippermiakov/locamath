//
//  TransitionsManager.h
//  Mathematic
//
//  Created by alexbutenko on 6/27/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class Level, Task, OlympiadLevel;

@interface TransitionsManager : NSObject

+ (id)sharedInstance;

- (BOOL)canOpenLevel:(Level *)level error:(NSError **)error;
- (BOOL)canOpenTask:(Task *)task error:(NSError **)error;
- (BOOL)canOpenOlympiadLevel:(OlympiadLevel *)level error:(NSError **)error;

@end
