//
//  Constants.h
//  Mathematic
//
//  Created by Alexander on 9/10/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

extern NSString * const kNotificationGoToTheNextTask;
extern NSInteger const kDefaultStarsCount;
extern NSString * const kBothSolutionsType;
extern NSString * const kAnimationBackgoundImageName;

// Action
typedef NS_ENUM(NSUInteger, ActionType) {
    kActionTypeExpression = 0,
    kActionTypeSolution   = 1,
    kActionTypeAnswer     = 2
};

// Task
typedef NS_ENUM(NSUInteger, TaskStatus) {
    kTaskStatusUndefined = 0,
    kTaskStatusSolved  = 1,
    kTaskStatusError   = 2,
    kTaskStatusStarted = 3,
    kTaskStatusSolvedNotAll = 4
};

typedef NS_ENUM(NSUInteger, TaskType) {
    kTaskTypeCommon     = 1,
    kTaskTypeTraining   = 2,
    kTaskTypeTest       = 3
};

typedef NS_ENUM(NSUInteger, TaskErrorType) {
    kTaskErrorTypeCalculation   = 0,
    kTaskErrorTypeUnderstanding = 1
};

//levels

typedef NS_ENUM(NSUInteger, LevelType) {
    kLevelType1 = 0,
    kLevelType2 = 1,
    kLevelType3 = 2,
    kLevelType4 = 3
};

typedef NS_ENUM(NSUInteger, SchemeElementType) {
    kSchemeElementTypeBracket1 = 0,
    kSchemeElementTypeBracket2 = 1,
    kSchemeElementTypeBracket3 = 2,
    kSchemeElementTypeBracket4 = 3,
    kSchemeElementTypeBracket6 = 4,
    kSchemeElementTypeBracketDown6 = 5,
    kSchemeElementTypeBracket7 = 6,
    kSchemeElementTypeBracket9 = 7,
    kSchemeElementTypeBracketRound = 8,
    kSchemeElementTypeDash = 9,
    kSchemeElementTypeDashLine = 10,
    kSchemeElementTypeFlag = 11,
    kSchemeElementTypeLArr1 = 12,
    kSchemeElementTypeLArr2 = 13,
    kSchemeElementTypeLArr3 = 14,
    kSchemeElementTypeLArr4 = 15,
    kSchemeElementTypeLine1 = 16,
    kSchemeElementTypeLine2 = 17,
    kSchemeElementTypeLine3 = 18,
    kSchemeElementTypeLine4 = 19,
    kSchemeElementTypeLine6 = 20,
    kSchemeElementTypeLine7 = 21,
    kSchemeElementTypeLineBase = 22,
    kSchemeElementTypeRArr1 = 23,
    kSchemeElementTypeRArr2 = 24,
    kSchemeElementTypeRArr3 = 25,
    kSchemeElementTypeRArr4 = 26,
//    kSchemeElementTypeV1 = 27,
    kSchemeElementTypeBracketDown3 = 28,
    kSchemeElementType1 = 29,
    kSchemeElementType2 = 30,
    kSchemeElementType3 = 31,
    kSchemeElementType4 = 32,
    kSchemeElementType5 = 33,
    kSchemeElementType6 = 34,
    kSchemeElementType7 = 35,
    kSchemeElementType8 = 36,
    kSchemeElementType9 = 37,
    kSchemeElementType0 = 38,
    kSchemeElementTypeA = 39,
    kSchemeElementTypeB = 40,
    kSchemeElementTypeC = 41,
    kSchemeElementTypeQuestionMark = 42,
    kSchemeElementTypeS = 43,
    kSchemeElementTypeV = 44,
    kSchemeElementTypeT = 45,
    kSchemeElementTypeMinus = 46,
    kSchemeElementTypeAdd = 47,
    kSchemeElementTypeBracketDown2 = 48,
    kSchemeElementTypeBracketDown4 = 49,
    kSchemeElementTypeBracketDown7 = 50,
    kSchemeElementTypeV1 = 51,
    kSchemeElementTypeV2 = 52,
    kSchemeElementTypeS1 = 53,
    kSchemeElementTypeS2 = 54,
    kSchemeElementTypeEqval = 55,
    kSchemeElementTypeBracketDown1 = 56
};


extern NSString * const kTaskErrorInfoStatus;
extern NSString * const kTaskErrorInfoDescription;
extern NSString * const kDefaultChildName;

extern CGFloat    const kActionViewMargin;
extern CGFloat    const kActionViewHeaderHeight;

extern CGFloat    const kSubActionViewHeight;
extern CGFloat    const kSubActionViewMargin;

extern NSString * const kSettingsSoundsOffOn;
extern NSString * const kSettingsMusicOffOn;
extern NSString * const kSettingsName;
extern NSString * const kSettingsCity;
extern NSString * const kSettingsCountry;

// Task Button Type

typedef NS_ENUM(NSUInteger, TaskButtonType) {
    kTaskButtonTypeTraining = 0,
    kTaskButtonTypeCommon   = 1
};


// Data Type

typedef NS_ENUM(NSUInteger, DateType) {
    kDateTypeDay    = 0,
    kDateTypeWeek   = 1,
    kDateTypeMonth  = 2
};

extern NSString * const kCellIdentifier;

//http://stackoverflow.com/questions/7017281/performselector-may-cause-a-leak-because-its-selector-is-unknown
#define SuppressPerformSelectorLeakWarning(Stuff) \
do { \
_Pragma("clang diagnostic push") \
_Pragma("clang diagnostic ignored \"-Warc-performSelector-leaks\"") \
Stuff; \
_Pragma("clang diagnostic pop") \
} while (0)
