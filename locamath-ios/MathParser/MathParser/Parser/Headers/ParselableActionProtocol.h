//
//  ParselableActionProtocol.h
//  Mathematic
//
//  Created by alexbutenko on 11/19/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef NS_OPTIONS(NSUInteger, ActionErrorType) {
    kActionErrorTypeNone        = 0,
    kActionErrorTypeStructure   = 1 << 0,
    kActionErrorTypeCalculation = 1 << 1
};

@protocol ParselableActionProtocol <NSObject>

@property (unsafe_unretained, nonatomic) ActionErrorType error;
@property (nonatomic, retain) NSNumber *etalon;
//string representation of action
@property (nonatomic, retain) NSString * string;
@property (nonatomic, retain) NSOrderedSet *subActions;

@end
