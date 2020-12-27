//
//  OlympiadHint.m
//  Mathematic
//
//  Created by alexbutenko on 3/19/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadHint.h"
#import "OlympiadAction.h"
#import "OlympiadTask.h"

@implementation OlympiadHint

@dynamic identifier;
@dynamic hasUserInput;
@dynamic hintString;
@dynamic userInput;
@dynamic baseUserInput;
@dynamic action;

- (void)setUserInput:(NSString *)userInput
{
    [self willChangeValueForKey:@"userInput"];
    [self setPrimitiveValue:userInput forKey:@"userInput"];
    [self didChangeValueForKey:@"userInput"];
    
    NSUInteger toolIndex = [self.action.task.tools indexOfObject:userInput];
    
    NSString *baseUserInput = userInput;
    
    if (NSNotFound != toolIndex && self.action.task.baseTools[toolIndex]) {
        baseUserInput = self.action.task.baseTools[toolIndex];
    }
    
//    NSLog(@"baseUserInput: %@", self.baseUserInput);
    
    [self willChangeValueForKey:@"baseUserInput"];
    [self setPrimitiveValue:baseUserInput forKey:@"baseUserInput"];
    [self didChangeValueForKey:@"baseUserInput"];
}

- (void)updateUserInputIfNeeded
{
//    if (self.baseUserInput)
//        NSLog(@"baseUserInput: %@", self.baseUserInput);

    if (self.action.task.baseTools) {
        //we get baseUserInput, we need to translate it to localized userInput
        NSUInteger toolIndex = [self.action.task.baseTools indexOfObject:self.userInput];
        
        if (NSNotFound != toolIndex && self.action.task.tools[toolIndex]) {
            [self setUserInput:self.action.task.tools[toolIndex]];
        }
    } else {
        
        NSUInteger toolIndex = [self.action.task.tools indexOfObject:self.userInput];

        //this case could appear, when we're not synchronized from the server after language changing
        if (NSNotFound == toolIndex && self.baseUserInput) {
            [self setUserInput:self.baseUserInput];
        }
    }
}

@end
