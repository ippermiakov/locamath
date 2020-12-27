//
//  OlympiadHint.h
//  Mathematic
//
//  Created by alexbutenko on 3/19/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

@class OlympiadAction;

@interface OlympiadHint : NSManagedObject

@property (nonatomic, retain) NSNumber * identifier;
@property (nonatomic, retain) NSNumber * hasUserInput;
@property (nonatomic, retain) NSString * hintString;
@property (nonatomic, retain) NSString * userInput;
@property (nonatomic, retain) NSString * baseUserInput;
@property (nonatomic, retain) OlympiadAction *action;

- (void)updateUserInputIfNeeded;

@end
