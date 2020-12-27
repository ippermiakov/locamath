//
//  Child.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

static NSUInteger const kNewUnsynchronizedChildID = NSUIntegerMax;

typedef enum {
    Undefined,
    Male,
    Female
} Gender;

@class Game, Level, Task, AccountMail, AccountFB, HelpPage, Scheme, Parent;

@interface Child : NSManagedObject

@property (retain, nonatomic) NSNumber *identifier;
@property (retain, nonatomic) NSString *name;

@property (retain, nonatomic) NSString *country;
@property (retain, nonatomic) NSString *city;

@property (retain, nonatomic) NSString *avatar;
@property (retain, nonatomic) NSNumber *genderNumber;
@property (retain, nonatomic) NSNumber *isCurrent;
@property (retain, nonatomic) NSNumber *isTrainingComplete;
@property (retain, nonatomic) NSString *dataLoadedForLanguage;
@property (retain, nonatomic) NSNumber *isDataLoaded;
@property (retain, nonatomic) NSNumber *isSyncCompleted;
@property (readonly, nonatomic) NSString *bigAvatar;
@property (retain, nonatomic) NSNumber *isSoundEnabled;
@property (retain, nonatomic) NSNumber *isMusicEnabled;
@property (retain, nonatomic) NSNumber *points;
@property (retain, nonatomic) NSNumber *isSyncNeeded;
@property (retain, nonatomic) NSNumber *lastLevelsSyncTimeInterval; //unixtime GMT
@property (retain, nonatomic) NSNumber *lastOlympiadLevelsSyncTimeInterval; //unixtime GMT
@property (retain, nonatomic) NSNumber *previousModificationJSONTimeInterval; //unixtime GMT
@property (retain, nonatomic) NSNumber *modificationJSONTimeInterval; //unixtime GMT

@property (retain, nonatomic) NSNumber *postTypes;
@property (retain, nonatomic) NSNumber *sendTypes;

@property (unsafe_unretained, nonatomic) Gender gender;
@property (retain, nonatomic) Game *game;
@property (retain, nonatomic) Parent *parent;
@property (retain, nonatomic) AccountFB *postFBAccount;
@property (retain, nonatomic) NSSet *levels;
@property (retain, nonatomic) NSSet *tasks;
@property (retain, nonatomic) NSSet *olympiadTasks;
@property (retain, nonatomic) NSSet *olympiadLevels;
@property (retain, nonatomic) NSSet *levelsPaths;
@property (retain, nonatomic) NSSet *sendStatisticsAccounts;
@property (retain, nonatomic) NSSet *schemes;

@property (retain, nonatomic) NSSet *helpPages;
@property (retain, nonatomic) NSNumber *isLocationPopupShown;

- (NSString *)avatarWithSuffix:(NSString *)suffix;

@end

@interface Child (CoreDataGeneratedAccessors)

- (void)addLevelsObject:(Level *)value;
- (void)removeLevelsObject:(Level *)value;
- (void)addLevels:(NSSet *)values;
- (void)removeLevels:(NSSet *)values;

- (void)addTasksObject:(Task *)value;
- (void)removeTasksObject:(Task *)value;
- (void)addTasks:(NSSet *)values;
- (void)removeTasks:(NSSet *)values;

- (void)addOlympiadTasksObject:(Level *)value;
- (void)removeOlympiadTasksObject:(Level *)value;
- (void)addOlympiadTasks:(NSSet *)values;
- (void)removeOlympiadTasks:(NSSet *)values;

- (void)addOlympiadLevelsObject:(Level *)value;
- (void)removeOlympiadLevelsObject:(Level *)value;
- (void)addOlympiadLevels:(NSSet *)values;
- (void)removeOlympiadLevels:(NSSet *)values;

- (void)addLevelsPathsObject:(Level *)value;
- (void)removeLevelsPathsObject:(Level *)value;
- (void)addLevelsPaths:(NSSet *)values;
- (void)removeLevelsPaths:(NSSet *)values;

- (void)addSendStatisticsAccountsObject:(AccountMail *)value;
- (void)removeSendStatisticsAccountsObject:(AccountMail *)value;
- (void)addSendStatisticsAccounts:(NSSet *)values;
- (void)removeSendStatisticsAccounts:(NSSet *)values;

- (void)addHelpPageObject:(HelpPage *)value;
- (void)removeHelpPageObject:(HelpPage *)value;
- (void)addHelpPage:(NSSet *)values;
- (void)removeHelpPage:(NSSet *)values;

- (void)addSchemeObject:(Scheme *)value;
- (void)removeSchemePageObject:(Scheme *)value;
- (void)addScheme:(NSSet *)values;
- (void)removeScheme:(NSSet *)values;

@end