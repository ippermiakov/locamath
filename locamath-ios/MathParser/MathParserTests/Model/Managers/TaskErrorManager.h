//
//  ErrorManager.h
//  Mathematic
//
//  Created by Developer on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class Task;

@interface TaskErrorManager : NSObject

+ (NSString *)errorDescriptionOnAnswerForActions:(NSArray *)actions withTask:(Task *)task;
+ (NSMutableDictionary *)errorInfoOnTaskSolvingWithActions:(NSArray *)actions withTask:(Task *)task;

@end