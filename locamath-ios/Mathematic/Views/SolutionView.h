//
//  SolutionView.h
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ActionView.h"
#import "ActionViewDelegate.h"
#import "SolutionViewDataSource.h"
#import "SolutionViewDelegate.h"

@interface SolutionView : UIScrollView <ActionViewDelegate>

@property (weak, nonatomic, setter = setDataSource:)   id <SolutionViewDataSource> dataSource;
@property (weak, nonatomic)                            id <SolutionViewDelegate> delegateSolution;

- (void)reloadData;
- (void)reloadDataWithAnimation;
- (void)cleanView;

@end
