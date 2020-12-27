//
//  OlympiadAction.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 01.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadAction.h"
#import "OlympiadHint.h"
#import "OlympiadTask.h"
#import "OlympiadHint.h"

#define STRING_NIL(str)(str?str:@"")

@implementation OlympiadAction

@dynamic answers;
@dynamic identifier;
@dynamic isCorrect;
@dynamic numOfToolsToFill;
@dynamic hints;
@dynamic task;

- (void)updateIsCorrect
{
    NSArray *hints = [self.hints.allObjects sortedArrayUsingComparator:^NSComparisonResult(OlympiadHint *hint1, OlympiadHint *hint2) {
        return [hint1.identifier integerValue] > [hint2.identifier integerValue];
    }];
    
//    NSLog(@"hints: %@", hints);
    
    NSMutableString *answer = [NSMutableString new];
    
    for (NSInteger i = 0, end = self.hints.count; i < end; ++i) {
        NSString *resultHintString = [NSString stringWithFormat:@"%@%@", STRING_NIL([hints[i] hintString]), STRING_NIL([hints[i] userInput])];

        if ([self shouldInverseAnswerOrder]) {
            answer = [[resultHintString stringByAppendingString:answer] mutableCopy];
        } else {
            [answer appendString:resultHintString];
        }
    }
    
//    NSLog(@"answer: %@", [answer ]);
    
    self.isCorrect = @NO;
    
    for (NSString *etalonAnswer in self.answers) {
        if ([etalonAnswer isEqualToString:answer]) {
            self.isCorrect = @YES;
            return;
        }
    }
}

- (BOOL)shouldInverseAnswerOrder
{
    NSMutableCharacterSet *charactersSet = [[NSCharacterSet decimalDigitCharacterSet] mutableCopy];
    [charactersSet addCharactersInString:@"+-"];
    
    BOOL toolsContainsNumbersOrSigns = [self.task.tools any:^BOOL(NSString *tool) {
        return [tool rangeOfCharacterFromSet:charactersSet].length > 0;
    }];
    
    BOOL shouldInverse = [DataUtils isArabicLocale] && !toolsContainsNumbersOrSigns;
    
    return shouldInverse;
}

- (NSNumber *)isFilled
{
    for (OlympiadHint *hint in self.hints) {
        if ([hint.hasUserInput boolValue]) {
            if ([hint.userInput length] == 0) {
                return @NO;
            }
        }
    }
    
    return @YES;
}

@end




