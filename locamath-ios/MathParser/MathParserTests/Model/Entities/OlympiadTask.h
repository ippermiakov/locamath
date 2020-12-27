//
//  OlympiadTask.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 01.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractTask.h"

typedef NS_ENUM(NSUInteger, HintsAlignmentType) {
    HintsAlignmentTypeNone,
    HintsAlignmentTypeRight,
    HintsAlignmentTypeLeft
};

@class OlympiadAction, OlympiadLevel, Child;

@interface OlympiadTask : NSManagedObject<AbstractTask>

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSNumber * isAnyAnswerApplicable;
@property (nonatomic, retain) NSNumber * alignmentTypeNumber;
@property (nonatomic, readonly) NSNumber * isOneToolToOneAnswerMapping;
@property (nonatomic, unsafe_unretained) HintsAlignmentType alignmentType;
@property (nonatomic, retain) NSNumber * tryCounter;
@property (nonatomic, retain) NSNumber * points;
@property (nonatomic, retain) id tools;
@property (nonatomic, retain) id baseTools; //base language (en) tools
@property (nonatomic, retain) id solutionHint;

- (BOOL)isCorrect;
- (NSUInteger)longestToolsLength;

@end

@interface OlympiadTask (CoreDataGeneratedAccessors)

- (void)addActionsObject:(OlympiadAction *)value;
- (void)removeActionsObject:(OlympiadAction *)value;
- (void)addActions:(NSSet *)values;
- (void)removeActions:(NSSet *)values;

@end
