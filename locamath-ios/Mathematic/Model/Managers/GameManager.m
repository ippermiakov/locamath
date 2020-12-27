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
#import "SpendTimeStatistic.h"

@interface GameManager ()

@property (nonatomic, unsafe_unretained) CGFloat taskSeconds;
@property (nonatomic, strong) NSTimer *appTimer;
@property (nonatomic, strong) NSTimer *taskTimer;
@property (unsafe_unretained, nonatomic) BOOL isAppTimerStart;
@property (strong, nonatomic) NSString *startTimerDate;

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
        [sharedInstance startAppTimer];
    });
    
    return sharedInstance;
}

#pragma mark Timer methods

- (void)invalidateTimers
{
    [self stopTaskTimer];
}

- (void)startAppTimer
{
    if (!self.isAppTimerStart && [ChildManager sharedInstance].currentChild && [DataUtils currentParent]) {
        
        self.isAppTimerStart = YES;
        self.startTimerDate = [DataUtils presentDateString];
        
        [[ChildManager sharedInstance] updateSpendTimeStatisticIfNeeded];
        
        SpendTimeStatistic *spendTimeEntitisForChild = [self spendTimeEntitisForChild];
        self.appSeconds = [spendTimeEntitisForChild.time integerValue];
        self.appTimer = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(increaseAppTime) userInfo:nil repeats:YES];
    }
}

- (void)stopAppTimer
{
    if (self.isAppTimerStart) {
        [self.appTimer invalidate];
        self.appTimer = nil;
        
        self.isAppTimerStart = NO;
        
        SpendTimeStatistic *spendTimeEntitisForChild = [self spendTimeEntitisForChild];
        spendTimeEntitisForChild.time = @(self.appSeconds);
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    }
}

- (void)increaseAppTime
{
    self.appSeconds++;
    
    if (![self.startTimerDate isEqualToString:[DataUtils presentDateString]]) {
        
        SpendTimeStatistic *spendTime = [DataUtils spendTimeStatisticForCurrentChildWithDateString:
                                          self.startTimerDate];
        spendTime.time = @(self.appSeconds);
        
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        self.appSeconds = 0;
        
        [self stopAppTimer];
        [self startAppTimer];
    }
}

- (SpendTimeStatistic *)spendTimeEntitisForChild
{
    SpendTimeStatistic *spendTime = [DataUtils spendTimeStatisticForCurrentChildWithDateString:[DataUtils presentDateString]];
    
    if (!spendTime) {
        spendTime = [SpendTimeStatistic createEntity];
        spendTime.statisticDate = [DataUtils presentDateString];
        spendTime.childId = [ChildManager sharedInstance].currentChild.identifier;
        spendTime.child = [ChildManager sharedInstance].currentChild;
    }
    
    return spendTime;
}

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
