//
//  AccountMail.h
//  Mathematic
//
//  Created by SanyaIOS on 26.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Child;

@interface AccountMail : NSManagedObject

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSString * name;
@property (nonatomic, retain) Child *child;

@end
