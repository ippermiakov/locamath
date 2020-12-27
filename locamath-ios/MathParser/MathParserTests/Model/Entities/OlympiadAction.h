//
//  OlympiadAction.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 01.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class OlympiadHint, OlympiadTask;

@interface OlympiadAction : NSManagedObject

@property (nonatomic, retain) id answers;
@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSNumber * isCorrect;
@property (nonatomic, readonly) NSNumber * isFilled;
@property (nonatomic, retain) NSNumber * numOfToolsToFill;
@property (nonatomic, retain) NSSet *hints;
@property (nonatomic, retain) OlympiadTask *task;

- (void)updateIsCorrect;

@end

@interface OlympiadAction (CoreDataGeneratedAccessors)

- (void)addHintsObject:(OlympiadHint *)value;
- (void)removeHintsObject:(OlympiadHint *)value;
- (void)addHints:(NSSet *)values;
- (void)removeHints:(NSSet *)values;

@end
