//
//  AbstractAchievement.h
//  Mathematic
//
//  Created by alexbutenko on 6/26/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "NSManagedObject+Achievement.h"

@protocol AbstractAchievement <NSObject>

@property (nonatomic, retain) NSDate * lastChangeDate;

- (NSString *)statisticDescription;
- (Class)controllerClass;

@optional
- (NSString *)taskStatisticFixOrErrorDescription;


@end
