//
//  StatisticHelper.m
//  Mathematic
//
//  Created by Developer on 25.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StatisticManager.h"
#import "Task.h"
#import "Action.h"
#import "ChildManager.h"
#import "DataUtils.h"
#import "OlympiadTask.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"
#import "TaskError.h"

@interface DateRange : NSObject

@property (copy, nonatomic) NSString *name;
@property (copy, nonatomic) NSDate *startDate;
@property (copy, nonatomic) NSDate *endDate;

@end

@implementation DateRange

@end


@interface StatisticManager ()

@property (unsafe_unretained, nonatomic) DateType   dateType;
@property (unsafe_unretained, nonatomic) TaskStatus taskStatus;
@property (unsafe_unretained, nonatomic) TaskType   taskType;
@property (unsafe_unretained, nonatomic) ActionErrorType actionErrorType;
@property (strong, nonatomic) NSArray *tasks;
@property (strong, nonatomic) NSArray *days;
@property (unsafe_unretained, nonatomic) NSInteger startDayForMonthCoef;

@property (strong, nonatomic) NSMutableArray *values;
@property (strong, nonatomic) NSMutableArray *titles;

@end

@implementation StatisticManager

- (id)initWithDateType:(DateType)dateType
            taskStatus:(TaskStatus)taskStatus
              taskType:(TaskType)taskType
                 error:(ActionErrorType)actionError
{
    self = [super init];
    
    if (self) {
        self.dateType = dateType;
        self.taskStatus = taskStatus;
        self.taskType = taskType;
        self.actionErrorType = actionError;
        self.values = [NSMutableArray new];
        self.titles = [NSMutableArray new];
        [self retrieveData];
    }
    
    return self;
}

- (void)retrieveData
{    
    NSArray *tasks = [DataUtils allTasksFromCurrentChild];
    NSMutableArray *tempTasks = [NSMutableArray new];
    
    if (self.actionErrorType) {
        
        [tempTasks addObjectsFromArray:[self showedActionInGrafhFromTasks:tasks withTaskStatus:kTaskStatusError]];
            
        tasks = tempTasks;
    } else {
        
         [tempTasks addObjectsFromArray:[self showedActionInGrafhFromTasks:tasks withTaskStatus:kTaskStatusSolved]];
        
        for (OlympiadTask *olympiadTask in tasks) {
            if ([olympiadTask isKindOfClass:[OlympiadTask class]] && olympiadTask.status == kTaskStatusSolved) {
                [tempTasks addObject:olympiadTask];
            }
        }
        tasks = tempTasks;
    }
    
    NSString *dateTypeString;
    NSUInteger range;
    
    switch (self.dateType) {
        case kDateTypeDay:
            dateTypeString = NSLocalizedString(@"Day", @"Statistics");
            range = 20;
            break;
        case kDateTypeWeek:
            dateTypeString = NSLocalizedString(@"Week", @"Statistics");
            range = 8;
            break;
        case kDateTypeMonth:
            dateTypeString = NSLocalizedString(@"Month", @"Statistics");
            range = 12;
            break;
    }
    
    self.tasks = tasks;
    self.days = [self arrayCountDays];
    [self calculateStartDayForMonthCoef];
    
    for (NSUInteger i = 0; i < range; i++) {
        
        DateRange *dateRange = [self createRange:i];
        
        NSPredicate *datePredicate =
        [NSPredicate predicateWithFormat:@"lastChangeDate >= %@ && lastChangeDate <= %@", dateRange.startDate, dateRange.endDate];
        
        NSArray *filteredTasks = [tasks filteredArrayUsingPredicate:datePredicate];
        
        if ([filteredTasks count] > 0) {
            [self.values addObject:[NSNumber numberWithInteger:[filteredTasks count]]];
            [self.titles addObject:[self dataDescriptionWithTask:filteredTasks]];
        }
    }
}

#pragma mark - Main mehtods

- (NSDate *)dayStart:(NSDate *)date
{
    NSCalendar *cal = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *components = [cal components:(  NSDayCalendarUnit
                                                    | NSMonthCalendarUnit
                                                    | NSYearCalendarUnit
                                                    | NSHourCalendarUnit
                                                    | NSMinuteCalendarUnit
                                                    | NSSecondCalendarUnit ) fromDate:date];
    
    [components setHour:0];
    [components setMinute:0];
    [components setSecond:0];
        
    return [cal dateFromComponents:components];
}

- (NSDate *)dayEnd:(NSDate *)date
{
    NSCalendar *cal = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *components = [cal components:(  NSDayCalendarUnit
                                                    | NSMonthCalendarUnit
                                                    | NSYearCalendarUnit
                                                    | NSHourCalendarUnit
                                                    | NSMinuteCalendarUnit
                                                    | NSSecondCalendarUnit ) fromDate:date];
    
    [components setHour:23];
    [components setMinute:59];
    [components setSecond:59];
        
    return [cal dateFromComponents:components];
}

- (NSDate *)weekStart:(NSDate *)date
{
    NSCalendar *gregorian = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *weekdayComponents = [gregorian components:
                                           NSWeekdayCalendarUnit
                                           | NSHourCalendarUnit
                                           | NSMinuteCalendarUnit
                                           | NSSecondCalendarUnit fromDate:date];
    
    NSDateComponents *componentsToSubtract  = [[NSDateComponents alloc] init];
    [componentsToSubtract setDay: (0 - [weekdayComponents weekday]) + self.startDayForMonthCoef];
    [componentsToSubtract setHour: 0 - [weekdayComponents hour]];
    [componentsToSubtract setMinute: 0 - [weekdayComponents minute]];
    [componentsToSubtract setSecond: 0 - [weekdayComponents second]];
    NSDate *beginningOfWeek = [gregorian dateByAddingComponents:componentsToSubtract toDate:date options:0];
    return beginningOfWeek;
}

- (NSDate *)weekEnd:(NSDate *)date
{
    NSCalendar *gregorian = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *weekdayComponents = [gregorian components:
                                           NSWeekdayCalendarUnit
                                           | NSHourCalendarUnit
                                           | NSMinuteCalendarUnit
                                           | NSSecondCalendarUnit fromDate:date];
    NSDateComponents *componentsToSubtract  = [[NSDateComponents alloc] init];
    [componentsToSubtract setDay: (0 - [weekdayComponents weekday]) + self.startDayForMonthCoef];
    [componentsToSubtract setHour: 0 - [weekdayComponents hour]];
    [componentsToSubtract setMinute: 0 - [weekdayComponents minute]];
    [componentsToSubtract setSecond: 0 - [weekdayComponents second]];
    NSDate *beginningOfWeek = [gregorian dateByAddingComponents:componentsToSubtract toDate:date options:0];
    NSDateComponents *componentsToAdd = [gregorian components:NSDayCalendarUnit fromDate:beginningOfWeek];
    [componentsToAdd setDay:7];
    NSDate *endOfWeek = [gregorian dateByAddingComponents:componentsToAdd toDate:beginningOfWeek options:0];
    return endOfWeek;
}

- (NSDate *)monthStart:(NSDate *)date
{
    NSCalendar *gregorian = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *comp = [gregorian components:(NSYearCalendarUnit | NSMonthCalendarUnit | NSDayCalendarUnit) fromDate:date];
    [comp setDay:1];
    NSDate *firstDayOfMonthDate = [gregorian dateFromComponents:comp];
    return firstDayOfMonthDate;
}

- (NSDate *)monthEnd:(NSDate *)date
{
    NSCalendar* calendar = [NSCalendar currentCalendar];
    NSDateComponents* comps = [calendar components:
                               NSYearCalendarUnit|NSMonthCalendarUnit|NSWeekCalendarUnit|NSWeekdayCalendarUnit fromDate:date];
    comps = [calendar components:NSYearCalendarUnit|NSMonthCalendarUnit|NSWeekCalendarUnit|NSWeekdayCalendarUnit fromDate:date];
    [comps setMonth:[comps month] + 1];
    [comps setDay:1];
    NSDate *tDateMonth = [calendar dateFromComponents:comps];
    return tDateMonth;
}

- (NSDate *)getStartRange:(NSDate *)date
{
    switch (self.dateType) {
        case kDateTypeDay:
            return [self dayStart:date];
        case kDateTypeWeek:
            return [self weekStart:date];
        case kDateTypeMonth:
            return [self monthStart:date];
    }
}

- (NSDate *)getEndRange:(NSDate *)date
{
    switch (self.dateType) {
        case kDateTypeDay:
            return [self dayEnd:date];
        case kDateTypeWeek:
            return [self weekEnd:date];
        case kDateTypeMonth:
            return [self monthEnd:date];
    }
}

- (DateRange *)createRange:(NSUInteger)index
{
    DateRange *dateRange = [DateRange new];
    
    NSCalendar *calendar = [NSCalendar currentCalendar];
    NSDateComponents *comps = [NSDateComponents new];
    
    if (self.dateType == kDateTypeDay) {
       
        if (self.days.count > 0 && self.days.count + 1 > index && index > 0) {
            index = [self.days[index - 1] integerValue];
        } else if (index != 0) {
            index = [[self.days lastObject] integerValue] + index;
        }
    }
    
    switch (self.dateType) {
        case kDateTypeDay:
            dateRange.name = NSLocalizedString(@"Day", @"Statistics");
            comps.day = -index;
            break;
        case kDateTypeWeek:
            dateRange.name = NSLocalizedString(@"Week", @"Statistics");
            comps.week = -index;
            break;
        case kDateTypeMonth:
            dateRange.name = NSLocalizedString(@"Month", @"Statistics");
            comps.month = -index;
            break;
    }
    
    NSDate *date = [calendar dateByAddingComponents:comps toDate:[NSDate date] options:0];
    
    dateRange.startDate = [self getStartRange:date];
    dateRange.endDate = [self getEndRange:date];
    
    return dateRange;
}

#pragma mark - Calculate Earned score

+ (NSInteger)earnedScoreByCurrentPlayer
{
    return [[[DataUtils allTasksFromCurrentChild] valueForKeyPath:@"@sum.currentScore"] integerValue];
}

#pragma mark - Helper

- (NSString *)dataDescriptionWithTask:(NSArray *)tasks
{
    NSString *date = nil;
    NSDateFormatter* dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setLocale:[NSLocale currentLocale]];
    
    NSArray *sortedTasks = [tasks sortedArrayUsingComparator:^NSComparisonResult(id<AbstractAchievement> obj1,
                                                                                 id<AbstractAchievement> obj2) {
        return [obj1.lastChangeDate timeIntervalSince1970GMT] > [obj2.lastChangeDate timeIntervalSince1970GMT];
    }];
    
    NSString *dateComponents = @"yMMd";
    id<AbstractAchievement> task = nil;
    
    if (self.dateType == kDateTypeDay) {
        
        dateFormatter.dateFormat = [NSDateFormatter dateFormatFromTemplate:dateComponents options:0
                                                                    locale:dateFormatter.locale];
        task = (id<AbstractAchievement>)sortedTasks[0];
        
    } else if (self.dateType == kDateTypeWeek) {
        dateComponents = @"MMMd";
        dateFormatter.dateFormat = [NSDateFormatter dateFormatFromTemplate:dateComponents options:0
                                                                    locale:dateFormatter.locale];
        task = (id<AbstractAchievement>)[sortedTasks lastObject];
        
    } else if (self.dateType == kDateTypeMonth) {
        dateComponents = @"yMMM";
        dateFormatter.dateFormat = [NSDateFormatter dateFormatFromTemplate:dateComponents options:0
                                                                    locale:dateFormatter.locale];
       task = (id<AbstractAchievement>)sortedTasks[0];
    }
    
    date = [dateFormatter stringFromDate:task.lastChangeDate];
    
    return date;
}

- (NSMutableArray *)showedActionInGrafhFromTasks:(NSArray *)tasks withTaskStatus:(TaskStatus)status
{
    NSMutableArray *tempTasks = [NSMutableArray new];
    
    for (Task *task in tasks) {
        if ([task isKindOfClass:[Task class]]) {
            if (task.status == status && (status == kTaskStatusSolvedNotAll || status == kTaskStatusSolved)) {
                [tempTasks addObject:task];
            }
            if (status != kTaskStatusSolved) {
                tempTasks = [[DataUtils allErrorsForTasks:DataUtils.tasksFromCurrentChild withErrorType:self.actionErrorType] mutableCopy];
                break;
            }
        }
    }
    
    return tempTasks;
}
#pragma mark - Helper

- (NSArray *)arrayCountDays
{
    NSMutableArray *settingDateCount = [NSMutableArray new];
    
    if (self.tasks.count) {
        id<AbstractTask> firstTask = self.tasks[0];

        NSCalendar *cal = [NSCalendar currentCalendar];
        NSDate *currentDate = [NSDate date];
        
        __block NSInteger value = 0;
        
        [self.tasks enumerateObjectsUsingBlock:^(id<AbstractTask> task, NSUInteger idx, BOOL *stop) {
            
            NSCalendar *cal2 = [NSCalendar currentCalendar];
            NSDateComponents *componentsForTask = [cal2 components:NSDayCalendarUnit fromDate:task.lastChangeDate toDate:currentDate options:0];
            
            if (componentsForTask.day > 0 && value != componentsForTask.day) {
                NSNumber *countDays = @(componentsForTask.day);
                [settingDateCount addObject:countDays];
                value = componentsForTask.day;
            }
        }];
        
        NSDateComponents *componentsForData = [cal components:NSDayCalendarUnit fromDate:firstTask.lastChangeDate toDate:currentDate options:0];
        if(settingDateCount.count == 0 && componentsForData.day > 0) {
            NSNumber *countDays = @(componentsForData.day);
            [settingDateCount addObject:countDays];
        } else {
            NSSet *uniqueStates = [NSSet setWithArray:settingDateCount];
            settingDateCount = [[uniqueStates allObjects] mutableCopy];
            
            [settingDateCount sortUsingComparator:^NSComparisonResult(NSNumber *obj1, NSNumber* obj2) {
                return [obj1 integerValue] > [obj2 integerValue];
            }];
        }
    }
    
    return settingDateCount;
}

- (void)calculateStartDayForMonthCoef
{
    NSDateFormatter* dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setLocale:[NSLocale currentLocale]];
    NSString *dateComponents = @"yMMd";
    dateFormatter.dateFormat = [NSDateFormatter dateFormatFromTemplate:dateComponents options:0
                                                                locale:dateFormatter.locale];
    self.startDayForMonthCoef = 1;
    if ([dateFormatter.dateFormat isEqualToString:@"d.MM.y"]) {
        self.startDayForMonthCoef = 2;
    }
}

@end
