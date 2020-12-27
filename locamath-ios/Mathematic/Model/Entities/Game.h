//
//  Game.h
//  Mathematic
//
//  Created by Developer on 10.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class Level, Child;

@interface Game : NSManagedObject

@property (nonatomic, retain) NSNumber * secondsTimeInApp;
@property (nonatomic, retain) NSNumber * secondsAverageSpeed;
@property (nonatomic, retain) NSString * maxHardTaskID;
@property (nonatomic, retain) NSString * maxEasyTaskID;
@property (nonatomic, retain) NSNumber * solvedTasksCount;
@property (nonatomic, retain) NSNumber * findSolutionsCount;
@property (nonatomic, retain) NSNumber * findExpressionCount;
@property (nonatomic, retain) NSNumber * earnedScore;
@property (nonatomic, retain) NSNumber * hasProgress;
@property (nonatomic, retain) NSNumber * identifier;

@property (nonatomic, retain) NSNumber * skipStatisticScreen;

@property (nonatomic, retain) Child *child;
@property (nonatomic, retain) Level *openedLevel;

+ (Game *)createGame;

@end
