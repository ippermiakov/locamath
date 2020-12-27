//
//  SBFileParser.h
//  Mathematic
//
//  Created by Alexander on 10/31/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef void(^MTFileParserCompletionBlock)();
typedef void(^MTFileParserFailureBlock)(NSError *error);

@class Child;

@interface MTFileParser : NSObject

@property (nonatomic, strong, readwrite) Child *currentChild;

+ (MTFileParser *)sharedInstance;

- (void)parseFilesToCoreDataForChild:(Child *)child
                             success:(MTFileParserCompletionBlock)success
                             failure:(MTFileParserFailureBlock)failure;
- (void)parseLocalFilesToCoreDataForChild:(Child *)child completion:(MTFileParserCompletionBlock)completionBlock;

@end
