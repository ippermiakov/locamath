//
//  NSObject+AbstractMethods.h
//  Flixa
//
//  Created by alexbutenko on 6/3/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSException (AbstractMethods)

+ (NSException *)exceptionForAbstractMethod:(SEL)method;

@end
