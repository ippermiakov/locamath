//
//  Child.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 11.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "Child.h"
#import "LevelsPath.h"

@implementation Child

@dynamic identifier;
@dynamic name;

@dynamic country;
@dynamic city;

@dynamic avatar;
@dynamic genderNumber;
@dynamic isCurrent;
@dynamic isTrainingComplete;
@dynamic isDataLoaded;
@dynamic isSyncCompleted;
@dynamic dataLoadedForLanguage;
@dynamic parent;
@dynamic game;
@dynamic levels;
@dynamic tasks;
@dynamic olympiadTasks;
@dynamic olympiadLevels;
@dynamic levelsPaths;
@dynamic isMusicEnabled;
@dynamic isSoundEnabled;
@dynamic isSyncNeeded;
@dynamic lastLevelsSyncTimeInterval;
@dynamic lastOlympiadLevelsSyncTimeInterval;
@dynamic modificationJSONTimeInterval;
@dynamic previousModificationJSONTimeInterval;
@dynamic points;
@dynamic postFBAccount;

@dynamic postTypes;
@dynamic sendTypes;

@dynamic sendStatisticsAccounts;
@dynamic helpPages;
@dynamic schemes;
@dynamic isLocationPopupShown;
@dynamic selectionDate;
@dynamic spendTimes;

#pragma mark - NSObject

- (NSString*)description
{
    return [NSString stringWithFormat:@"%@, userData:<name: %@, country: %@, city: %@, is male: %d>", [super description], self.name, self.country, self.city, self.gender == Male];
}

- (NSString *)avatarWithSuffix:(NSString *)suffix
{
    NSString *avatarWithSuffix = nil;
    
    NSRange range = [self.avatar rangeOfString:@"@2x"];
    
    if (NSNotFound != range.location) {
        NSString *beginning = [self.avatar substringToIndex:range.location];
        NSString *ending    = [self.avatar substringFromIndex:range.location + range.length];
        
        avatarWithSuffix = [NSString stringWithFormat:@"%@%@%@%@", beginning, suffix, @"@2x", ending];
    } else {
        NSLog(@"not found corresponding small avatar for %@", self.avatar);
    }
    
    return avatarWithSuffix;
}

#pragma mark - Setters&Getters

- (void)setGender:(Gender)gender
{
    [self willChangeValueForKey:@"genderNumber"];
    [self setPrimitiveValue:@(gender) forKey:@"genderNumber"];
    [self didChangeValueForKey:@"genderNumber"];
}

- (Gender)gender
{
    [self willAccessValueForKey:@"genderNumber"];
    Gender scalarGender = [[self primitiveValueForKey:@"genderNumber"] integerValue];
    [self didAccessValueForKey:@"genderNumber"];
    return scalarGender;
}

- (NSString *)bigAvatar
{
    NSRange range = [self.avatar rangeOfString:@"@2x"];
    if (range.location != NSNotFound) {
        NSString *beginning = [self.avatar substringToIndex:range.location];
        NSString *ending    = [self.avatar substringFromIndex:range.location + range.length];
        return [NSString stringWithFormat:@"%@%@%@", beginning, @"_Big", ending];
    } else {
        return [self.avatar stringByAppendingString:@"_Big"];
    }
}

- (void)setIsCurrent:(NSNumber *)isCurrent
{
    [self willChangeValueForKey:@"isCurrent"];
    [self setPrimitiveValue:isCurrent forKey:@"isCurrent"];
    [self didChangeValueForKey:@"isCurrent"];
    
    self.isSyncNeeded = @YES;
}

- (NSNumber *)isDataLoaded
{
    NSString *currentLanguage = [NSLocale preferredLanguages][0];
    NSNumber *numberOfPaths = [LevelsPath numberOfEntitiesWithPredicate:[NSPredicate predicateWithFormat:@"child.identifier == %@", self.identifier]];
    
    return @([self.dataLoadedForLanguage isEqualToString:currentLanguage] &&
             [self.modificationJSONTimeInterval integerValue] == [self.previousModificationJSONTimeInterval integerValue] &&
             [numberOfPaths integerValue] > 0);
}

- (void)setIsDataLoaded:(NSNumber *)isDataLoaded
{
    if ([isDataLoaded boolValue]) {
        self.dataLoadedForLanguage = [NSLocale preferredLanguages][0];
        NSLog(@"!!! data loaded for %@", self.name);
        [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    } else {
        self.dataLoadedForLanguage = nil;
    }
}

@end
