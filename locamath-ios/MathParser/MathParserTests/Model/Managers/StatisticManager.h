//
//  StatisticHelper.h
//  Mathematic
//
//  Created by Developer on 25.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface StatisticManager : NSObject

@property (readonly, nonatomic) NSMutableArray *values;
@property (readonly, nonatomic) NSMutableArray *titles;

- (id)initWithDateType:(DateType)dateType
            taskStatus:(TaskStatus)taskStatus
              taskType:(TaskType)taskType
                 error:(ActionErrorType)actionError;

+ (NSInteger)earnedScoreByCurrentPlayer;

@end
