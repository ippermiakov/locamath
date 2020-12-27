//
//  ExpressionParser.h
//  Mathematic
//
//  Created by Developer on 03.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AST.h"

extern NSString * const kInvalidSyntaxException;

@interface ExpressionParser : NSObject

- (Node*)parse:(NSString*)parsingString;

@end
