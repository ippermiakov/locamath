//
//  Action.m
//  Mathematic
//
//  Created by alexbutenko on 4/1/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Action.h"
#import "Action.h"
#import "Task.h"
#include <mach/mach_time.h>


@implementation Action

@dynamic answer;
@dynamic errorNumber;
@dynamic identifier;
@dynamic typeNumber;
@dynamic string;
@dynamic task;
@dynamic subActions;
@dynamic parentAction;
@dynamic isCorrect;
@dynamic etalon;
@dynamic taskError;

- (BOOL)importSubActions:(id)data
{
    NSArray *dict = data[@"subActions"];
    
    if (dict.count) {
        [dict each:^(NSDictionary *sender) {
            Action *subAction = [Action importFromObject:sender];
            subAction.parentAction = self;
        }];
    }
    
    return YES;
}

- (void)setType:(ActionType)type
{
    [self willChangeValueForKey:@"typeNumber"];
    [self setPrimitiveValue:[NSNumber numberWithInteger:type] forKey:@"typeNumber"];
    [self didChangeValueForKey:@"typeNumber"];
}

- (ActionType)type
{
    [self willAccessValueForKey:@"typeNumber"];
    ActionType result = [[self primitiveValueForKey:@"typeNumber"] integerValue];
    [self didAccessValueForKey:@"typeNumber"];
    return result;
}

- (void)setError:(ActionErrorType)error
{
    [self willChangeValueForKey:@"errorNumber"];
    [self setPrimitiveValue:[NSNumber numberWithInteger:error] forKey:@"errorNumber"];
    [self didChangeValueForKey:@"errorNumber"];
}

- (ActionErrorType)error
{
    [self willAccessValueForKey:@"errorNumber"];
    ActionErrorType result = [[self primitiveValueForKey:@"errorNumber"] integerValue];
    [self didAccessValueForKey:@"errorNumber"];
    return result;
}

- (Action *)addSubActionWithString:(NSString *)string
{
    Action *action = [Action actionWithString:string];
    action.parentAction = self;
    
    return action;
}

- (NSString *)answer
{
    [self willAccessValueForKey:@"answer"];
    NSString *primitiveAnswer = [self primitiveValueForKey:@"answer"];
    [self didAccessValueForKey:@"answer"];

    if (!primitiveAnswer) {
        NSString *answer = [[self.string componentsSeparatedByString:@"="] lastObject];
        return answer;
    }
    
    return primitiveAnswer;
}

+ (id)actionWithString:(NSString *)string
{
    Action *action = [Action createEntity];
    action.identifier = [NSString stringWithFormat:@"%llu", mach_absolute_time()];
    [action updateWithString:string];

    return action;
}

- (void)updateWithString:(NSString *)string
{
    self.string = string;

    NSString *answer = [[string componentsSeparatedByString:@"="] lastObject];
    self.answer = answer;
}

//HACK: http://stackoverflow.com/questions/7385439/exception-thrown-in-nsorderedset-generated-accessors/7922993#7922993
- (void)replaceObjectInSubActionsAtIndex:(NSUInteger)idx withObject:(Action *)value
{
    NSMutableOrderedSet* tempSet = [NSMutableOrderedSet orderedSetWithOrderedSet:self.subActions];
    [tempSet replaceObjectAtIndex:idx withObject:value];
    self.subActions = tempSet;
}

- (BOOL)isActionEqualToAction:(Action *)action
{
    return [self.etalon isEqualToNumber:action.etalon] &&
            self.answer.length > 0 && action.answer.length > 0 &&
            [self.subActions count] == [action.subActions count] &&
            self.error == action.error;
}

@end
