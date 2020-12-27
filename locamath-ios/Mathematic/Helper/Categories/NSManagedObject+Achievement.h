//
//  NSManagedObject+Achievement.h
//  Mathematic
//
//  Created by alexbutenko on 7/5/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <CoreData/CoreData.h>
#import "AbstractAchievement.h"

@interface NSManagedObject (Achievement)

- (BOOL)importLastChangeDate:(id)data;

@end
