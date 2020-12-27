//
//  MTMovableViewCollection.h
//  Mathematic
//
//  Created by alexbutenko on 2/15/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

extern NSString * const kNotificationRemovedComponent;
extern NSString * const kNotificationInfoInitialCenterOfMovableView;
extern double const kPickupOnRemoveDelay;

@class MTMovableView;
@class MTMovableViewCollection;

@protocol MTMovableViewCollectionDataSource <NSObject>

- (NSUInteger)numberOfRowsInCollection:(MTMovableViewCollection *)collection;
- (MTMovableView *)collection:(MTMovableViewCollection *)collection viewAtIndex:(NSUInteger)index;

@optional
- (void)collection:(MTMovableViewCollection *)collection didStartMovingView:(MTMovableView *)movableView;
- (void)collection:(MTMovableViewCollection *)collection moveView:(MTMovableView *)movableView fromIndex:(NSUInteger)index toIndex:(NSUInteger)toIndex;
- (void)collection:(MTMovableViewCollection *)collection didMoveView:(MTMovableView *)movableView toIndex:(NSUInteger)index;
- (void)collection:(MTMovableViewCollection *)collection removeView:(MTMovableView *)movableView atIndex:(NSUInteger)index;
- (void)collection:(MTMovableViewCollection *)collection endMovingView:(MTMovableView *)movableView;

@end

typedef enum {
    OutOfBoundsDecisionTypeOffsetFromPivot,
    OutOfBoundsDecisionTypeTypeBounds
} OutOfBoundsDecisionType;


@interface MTMovableViewCollection : UIView <NSCopying>

@property (weak, nonatomic) id<MTMovableViewCollectionDataSource> dataSource;
@property (nonatomic, readonly) NSString *textRepresentation;
@property (unsafe_unretained, nonatomic) CGPoint focusXYBounds;
@property (unsafe_unretained, nonatomic) BOOL isRearrangingItemsOnMove; //NO by default
//OutOfBoundsDecisionTypeOffsetFromPivot by default
@property (nonatomic, strong, readonly) UIScrollView *scrollCanvas;
@property (unsafe_unretained, nonatomic) OutOfBoundsDecisionType outOfBoundsDecisionType;
@property (unsafe_unretained, nonatomic) NSUInteger itemsSpacing;
@property (unsafe_unretained, nonatomic) CGFloat itemsOffsetY;
@property (strong, nonatomic, readonly) NSMutableArray *components;
@property (unsafe_unretained, nonatomic) BOOL isOneToolToOneAnswerMapping;

- (void)reloadData;
- (NSUInteger)movableViewIndexAtPoint:(CGPoint)point;

@end
