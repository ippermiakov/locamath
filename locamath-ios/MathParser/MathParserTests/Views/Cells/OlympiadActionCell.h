//
//  OlympiadActionCell.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 27.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "MTMovableViewCollectionWrapper.h"
#import "OlympiadTask.h"

typedef void(^OlympiadActionCellScrollingEnablingBlock)();
typedef void(^OlympiadActionCellScrollingDisablingBlock)();
typedef void(^OlympiadActionCellDidReloadBlock)();

typedef enum {
    ActionCellEditing,
    ActionCellDisplaying
} OlympiadActionCellType;


@class MTMovableViewCollection;

@interface OlympiadActionCell : UITableViewCell<MTMovableViewCollectionWrapperDelegate>

@property(nonatomic, readonly) NSString *summaryText;
@property(nonatomic, readonly) NSArray *usersAnswers;
@property (strong, nonatomic) NSArray *hints;
@property (strong, nonatomic) OlympiadTask * task;

@property (copy, nonatomic) OlympiadActionCellScrollingDisablingBlock scrollingDisablingBlock;
@property (copy, nonatomic) OlympiadActionCellScrollingEnablingBlock scrollingEnablingBlock;
@property (copy, nonatomic) OlympiadActionCellDidReloadBlock didReloadBlock;

@property(nonatomic, unsafe_unretained) OlympiadActionCellType cellType;

@end
