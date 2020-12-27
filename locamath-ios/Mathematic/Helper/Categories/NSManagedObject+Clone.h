//
//  NSManagedObject+Clone.h
//  Flixa
//
//  Created by alexbutenko on 7/11/13.
//  Copyright (c) 2013 Developer. All rights reserved.
//

#import <CoreData/CoreData.h>

@interface NSManagedObject (Clone)

- (NSManagedObject *)cloneInContext:(NSManagedObjectContext *)context
                     exludeEntities:(NSArray *)namesOfEntitiesToExclude;

- (NSManagedObject *)cloneInContext:(NSManagedObjectContext *)context;
- (NSManagedObject *)cloneInCurrentContext;

@end
