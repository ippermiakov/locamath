//
//  HelpPage.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 23.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

typedef enum {
    PageTypeAnimation,
    PageTypeStatic
} PageType;

@class Child;

@interface HelpPage : NSManagedObject

@property (nonatomic, retain) NSNumber * pageNum;
@property (nonatomic, retain) NSNumber * pageType;
@property (nonatomic, retain) NSString * identifier;
@property (nonatomic, retain) NSString * girlPhrase;
@property (nonatomic, retain) NSString * boyPhrase;
@property (nonatomic, retain) NSString * boardText;
@property (nonatomic, retain) NSString * animation;
@property (nonatomic, retain) id exampleImages;
@property (nonatomic, retain) Child *child;

@end
