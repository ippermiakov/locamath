//
//  SpendTimeStatistic.h
//  Mathematic
//
//  Created by SanyaIOS on 12/18/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "Child.h"


@interface SpendTimeStatistic : NSManagedObject

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSString * statisticDate;
@property (nonatomic, retain) NSNumber * childId;
@property (nonatomic, retain) NSNumber * time;

@property (nonatomic, retain) Child *child;

@end
