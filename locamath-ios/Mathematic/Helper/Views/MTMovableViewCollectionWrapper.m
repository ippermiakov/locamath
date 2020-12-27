//
//  MTMovableViewCollectionWrapper.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 29.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTMovableViewCollectionWrapper.h"
#import "MTMovableView.h"
#import "UILabel+Mathematic.h"
#import "OlympiadTask.h"

static double const kRefreshDelay = 0.1;

@implementation MTMovableViewCollectionWrapper

- (void)dealloc
{
//    NSLog(@"clean collection wrapper for task #%@ for level %@", self.task.identifier, [self.task.level identifier]);
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

- (void)initialize
{
    self.movableCollection = [[MTMovableViewCollection alloc] initWithFrame:self.bounds];
    self.movableCollection.dataSource = self;
    self.movableCollection.isRearrangingItemsOnMove = YES;
    self.movableCollection.itemsOffsetY = 0.0f;
    self.movableCollection.itemsSpacing = 0;
    
    [self addSubview:self.movableCollection];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(putComponent:) name:kNotificationPutComponent object:nil];
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:(NSCoder *)aDecoder];
    if (self) {
        [self initialize];
    }
    return self;
}

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        [self initialize];        
    }
    return self;
}

- (void)setFrame:(CGRect)frame
{
    [super setFrame:frame];
    
    self.movableCollection.frame = self.bounds;
}

- (void)sizeToFit
{
    if (self.movableCollection.components.count != 0) {
        MTMovableView *rightestView = [self.movableCollection.components lastObject];
                
        CGRect newCollectionFrame = self.movableCollection.frame;
                
        newCollectionFrame.size.width = rightestView.frame.size.width;
        
        self.movableCollection.frame = newCollectionFrame;
        self.movableCollection.scrollCanvas.frame = self.movableCollection.bounds;
        
        CGRect newSelfFrame = self.frame;

        newSelfFrame.size = newCollectionFrame.size;
        newSelfFrame.size.height = MAX(newSelfFrame.size.height, 20);
        newSelfFrame.size.width  = MAX(newSelfFrame.size.width,  20);
        
        self.frame = newSelfFrame;
    }
}

#pragma mark Putting movable view

- (void)putComponent:(NSNotification *)notification
{
//    NSLog(@"received by task #%@ from level: %@", self.task.identifier, [self.task.level identifier]);
    
    MTMovableView *movableView = [notification object];
    
    CGPoint convertedPoint = CGPointZero;
    CGRect convertedSelfRect = self.frame;
    
    if (movableView.superview) {
        convertedPoint = [movableView.superview convertPoint:movableView.center toView:self.superview];
    } else {
        convertedPoint = movableView.center;
        convertedSelfRect = [movableView.overlayView convertRect:self.frame fromView:self.superview];
    }
    
//    NSLog(@"ON CHECK!!! converted point: %@ self.frame: %@", NSStringFromCGPoint(convertedPoint), NSStringFromCGRect(convertedSelfRect));
    
    if (CGRectContainsPoint(convertedSelfRect, convertedPoint)) {
        
//        NSLog(@"CONTAINS!!! converted point: %@ self.frame: %@", NSStringFromCGPoint(convertedPoint), NSStringFromCGRect(convertedSelfRect));
        
        movableView.isMovedToAnotherParent = YES;

        MTMovableView *currentMovableView = nil;
        
        //prepare to exchange views between collections
        if ([self.task.isOneToolToOneAnswerMapping boolValue]) {
            NSDictionary *userInfo = [notification userInfo];
            
            if (userInfo) {
                currentMovableView = [self.movableCollection.components lastObject];
                CGPoint centerPointInMovableCollectionToInsert = [userInfo[kNotificationInfoInitialCenterOfMovableView] CGPointValue];
//                NSLog(@"centerPointInMovableCollectionToInsert: %@", NSStringFromCGPoint(centerPointInMovableCollectionToInsert));
                //pass coordinates from notification
                currentMovableView.center = centerPointInMovableCollectionToInsert;
            }
        }
        
        if ([self.text isEqualToString:self.placeholder] ||
            ([self.delegate respondsToSelector:@selector(insertionTypeOfCollectionWrapper:)] &&
             [self.delegate insertionTypeOfCollectionWrapper:self] == InsertionTypeReplace)) {
            self.text = movableView.text;
        } else {
            self.text = [self.text stringByAppendingString:movableView.text];
        }
        
        if (self.text.length > self.numberToDisplay) {
            self.text = movableView.text;
            [self sizeToFit];
        }
                
        [self.delegate collectionWrapper:self didChangeTextToNew:self.text];
        
        if ([self.task.isOneToolToOneAnswerMapping boolValue]) {
            [movableView removeFromSuperview];
            
            if (currentMovableView) {
                //exchange views between collections
                [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationRemovedComponent
                                                                    object:currentMovableView];
            }
        }
    }
}

#pragma mark MTMovableViewDatasource

- (NSUInteger)numberOfRowsInCollection:(MTMovableViewCollection *)collection
{
    return 1;
}

- (MTMovableView *)collection:(MTMovableViewCollection *)collection viewAtIndex:(NSUInteger)index
{
    UILabel *charLabel = [UILabel olympiadTasksLabelWithText:[self.text isEqualToString:@""] ? self.placeholder : self.text
                                                 placeholder:self.placeholder
                                               withAlignment:self.task.alignmentType];

    MTMovableView *retMovableView = [[MTMovableView alloc] initWithFrame:charLabel.frame];
    
    if ([charLabel.text isEqualToString:self.placeholder] || self.isTaskCompleted) {
        retMovableView.isMoveEnabled = NO;
    } else retMovableView.isMoveEnabled = YES;
    
    retMovableView.carriedView = charLabel;
    retMovableView.overlayView = [UIView overlayForOlympiads];
            
    return retMovableView;
}

- (void)collection:(MTMovableViewCollection *)collection didMoveView:(MTMovableView *)movableView toIndex:(NSUInteger)index
{    
    self.text = self.movableCollection.textRepresentation;
    [self.delegate collectionWrapper:self didChangeTextToNew:self.text];
}

- (void)collection:(MTMovableViewCollection *)collection removeView:(MTMovableView *)movableView atIndex:(NSUInteger)index
{
    self.text = @"";
    
    //let pickup removed view before parent update
    double delayInSeconds = kPickupOnRemoveDelay + kRefreshDelay;
    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
    dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
        [self.delegate collectionWrapper:self didChangeTextToNew:self.text];
    });
}

- (void)collection:(MTMovableViewCollection *)collection endMovingView:(MTMovableView *)movableView
{
    if (self.didEndMoveBlock) {
        self.didEndMoveBlock();
    }
}

- (void)collection:(MTMovableViewCollection *)collection didStartMovingView:(MTMovableView *)movableView
{
    if (self.didStartMoveBlock) {
        self.didStartMoveBlock();
    }
}

#pragma mark Setters & Getters

- (void)setText:(NSString *)text
{
    _text = text;
    
    [self sizeToFit];
    
    [self.movableCollection reloadData];
}

- (void)setTask:(OlympiadTask *)task
{
    _task = task;
    self.movableCollection.isOneToolToOneAnswerMapping = [task.isOneToolToOneAnswerMapping boolValue];
    
    if ([task.isOneToolToOneAnswerMapping boolValue]) {
        self.movableCollection.outOfBoundsDecisionType = OutOfBoundsDecisionTypeTypeBounds;

        [[NSNotificationCenter defaultCenter] removeObserver:self
                                                        name:kNotificationRemovedComponent
                                                      object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(putComponent:)
                                                     name:kNotificationRemovedComponent
                                                   object:nil];
    }
}

@end
