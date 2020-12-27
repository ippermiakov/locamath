//
//  GameManager.h
//  Mathematic
//
//  Created by Developer on 10.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "LevelMapViewController.h"

@class Task;
@class Game;

@interface GameManager : NSObject

@property (nonatomic, unsafe_unretained) NSUInteger appSeconds;
@property (nonatomic, strong) Game *game;

+ (GameManager *)sharedInstance;

//- (void)startAppTimer;
- (void)startTaskTimerForTask:(Task *)task;
- (void)stopTaskTimer;

- (void)saveData;
- (NSUInteger)getSecondsForTask;
+ (BOOL)hasProgressChild:(Child *)child;

- (void)logOffParent;
+ (NSString *)currentLocalization;
+ (LevelMapViewController *)levelMap;

@end
