//
//  Parser.h
//  Mathematic
//
//  Created by Developer on 25.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ParselableActionProtocol.h"

@protocol ParserDelegate <NSObject>

- (void)didFailedParsingAction:(id<ParselableActionProtocol>)action;

@end

@interface Parser : NSObject

@property (weak, nonatomic) id<ParserDelegate> delegate;

- (void)parseWithActions:(NSArray *)actions withEtalons:(NSArray *)expressions;

@end
