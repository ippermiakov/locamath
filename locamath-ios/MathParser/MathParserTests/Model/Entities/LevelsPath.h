//
//  LevelsPath.h
//  Mathematic
//
//  Created by alexbutenko on 6/26/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>
#import "AbstractAchievement.h"

@class Level, Child;

@interface LevelsPath : NSManagedObject<AbstractAchievement>

@property (nonatomic, retain) NSNumber *identifier;
@property (nonatomic, retain) NSString * name;
@property (nonatomic, retain) NSString * color;
@property (nonatomic, retain) NSNumber * isAllLevelsSolved;
@property (nonatomic, retain) id transitionErrors;
@property (nonatomic, retain) NSNumber *levelNumber;
@property (nonatomic, retain) NSString *olympiadLocalText;
@property (nonatomic, retain) NSNumber *isGrowingAnimated;
@property (nonatomic, retain) NSNumber *isStarAnimated;

@property (nonatomic, retain) NSSet *levels;
@property (nonatomic, retain) Child *child;

//to open LevelsPath's levels
@property (nonatomic, readonly) Level *requiredLevel;

- (BOOL)isOpened;

@end

@interface LevelsPath (CoreDataGeneratedAccessors)

- (void)addLevelsObject:(Level *)value;
- (void)removeLevelsObject:(Level *)value;
- (void)addLevels:(NSSet *)values;
- (void)removeLevels:(NSSet *)values;

@end
