//
//  MTMovableView.h
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

extern NSString * const kNotificationPutComponent;
extern NSString * const kNotificationChooseComponent;
extern NSString * const kNotificationReloadComponents;

@class MTMovableView;
@class MTToolsView;

typedef void (^MovableBlockType)(MTMovableView *);

@interface MTMovableView : UIView <NSCopying, UIGestureRecognizerDelegate>

@property (nonatomic, copy) MovableBlockType onMoveBlock;
@property (nonatomic, copy) MovableBlockType onMoveEndedBlock;
@property (nonatomic, copy) MovableBlockType onMoveCompletionBlock;
@property (nonatomic, copy) MovableBlockType onMoveBeganBlock;

@property (unsafe_unretained, nonatomic) CGSize hitAreaEnlarge;

@property (nonatomic, readwrite) NSString *text;
@property (strong, nonatomic) UIView *carriedView;
@property (unsafe_unretained, nonatomic) BOOL isReturnOnMoveEnd; //NO by default
@property (unsafe_unretained, nonatomic) BOOL isOnMove;
@property (unsafe_unretained, nonatomic) BOOL isMoveEnabled;
@property (unsafe_unretained, nonatomic) BOOL isMovedToAnotherParent;
@property (weak, nonatomic) MTToolsView *toolsView;
@property (weak, nonatomic) UIView *overlayView;
@property (unsafe_unretained, nonatomic) CGPoint initialCenter;

@property (strong, nonatomic) id linkedData;

@end
