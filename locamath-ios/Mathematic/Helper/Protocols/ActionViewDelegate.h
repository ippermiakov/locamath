//
//  ActionViewDelegate.h
//  Mathematic
//
//  Created by Developer on 18.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class Action;

@protocol ActionViewDelegate <NSObject>
@required

- (void)addSubActionToAction:(Action *)action;

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action;

- (void)addAnswerWithComponent:(NSString *)component forAction:(Action *)action;

- (void)deleteAction:(Action *)action;

- (void)deleteSubActionViewWithIndex:(NSInteger)subActionIndex forAction:(Action *)action;

- (void)didChangeComponent:(NSString *)component withSubActionIndex:(NSInteger)subActionIndex forAction:(Action *)action;

- (void)didChangeAnswerComponent:(NSString *)component forAction:(Action *)action;

@end
