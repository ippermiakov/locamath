//
//  SubActionViewDelegate.h
//  Mathematic
//
//  Created by Developer on 19.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol SubActionViewDelegate <NSObject>
@required

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex;

- (void)addAnswerComponent:(NSString *)component;

- (void)deleteSubActionViewAtIndex:(NSInteger)index;

- (void)didChangeComponent:(NSString *)component forSubActionWithIndex:(NSInteger)subActionIndex;

- (void)didChangeAnswerComponent:(NSString *)component;

@end
