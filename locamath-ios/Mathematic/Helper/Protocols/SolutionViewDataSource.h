//
//  SolutionViewDelegate.h
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class ActionView;
@class Action;

@protocol SolutionViewDataSource <NSObject>
@required

- (NSArray *)fetchedActions;

- (ActionView *)createActionViewWithAction:(Action *)action;

@end
