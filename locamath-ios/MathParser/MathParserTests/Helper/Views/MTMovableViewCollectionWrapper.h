//
//  MTMovableViewCollectionWrapper.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 29.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "MTMovableViewCollection.h"

typedef enum {
    InsertionTypeAdd,
    InsertionTypeReplace
} InsertionType;

typedef void(^WrapperDidEndMoveBlock)();
typedef void(^WrapperDidStartMoveBlock)();

@class MTMovableViewCollectionWrapper;
@class OlympiadTask;

@protocol MTMovableViewCollectionWrapperDelegate <NSObject>
- (void)collectionWrapper:(MTMovableViewCollectionWrapper *)wrapper didChangeTextToNew:(NSString *)text;
@optional
- (InsertionType)insertionTypeOfCollectionWrapper:(MTMovableViewCollectionWrapper *)wrapper;
@end

@interface MTMovableViewCollectionWrapper : UIView<MTMovableViewCollectionDataSource>

@property(nonatomic, strong) MTMovableViewCollection *movableCollection;
@property(nonatomic, strong) NSString *text;
@property(nonatomic, strong) NSString *placeholder;
@property (unsafe_unretained, nonatomic) CGRect rectForLabel;
@property (unsafe_unretained, nonatomic) BOOL isTaskCompleted;
@property (unsafe_unretained, nonatomic) NSInteger numberToDisplay;
@property (copy, nonatomic) WrapperDidEndMoveBlock didEndMoveBlock;
@property (copy, nonatomic) WrapperDidStartMoveBlock didStartMoveBlock;
@property(nonatomic, weak)   id<MTMovableViewCollectionWrapperDelegate> delegate;
@property (strong, nonatomic) OlympiadTask *task;

@end
