//
//  HelpChainBuilder.h
//  
//
//  Created by Dmitriy Gubanov on 22.04.13.
//
//

#import <Foundation/Foundation.h>

@class PresentableViewController;
@class PresentingSeguesStructure;

@interface HelpChainBuilder : NSObject

+ (PresentingSeguesStructure *)helpChainWithLevelID:(NSString *)levelID;

@end
