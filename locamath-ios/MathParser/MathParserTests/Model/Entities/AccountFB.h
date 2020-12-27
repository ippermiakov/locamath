//
//  AccountFB.h
//  Mathematic
//
//  Created by SanyaIOS on 11.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Child;

@interface AccountFB : NSManagedObject

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSString * mail;
@property (nonatomic, retain) Child *child;

@end
