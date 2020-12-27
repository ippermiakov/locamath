//
//  Scheme.h
//  Mathematic
//
//  Created by SanyaIOS on 20.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Child;

@interface Scheme : NSManagedObject

@property (nonatomic, retain) NSString * identifier;
@property (nonatomic, retain) NSSet *elements;
@property (nonatomic, retain) Child *child;
@end

@interface Scheme (CoreDataGeneratedAccessors)

- (void)addElementsObject:(NSManagedObject *)value;
- (void)removeElementsObject:(NSManagedObject *)value;
- (void)addElements:(NSSet *)values;
- (void)removeElements:(NSSet *)values;

@end
