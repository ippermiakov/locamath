//
//  Action+Creation.m
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Action+Creation.h"
#include <mach/mach_time.h>

@implementation Action (Creation)

//NB: actually type doesn't influe on tests logic now
+ (Action *)actionOfType:(ActionType)type task:(Task *)task withString:(NSString *)expression
{
    Action *action = [Action createEntity];
    
    action.identifier = [NSString stringWithFormat:@"%llu", mach_absolute_time()];

    [action addSubActionWithString:expression];
    
    action.type = type;
    action.answer = [[expression componentsSeparatedByString:@"="] lastObject];

    action.task = task;
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];

    return action;
}

@end
