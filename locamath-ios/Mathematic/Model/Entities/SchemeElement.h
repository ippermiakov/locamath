//
//  SchemeElement.h
//  Mathematic
//
//  Created by SanyaIOS on 20.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Scheme;

@interface SchemeElement : NSManagedObject

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSNumber * typeNumber;
@property (nonatomic, retain) NSNumber * position_x;
@property (nonatomic, retain) NSNumber * position_y;
@property (nonatomic, retain) NSNumber * isFilled;
@property (nonatomic, retain) Scheme *scheme;

@end
