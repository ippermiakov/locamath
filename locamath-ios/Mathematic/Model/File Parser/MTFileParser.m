//
//  SBFileParser.m
//  Mathematic
//
//  Created by Alexander on 10/31/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "MTFileParser.h"

#import "Task.h"
#import "Level.h"
#import "OlympiadTask.h"
#import "OlympiadAction.h"
#import "OlympiadHint.h"
#import "OlympiadLevel.h"
#import "Child.h"
#import "DataUtils.h"
#import "HelpPage.h"
#import "LevelsPath.h"
#import "Scheme.h"
#import "SchemeElement.h"
#import "MTHTTPClient.h"
#import "AccountMail.h"
#import "Parent.h"

static NSString * const kHintsSeparatorString = @"...";

@interface MTFileParser ()
{
    dispatch_queue_t _backgroundQueue;
}
@end

@implementation MTFileParser

- (id)init
{
    self = [super init];
    if (self) {
         _backgroundQueue = dispatch_queue_create("parser.mathematic", DISPATCH_QUEUE_SERIAL);
    }
    return self;
}

+ (MTFileParser*)sharedInstance
{
    static dispatch_once_t pred;
    static MTFileParser *sharedInstance = nil;
    
    dispatch_once(&pred, ^{
        sharedInstance = [[self alloc] init];
    });
    
    return sharedInstance;
}

#pragma mark - Main Parse Methods

- (void)parseFilesToCoreDataForChild:(Child *)child
                             success:(MTFileParserCompletionBlock)successBlock
                             failure:(MTFileParserFailureBlock)failureBlock
{
    dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
        if (![child.isDataLoaded boolValue]) {
            
            NSNumber *levelNumber = @1;
            
            NSArray *requestsKey = @[@{@"object": @"level", @"isOlympiad": @NO},
                                     @{@"object": @"task", @"isOlympiad": @NO},
                                     @{@"object": @"help", @"isOlympiad": @NO},
                                     @{@"object": @"scheme", @"isOlympiad": @NO},
                                     @{@"object": @"level", @"isOlympiad": @YES},
                                     @{@"object": @"task", @"isOlympiad": @YES}];
            
            __block NSMutableArray *gameDataDictionaries = [NSMutableArray new];
            
            __block NSUInteger requestsToPerformLeft = [requestsKey count];
            
            void(^onFinish)(NSNumber *, NSString *, NSDictionary *) =
            ^(NSNumber *levelNumber, NSString *key, NSDictionary *dictionary) {
                
                NSMutableDictionary *dictionaryToChange = nil;
                
                if ([gameDataDictionaries count] < [levelNumber integerValue]) {
                    dictionaryToChange = [NSMutableDictionary new];
                    [gameDataDictionaries addObject:dictionaryToChange];
                } else {
                    dictionaryToChange = gameDataDictionaries[[levelNumber integerValue] - 1];
                }
                
                dictionaryToChange[key] = dictionary;
                
//                NSLog(@"gameDataDictionaries: %@", gameDataDictionaries);
                
                //when all data is loaded parse it to model by updating existing model if needed
                if (requestsToPerformLeft == 0) {
                   
                   [MTFileParser sharedInstance].currentChild = [child inThreadContext];
                    
                    for (NSUInteger levelIndex = 0; levelIndex < [levelNumber integerValue]; levelIndex++) {
                        
                        [self parseLevelsFromDictionary:gameDataDictionaries[levelIndex][@"level"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"level"] fileName:@"Level_1"];
                        
                        [self parseTasksFromDictionary:gameDataDictionaries[levelIndex][@"task"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"task"] fileName:@"1st240"];
                        
                        [self parseOlympiadLevelsFromDictionary:gameDataDictionaries[levelIndex][@"level_olympiad"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"level_olympiad"] fileName:@"OlympiadLevels"];
                        
                        [self parseOlympiadTasksFromDictionary:gameDataDictionaries[levelIndex][@"task_olympiad"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"task_olympiad"] fileName:@"olympiad"];
                        
                        [self parseHelpsFromDictionary:gameDataDictionaries[levelIndex][@"help"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"help"] fileName:@"Help"];
                        
                        [self parseSchemeFromDictionary:gameDataDictionaries[levelIndex][@"scheme"]];
                        [self updateDataFileWithArray:gameDataDictionaries[levelIndex][@"scheme"] fileName:@"training_schemes"];
                        
                        [MTFileParser sharedInstance].currentChild.previousModificationJSONTimeInterval =
                        [MTFileParser sharedInstance].currentChild.modificationJSONTimeInterval;
                    }
                    
                    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];

                    if (successBlock) {
                        dispatch_async(dispatch_get_main_queue(), ^{
                            successBlock();
                        });
                    }
                }
            };
            
            
            [requestsKey each:^(NSDictionary *dictionary) {
                [[MTHTTPClient sharedMTHTTPClient] getGameDataWithObject:dictionary[@"object"]
                                                             levelNumber:levelNumber
                                                              isOlympiad:dictionary[@"isOlympiad"]
                                                                 success:^(NSDictionary *successResponseData) {
                                                                     requestsToPerformLeft--;
                                                                     
                                                                     NSMutableString *key = [dictionary[@"object"] mutableCopy];
                                                                     
                                                                     if ([dictionary[@"isOlympiad"] boolValue]) {
                                                                         [key appendFormat:@"_olympiad"];
                                                                     }
                                                                     
                                                                     onFinish(levelNumber, key, successResponseData);
                                                                 }
                                                                 failure:^(BOOL finished, NSError *error) {
                                                                     NSLog(@"getting %@ for level %@ isOlympiad %@ with error: %@", dictionary[@"object"], levelNumber, dictionary[@"isOlympiad"], error);
                                                                     
                                                                     if (failureBlock) {
                                                                         dispatch_async(dispatch_get_main_queue(), ^{
                                                                             failureBlock(error);
                                                                         });
                                                                     }
                                                                 }];
            }];
        } else {
            if (successBlock) {
                dispatch_async(dispatch_get_main_queue(), ^{
                    successBlock();
                });
            }
        }
    });
}

- (void)parseLocalFilesToCoreDataForChild:(Child *)child completion:(MTFileParserCompletionBlock)completionBlock
{
#ifndef UNIT_TESTS
    dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
#endif
        if (![child.isDataLoaded boolValue]) {
                      
            [MTFileParser sharedInstance].currentChild = [child inThreadContext];
            
            [self parseLevelsFromFile:@"Level_1"];
            [self parseTasksFromFile:@"1st240"];
            
            [self parseOlympiadLevelsFromFile:@"OlympiadLevels"];
            [self parseOlympiadTasksFromFile:@"olympiad"];
            
            [self parseHelpsFromFile:@"Help"];
            [self parseSchemeFromFile:@"training_schemes"];
            
            [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
        }
        
        if (completionBlock) {
            dispatch_async(dispatch_get_main_queue(), ^{
                completionBlock();
            });
        }
#ifndef UNIT_TESTS
    });
#endif
}

- (void)updateDataFileWithArray:(NSDictionary *)dictGameData fileName:(NSString *)fileName
{
    dispatch_async(_backgroundQueue, ^{
        NSFileManager *manager = [NSFileManager defaultManager];
        NSString *path = [[NSBundle mainBundle] pathForResource:fileName ofType:@"txt"];
        
        //NSLog(@"file path :%@",path);
        
        NSData *jsonData = [NSJSONSerialization dataWithJSONObject:dictGameData
                                                           options:NSJSONWritingPrettyPrinted
                                                             error:nil];
        NSString *dateStr = [[NSString alloc] initWithData:jsonData encoding:NSUTF8StringEncoding];
        if ([manager fileExistsAtPath:path]) {
            
            //NSLog(@"dateStr1 :%@", [NSString stringWithContentsOfFile:path encoding:NSUTF8StringEncoding error:nil]);
            
            [dateStr writeToFile:path atomically:YES encoding:NSUTF8StringEncoding error:nil];
            
            //NSLog(@"dateStr2 :%@", [NSString stringWithContentsOfFile:path encoding:NSUTF8StringEncoding error:nil]);
        }
    });
}

- (void)parseHelpsFromFile:(NSString *)filename
{
    NSDictionary *levels = [self parseJSONfromFileToDictionary:filename];

    [self parseHelpsFromDictionary:levels];
}

- (void)parseHelpsFromDictionary:(NSDictionary *)levels
{
    for (NSString *level in levels) {
        
        NSSet *helpPagesForLevel = [[[MTFileParser sharedInstance] currentChild].helpPages select:^BOOL(HelpPage *help) {
            return [help.identifier isEqualToString:level];
        }];
        
        NSArray *sortedHelps = [[helpPagesForLevel allObjects] sortedArrayUsingComparator:^NSComparisonResult(HelpPage *obj1, HelpPage *obj2) {
            return [obj1.pageNum integerValue] > [obj2.pageNum integerValue];
        }];
        
        NSArray *pages = [levels objectForKey:level];
        
        for (NSInteger i = 0, end = pages.count; i < end; ++i) {
            NSDictionary *page = pages[i];
            
            HelpPage *helpPage = nil;
            
            if ([sortedHelps count] > i) {
                helpPage = sortedHelps[i];
            } else {
                helpPage = [HelpPage createEntity];
                helpPage.pageNum    = @(i);
                helpPage.identifier = level;
            }
            
            helpPage.pageType   = @([[page objectForKey:@"girlPhrase"] isEqualToString:@"animation"] ? PageTypeAnimation: PageTypeStatic);
            helpPage.girlPhrase = [page objectForKey:@"girlPhrase"];
            helpPage.boyPhrase  = [page objectForKey:@"boyPhrase"];
            helpPage.boardText  = [page objectForKey:@"board"];
            helpPage.animation  = [page objectForKey:@"animation"];
            helpPage.exampleImages = [page objectForKey:@"exampleImages"];
            helpPage.child = [[MTFileParser sharedInstance] currentChild];
        }
    }
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)parseLevelsFromFile:(NSString *)filename
{
    NSDictionary *jsonObjects = [self parseJSONfromFileToDictionary:filename];

    [self parseLevelsFromDictionary:jsonObjects];
}

- (void)parseLevelsFromDictionary:(NSDictionary *)jsonObjects
{
    /*
     "paths" : [
     {
         "color" : "Red",
         "levels": {
             "1-A-1": {
             "Name": "High Bank",
             "Image": "exercises_page_high_bank",
             "PointX": 1104,
             "PointY": 423
         },
         ...
     } ]
     ....
    */
    static NSString * const kPathsKey = @"paths";
    static NSString * const kPathNameKey = @"name";
    static NSString * const kPathColorKey = @"color";
    static NSString * const kPathOlympiadLocalText = @"olympiad_localized_text";
    static NSString * const kPathErrorsKey = @"errors";
    static NSString * const kPathLevelNumberKey = @"levelNumber";
    static NSString * const kLevelsKey = @"levels";
    static NSString * const kLevelNameKey = @"Name";
    static NSString * const kLevelImageKey = @"Image";
    static NSString * const kLevelPointXKey = @"PointX";
    static NSString * const kLevelPointYKey = @"PointY";
    static NSString * const kLevelIsTestKey = @"isTest";
    
    NSArray *pathsArray = [jsonObjects objectForKey:kPathsKey];
    
    NSArray *childLevelsPath = [[self.currentChild.levelsPaths allObjects] sortedArrayUsingComparator:^NSComparisonResult(LevelsPath *obj1, LevelsPath *obj2) {
        return [obj1.identifier integerValue] > [obj2.identifier integerValue];
    }];
    
    [pathsArray enumerateObjectsUsingBlock:^(NSDictionary *pathDictionary, NSUInteger idx, BOOL *stop) {
        LevelsPath *path = nil;
        
        if ([childLevelsPath count] > idx) {
            path = childLevelsPath[idx];
        } else {
            path = [LevelsPath createEntity];
            path.identifier = @([self.currentChild.levelsPaths count] + 1);
            path.color = pathDictionary[kPathColorKey];
            path.levelNumber = pathDictionary[kPathLevelNumberKey];
        }

        path.name = pathDictionary[kPathNameKey];
        path.transitionErrors = pathDictionary[kPathErrorsKey];
        
        path.olympiadLocalText = pathDictionary[kPathOlympiadLocalText];
        
        path.child = [MTFileParser sharedInstance].currentChild;
        
        NSArray *keys = [pathDictionary[kLevelsKey] allKeys];
        
        for (NSString *key in keys) {
            
            Level *level = [path.levels match:^BOOL(Level *obj) {
                return [obj.identifier isEqualToString:key];
            }];
            
            if (!level) {
                level = [Level createEntity];
                level.identifier  = key;
                level.isTest = pathDictionary[kLevelsKey][key][kLevelIsTestKey];

                [path addLevelsObject:level];
            }
            
            level.child = [MTFileParser sharedInstance].currentChild;

            level.pointX = pathDictionary[kLevelsKey][key][kLevelPointXKey];
            level.pointY = pathDictionary[kLevelsKey][key][kLevelPointYKey];
            level.image  = pathDictionary[kLevelsKey][key][kLevelImageKey];
            level.name = pathDictionary[kLevelsKey][key][kLevelNameKey];
        }        
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)parseTasksFromFile:(NSString *)filename
{
    NSDictionary *jsonObjects = [self parseJSONfromFileToDictionary:filename];

    [self parseTasksFromDictionary:jsonObjects];
}

- (void)parseTasksFromDictionary:(NSDictionary *)jsonObjects
{
    NSArray *levels = [DataUtils levelsFromCurrentChild];
    
    NSArray *keys = [[jsonObjects objectForKey:@"tasks"] allKeys];
    
    for (NSString *key in keys) {
        Task *task = [self.currentChild.tasks match:^BOOL(Task *obj) {
            return [obj.identifier isEqualToString:key];
        }];
        
        if (!task) {
            task = [Task createEntity];
            task.identifier = key;
        }
        
        task.child = [MTFileParser sharedInstance].currentChild;
        
        task.animation   = jsonObjects[@"tasks"][key][@"Animation"];
        task.solutions   = jsonObjects[@"tasks"][key][@"Solutions"];
        task.formula     = jsonObjects[@"tasks"][key][@"Formula"];
        task.score       = jsonObjects[@"tasks"][key][@"Score"];
        task.expressions = jsonObjects[@"tasks"][key][@"Expressions"];
        task.answer      = jsonObjects[@"tasks"][key][@"Answer"];
        task.hint        = jsonObjects[@"tasks"][key][@"Hint"];
        task.objective   = jsonObjects[@"tasks"][key][@"Objective"];

        task.literal     = @([self isTaskLiteral:task]);

        if (task.literal) {
            task.letters = [[MTFileParser sharedInstance] getLetters:task];
        }
        
        Level *levelForTask = [levels match:^BOOL(Level *level) {
            return [task.identifier rangeOfString:level.identifier].location != NSNotFound;
        }];
        
        BOOL isLevelContainTask = [levelForTask.tasks any:^BOOL(Task *obj) {
            return [task.identifier isEqualToString:obj.identifier];
        }];
        
        if (!isLevelContainTask) {
            task.level = levelForTask;
            NSInteger levelScore = [levelForTask.levelScore integerValue];
            levelScore += [task.score integerValue];
            levelForTask.levelScore = @(levelScore);
            task.taskType = [self taskType:task];
        }
    }
    
    [levels each:^(Level *level) {
        
        __block NSInteger trainingIndex = 1;
        __block NSInteger commonIndex = 1;
        
        NSArray *tasks = [level sortedArrayOfTasks];
        
        [tasks each:^(Task *senderTask) {
            
             NSString *number = [[[[senderTask.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:1];

            if ([[[[[senderTask.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
                 isEqualToString:@"1"]) {
                senderTask.numberTask = @([number integerValue]);
                trainingIndex++;
            } else {
                senderTask.numberTask = @([number integerValue]);
                commonIndex++;
            }
        }];
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)parseOlympiadLevelsFromFile:(NSString *)filename
{
    NSDictionary *jsonObjects = [self parseJSONfromFileToDictionary:filename];

    [self parseOlympiadLevelsFromDictionary:jsonObjects];
}

- (void)parseOlympiadLevelsFromDictionary:(NSDictionary *)jsonObjects
{
    NSDictionary *levels = jsonObjects[@"levels"];
    
    [levels enumerateKeysAndObjectsUsingBlock:^(id key, id obj, BOOL *stop) {
        
        OlympiadLevel *level = [self.currentChild.olympiadLevels match:^BOOL(OlympiadLevel *level) {
            return [level.identifier isEqualToString:key];
        }];
        
        if (!level) {
            level = [OlympiadLevel createEntity];
            level.identifier = key;
        }
        
        level.child = self.currentChild;
        level.name = obj[@"Name"];
        level.image = obj[@"Image"];
    }];
        
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)parseOlympiadTasksFromFile:(NSString *)filename
{
    NSDictionary *jsonObjects = [self parseJSONfromFileToDictionary:filename];

    [self parseOlympiadTasksFromDictionary:jsonObjects];
}

- (void)parseOlympiadTasksFromDictionary:(NSDictionary *)jsonObjects
{
    /*
         "Points" : "800",
         "Objective": "Три котенка – Касьянка, Том и Плут –съели плотвичку, окуня и карася. Том не ел плотвичку. Каська не ел ни плотвичку,ни окуня. Какую рыбку съел каждый котенок ?",
         "Tools" : ["плотвичку", "окуня", "карася"],
         "Hint" : ["Касьянка - ...", "Том - ...", "Плут - ..."],
         "Answers": [ ["Касьянка - карася"], ["Том - окуня"], ["Плут - плотвичку"] ]
     
     
         "Points" : "800",
         "Objective": "Вставь пропущенные знаки действий  + или –",
         "Tools" : ["+", "-"],
         "Hint" : ["а  5...4...3...2...1=3", "б 5...4...3...2...1=5"],
         "Answers": [ ["a 5+4-3-2-1=3", "a 5-4+3-2+1=3"], ["б 5+4-3+2+1=5", "б 5-4+3+2-1=5"] ]
    */
    
    NSArray *tasks = jsonObjects[@"tasks"];
    
    NSArray *childOlympiadTasks = [[self.currentChild.olympiadTasks allObjects]
                                   sortedArrayUsingComparator:^NSComparisonResult(OlympiadTask *obj1, OlympiadTask *obj2) {
                                       return [obj1.identifier integerValue] > [obj2.identifier integerValue];
    }];
        
    [tasks enumerateObjectsUsingBlock:^(NSDictionary *taskDictionary, NSUInteger idx, BOOL *stop) {

        OlympiadTask *task = nil;
        BOOL isExistingTask = NO;
        
        if ([childOlympiadTasks count] > idx) {
            task = childOlympiadTasks[idx];
            isExistingTask = YES;
        } else {
            task = [OlympiadTask createEntity];
            task.identifier = @(idx + 1);
        }
        
        task.child = self.currentChild;
        task.points    = @([taskDictionary[@"Points"] integerValue]);
        task.tools     = taskDictionary[@"Tools"];
        task.baseTools = taskDictionary[@"BaseTools"];
        task.solutionHint = taskDictionary[@"SolutionHint"];
        task.objective  = taskDictionary[@"Objective"];
        task.isAnyAnswerApplicable = @([taskDictionary[@"isAnyAnswerApplicable"] boolValue]);
        
        task.alignmentTypeNumber = @([taskDictionary[@"alignmentType"] integerValue]);
        
        if (!isExistingTask) {            
            task.level = [self.currentChild.olympiadLevels match:^BOOL(OlympiadLevel *obj) {
                return [obj.identifier isEqualToString:taskDictionary[@"Level"]];
            }];
        }
        
        NSArray *hints   = taskDictionary[@"Hint"];
        NSArray *answers = taskDictionary[@"Answers"];
        
        NSArray *sortedTaskActions = [[task.actions allObjects]
                                      sortedArrayUsingComparator:^NSComparisonResult(OlympiadAction *obj1, OlympiadAction *obj2) {
                                          return [obj1.identifier integerValue] > [obj2.identifier integerValue];
        }];
        
        [hints enumerateObjectsUsingBlock:^(NSString *hintString, NSUInteger idx, BOOL *stop) {
            
            OlympiadAction *action = nil;
            
            if ([sortedTaskActions count] > idx) {
                action = sortedTaskActions[idx];
            } else {
                action = [OlympiadAction createEntity];
                action.identifier = @(idx + 1);
                action.task = task;
            }
            
            action.numOfToolsToFill = @([taskDictionary[@"numOfToolsToFill"] integerValue]);
            action.answers = [NSArray new];
            
            NSScanner *hintScanner = [NSScanner localizedScannerWithString:hintString];
            [hintScanner setCharactersToBeSkipped:[NSCharacterSet new]];
         
            NSUInteger hintIndex = 0;
            
            if ([task.isAnyAnswerApplicable boolValue]) {
                for (id answer in answers) {
                    action.answers = [action.answers arrayByAddingObjectsFromArray:answer];
                }
            } else {
                action.answers = answers[idx];
            }
            
            NSArray *sortedHints = [[action.hints allObjects]
                                    sortedArrayUsingComparator:^NSComparisonResult(OlympiadHint *obj1, OlympiadHint *obj2) {
                                        return [obj1.identifier integerValue] > [obj2.identifier integerValue];
            }];
            
            while (![hintScanner isAtEnd]) {
                NSString *scannedHint = nil;
                
                [hintScanner scanUpToString:kHintsSeparatorString intoString:&scannedHint];
                
                OlympiadHint *olympiadHint = nil;
                
                if ([sortedHints count] > hintIndex) {
                    olympiadHint = sortedHints[hintIndex];
                    [olympiadHint updateUserInputIfNeeded];
                } else {
                    olympiadHint = [OlympiadHint createEntity];
                    olympiadHint.identifier = @(hintIndex + 1);
                    olympiadHint.action = action;
                }
                
                olympiadHint.hintString = scannedHint;
                
//                NSLog(@"scanned hint: %@, scan location : %i", scannedHint, [scanner scanLocation]);
                
                if (![hintScanner isAtEnd]) {
                    
                    if (scannedHint == nil) {
                        olympiadHint.hasUserInput = @YES;
                    } else olympiadHint.hasUserInput = @NO;
                    
                    if (scannedHint) {
                        hintIndex++;

                        OlympiadHint *olympiadHint2 = nil;
                        
                        if ([sortedHints count] > hintIndex) {
                            olympiadHint2 = sortedHints[hintIndex];
                            [olympiadHint2 updateUserInputIfNeeded];
                        } else {
                            olympiadHint2 = [OlympiadHint createEntity];
                            olympiadHint2.identifier = @(hintIndex + 1);
                            olympiadHint2.action = action;
                        }
                        
                        olympiadHint2.hintString = nil;
                        olympiadHint2.hasUserInput = @YES;
                    }

                    [hintScanner setScanLocation:[hintScanner scanLocation] + [kHintsSeparatorString length]];
                }
                
                hintIndex++;
            }
        }];
    }];
    
    [self.currentChild.olympiadLevels each:^(OlympiadLevel *senderLevel) {
        
        //set number task
        [senderLevel.sortedArrayOfTasks enumerateObjectsUsingBlock:^(OlympiadTask *obj, NSUInteger idx, BOOL *stop) {
            obj.numberTask = @(idx + 1);
        }];
    }];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)parseSchemeFromFile:(NSString *)filename
{
    NSDictionary *schemesDict = [self parseJSONfromFileToDictionary:filename];
    
    [self parseSchemeFromDictionary:schemesDict];
}

- (void)parseSchemeFromDictionary:(NSDictionary *)schemesDict
{
    NSArray *schemes = schemesDict[@"training_schemes"];
    
    [schemes each:^(NSDictionary *schemeDict) {
        
        Scheme *scheme = [self.currentChild.schemes match:^BOOL(Scheme *scheme) {
            return [scheme.identifier isEqualToString:schemeDict[@"task_id"]];
        }];
        
        if (!scheme) {
            scheme = [Scheme createEntity];
            scheme.identifier = schemeDict[@"task_id"];
        }
        
        scheme.child  = self.currentChild;
        
        NSArray *elements = schemeDict[@"elements"];
        
        NSArray *sortedSchemeElements = [[scheme.elements allObjects]
                                         sortedArrayUsingComparator:^NSComparisonResult(SchemeElement *obj1, SchemeElement *obj2) {
                                             return [obj1.identifier integerValue] > [obj2.identifier integerValue];
        }];
        
        [elements enumerateObjectsUsingBlock:^(NSDictionary *elementDict, NSUInteger idx, BOOL *stop) {
        
            SchemeElement *schemeElement = nil;
            
            if ([sortedSchemeElements count] > idx) {
                schemeElement = sortedSchemeElements[idx];
            } else {
                schemeElement = [SchemeElement createEntity];
                schemeElement.identifier = @(idx);
                schemeElement.scheme = scheme;
            }
            
            schemeElement.typeNumber = elementDict[@"type"];
            schemeElement.position_x = elementDict[@"position_x"];
            schemeElement.position_y = elementDict[@"position_y"];
        }];
    }];
}

#pragma mark - JSON Methods

- (NSDictionary *)parseJSONfromFileToDictionary:(NSString *)fileName
{
    NSError *error = nil;
    
    NSString *fullPath = [[NSBundle bundleForClass:[self class]] pathForResource:fileName
                                                                          ofType:@"txt"];
    
    NSData *jsonData = [NSData dataWithContentsOfURL:[NSURL fileURLWithPath:fullPath]];
    
    NSDictionary *jsonObjects = [NSJSONSerialization JSONObjectWithData:jsonData options:0 error:&error];
    
    if (error) {
        NSLog(@"error is %@", [error localizedDescription]);
        return nil;
    }
    
    return jsonObjects;
}


#pragma mark - Helper Mehtods

- (BOOL)isTaskLiteral:(Task *)task
{
    for (NSString *str in task.expressions) {
        NSCharacterSet *lowerCaseChars = [NSCharacterSet characterSetWithCharactersInString:@"abcdefghijklmnopqrstuvwxyz"];
        NSCharacterSet *upperCaseChars = [NSCharacterSet characterSetWithCharactersInString:@"ABCDEFGHIJKLKMNOPQRSTUVWXYZ"];
        //NSCharacterSet *numbers = [NSCharacterSet characterSetWithCharactersInString:@"0123456789"];
        if ([str rangeOfCharacterFromSet:lowerCaseChars].location != NSNotFound || [str rangeOfCharacterFromSet:upperCaseChars].location != NSNotFound) {
            return YES;
        }
    }
    return NO;
}

- (NSMutableArray *)getLetters:(Task *)task
{
    NSMutableArray *letters = [[NSMutableArray alloc] init];
    
    for (NSString *str in task.expressions) {
        NSCharacterSet *lowerCaseChars = [NSCharacterSet characterSetWithCharactersInString:@"abcdefghijklmnopqrstuvwxyz"];
        NSCharacterSet *upperCaseChars = [NSCharacterSet characterSetWithCharactersInString:@"ABCDEFGHIJKLKMNOPQRSTUVWXYZ"];
        
        for (NSInteger i = 0; i < [str length]; i++) {
            NSString *subStr = [str substringWithRange:NSMakeRange(i, 1)];
            if ([subStr rangeOfCharacterFromSet:lowerCaseChars].location != NSNotFound || [subStr rangeOfCharacterFromSet:upperCaseChars].location != NSNotFound) {
                if (![letters containsObject:subStr]) {
                    [letters addObject:subStr];
                }
            }
        }
    }
    return letters;
}


- (NSNumber *)taskType:(Task *)task
{
    if ([[(Level *)task.level isTest] boolValue]) {
        return @(kTaskTypeTest);
    }
    
    if ([[[[[task.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
         isEqualToString:@"1"]) {
        return @(kTaskTypeTraining);
    }
    
    return @(kTaskTypeCommon);
}

@end