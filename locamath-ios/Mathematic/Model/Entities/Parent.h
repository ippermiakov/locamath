//
//  Parent.h
//  Mathematic
//
//  Created by alexbutenko on 9/2/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Child;

@interface Parent : NSManagedObject

@property (nonatomic, retain) NSString * email;
@property (nonatomic, retain) NSString * password;
@property (nonatomic, retain) NSString * city;
@property (nonatomic, retain) NSString * country;
@property (nonatomic, retain) NSNumber * fbID;
@property (nonatomic, retain) NSSet *childs;
@property (nonatomic, retain) NSNumber *identifier;
@property (nonatomic, retain) NSNumber *latitude;
@property (nonatomic, retain) NSNumber *longitude;
@property (nonatomic, retain) NSNumber *isSchoolMode;

- (BOOL)isPasswordEqualToPassword:(NSString *)password;
- (void)setPasswordString:(NSString *)password;

@end

@interface Parent (CoreDataGeneratedAccessors)

- (void)addChildsObject:(Child *)value;
- (void)removeChildsObject:(Child *)value;
- (void)addChilds:(NSSet *)values;
- (void)removeChilds:(NSSet *)values;

@end
