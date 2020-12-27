//
//  Action+Creation.h
//  Mathematic
//
//  Created by Alex on 21.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Action.h"

@interface Action (Creation)

+ (Action *)actionOfType:(ActionType)type task:(Task *)task withString:(NSString *)expression;

@end
