//
//  GameManager.m
//  Mathematic
//
//  Created by Developer on 10.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "GameManager.h"
#import "Game.h"
#import "Task.h"
#import "Action.h"
#import "DataUtils.h"
#import "ChildManager.h"
#import "Parent.h"

@interface GameManager ()

@property (nonatomic, unsafe_unretained) NSUInteger taskSeconds;
@property (nonatomic, strong) NSTimer *appTimer;
@property (nonatomic, strong) NSTimer *taskTimer;

@end

@implementation GameManager

- (id)init
{
    self = [super init];
    if (self) {
        
//        ChildManager *childManager = [ChildManager sharedInstance];
        
//        if (childManager.currentChild) {
//            
//            NSPredicate *predicate = [NSPredicate predicateWithFormat:@"child == %@", childManager.currentChild];
//            self.game = [Game findFirstWithPredicate:predicate];
//            
//            if (!self.game) {
//                self.game = [Game createGame];
//                self.game.secondsTimeInApp = [NSNumber numberWithLong:0];
//                
//                [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
//            }
//            
//            [self startAppTimer];
//
//        } else {
            self.game = [Game findFirst];
        
            if (!self.game) {
                self.game = [Game createGame];
                [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
            }
            
//            childManager.addChildCreateGameBlock = ^(Child *child) {
//                
//                NSPredicate *predicate = [NSPredicate predicateWithFormat:@"child == %@", child];
//                self.game = [Game findFirstWithPredicate:predicate];
//                
//                if (!self.game) {
//                    self.game = [Game createGame];
//                    //self.game.secondsTimeInApp = [NSNumber numberWithLong:0];
//                }
//              
//                [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
//                //[self startAppTimer];
//            };
       // }
    }
    return self;
}

+ (GameManager *)sharedInstance
{
    static dispatch_once_t pred;
    static GameManager *sharedInstance = nil;
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    return sharedInstance;
}

#pragma mark Timer methods

- (void)invalidateTimers
{
//    [self stopAppTimer];
    [self stopTaskTimer];
}

//- (void)startAppTimer
//{
//    self.appSeconds = [self.game.secondsTimeInApp integerValue];
//    self.appTimer = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(increaseAppTime) userInfo:nil repeats:YES];
//}

//- (void)stopAppTimer
//{
//    [self.appTimer invalidate];
//    self.appTimer = nil;
//}

//- (void)increaseAppTime
//{
//    self.appSeconds++;
//}

- (void)startTaskTimerForTask:(Task *)task
{
    self.taskSeconds = [task.secondsPerTask longValue];
    self.taskTimer = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(increaseTaskTime) userInfo:nil repeats:YES];
}

- (void)stopTaskTimer
{
    [self.taskTimer invalidate];
    self.taskTimer = nil;
}

- (void)increaseTaskTime
{
    self.taskSeconds++;
}

#pragma mark - Main methods

- (void)saveData
{
    [self invalidateTimers];
//    [self setSecondsTimeInApp];
}

- (NSUInteger)getSecondsForTask
{
    [self updateProgressForUser];

    [self stopTaskTimer];
    
    NSUInteger tempTaskSeconds = self.taskSeconds;
    self.taskSeconds = 0;
    
    return tempTaskSeconds;
}

+ (BOOL)hasProgressChild:(Child *)child
{    
    return [child.game.hasProgress boolValue];
}

- (void)logOffParent
{
    [[MTHTTPClient sharedMTHTTPClient] logout];
    [[ChildManager sharedInstance] logoutCurrentChild];
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

+ (NSString *)currentLocalization
{
    NSString *localization = [NSLocale preferredLanguages][0];
    
    if ([localization length] > 2) {
        localization = [localization substringToIndex:2];
    }
    
    return localization;
}

+ (LevelMapViewController *)levelMap
{
    return (LevelMapViewController *)[[[UIApplication sharedApplication].delegate window] rootViewController];
}

#pragma mark - Helper

//- (void)setSecondsTimeInApp
//{
//    self.game.secondsTimeInApp = [NSNumber numberWithLong:self.appSeconds];
//    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
//}

- (void)updateProgressForUser
{
    self.game.hasProgress = @YES;
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

@end
