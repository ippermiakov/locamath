//
//  Game.m
//  Mathematic
//
//  Created by Developer on 10.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Game.h"
#import "ChildManager.h"

@implementation Game

@dynamic secondsTimeInApp;
@dynamic secondsAverageSpeed;
@dynamic maxHardTaskID;
@dynamic maxEasyTaskID;
@dynamic solvedTasksCount;
@dynamic findSolutionsCount;
@dynamic findExpressionCount;
@dynamic earnedScore;
@dynamic hasProgress;
@dynamic child;
@dynamic openedLevel;
@dynamic identifier;
@dynamic skipStatisticScreen;

+ (Game *)createGame
{
    Game *game = [Game createEntity];
    game.child = [[ChildManager sharedInstance] currentChild];
    
    return game;
}

@end
