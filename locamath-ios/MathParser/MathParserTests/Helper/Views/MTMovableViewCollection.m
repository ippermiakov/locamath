//
//  MTMovableViewCollection.m
//  Mathematic
//
//  Created by alexbutenko on 2/15/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTMovableViewCollection.h"
#import "MTMovableView.h"
#import "DistanceCalculation.h"

#define DEFAULT_ITEMS_OFFSET_Y 10.0f
#define DEFAULT_ITEMS_SPACING 8.0f

NSString * const kNotificationRemovedComponent      = @"kNotificationRemovedComponent";
NSString * const kNotificationInfoInitialCenterOfMovableView = @"kNotificationInfoInitialCenterOfMovableView";
double const kPickupOnRemoveDelay = 0.3;

//value to decide whether view is moved out of focus... so we can remove it
static const CGPoint kDefaultFocusXYBounds = (CGPoint){200.0f, 50.0f};

@interface MTMovableViewCollection ()

@property (unsafe_unretained, nonatomic) CGPoint pivotPoint;
@property (strong, nonatomic, readwrite) UIScrollView *scrollCanvas;
@property (strong, nonatomic, readwrite) NSMutableArray *components;

@end

@implementation MTMovableViewCollection

- (void)initialize {
    self.itemsSpacing = DEFAULT_ITEMS_SPACING;
    self.itemsOffsetY = DEFAULT_ITEMS_OFFSET_Y;
    
    self.scrollCanvas = [[UIScrollView alloc] initWithFrame:self.bounds];
    
    [self addSubview:self.scrollCanvas];
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:aDecoder];
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

#pragma mark - Public

- (void)reloadData
{
    //reset content
    for (MTMovableView *view in self.components) {
        [view removeFromSuperview];
    }
    [self.components removeAllObjects];
    
    NSUInteger numberOfComponents = [self.dataSource numberOfRowsInCollection:self];
    self.components = [[NSMutableArray alloc] initWithCapacity:numberOfComponents];
    
    for (int i = 0; i < numberOfComponents; i++) {
        MTMovableView *movableView = [self.dataSource collection:self viewAtIndex:i];
        [self.components addObject:movableView];
        
        movableView.onMoveBeganBlock = ^(MTMovableView *touchedView) {
            self.pivotPoint = [self convertPoint:touchedView.frame.origin fromView:touchedView.superview];
            [self.scrollCanvas setScrollEnabled:NO];
            
            [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationChooseComponent object:touchedView];
        };
        
        if (self.isRearrangingItemsOnMove) {
            movableView.onMoveBlock = ^(MTMovableView *touchedView) {
                if ([self.dataSource respondsToSelector:@selector(collection:didStartMovingView:)] && !touchedView.isOnMove) {
                    [self.dataSource collection:self didStartMovingView:touchedView];
                }
                
                if (![self isOutOfFocusView:touchedView]) {
                    [self layoutSubviewsOnChangingPositionOfView:touchedView];
                }
            };
        }
        
        movableView.onMoveEndedBlock = ^(MTMovableView *touchedView) {
            if ([self.dataSource respondsToSelector:@selector(collection:endMovingView:)]) {
                [self.dataSource collection:self endMovingView:touchedView];
            }
            
            [self.scrollCanvas setScrollEnabled:YES];
            
            if (![self isOutOfFocusView:touchedView]) {
                [self snapComponent:touchedView];
//                [self reloadData];
            } else {
                [self removeMovableView:touchedView];
                
                if (self.isOneToolToOneAnswerMapping) {
                    //convert to frame window coordinate system to allow proper picking up by other siblings
                    CGRect touchedViewConvertedRect = [touchedView.overlayView convertRect:touchedView.frame fromView:self];
                    touchedView.frame = touchedViewConvertedRect;
                    touchedView.isMovedToAnotherParent = NO;
                    
                    CGPoint initialTouchedViewConvertedCenter = [touchedView.overlayView convertPoint:touchedView.initialCenter
                                                                                             fromView:self];
                    
                    NSDictionary *userInfo = @{kNotificationInfoInitialCenterOfMovableView:
                                              [NSValue valueWithCGPoint:initialTouchedViewConvertedCenter]};
                    //allow other view to pick up touched view
                    [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationRemovedComponent
                                                                        object:touchedView
                                                                      userInfo:userInfo];
                    
                    //check if somebody picked touched view
                    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(kPickupOnRemoveDelay * NSEC_PER_SEC));
                    dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
                        if (!touchedView.isMovedToAnotherParent && self.dataSource) {
                            [self.components makeObjectsPerformSelector:@selector(removeFromSuperview)];
                            [self.components removeAllObjects];
                            [self addMovableView:touchedView];
                        }
                    });
                }
            }
        };
        
        [self.scrollCanvas addSubview:movableView];
    }
    
    [self updateLayout];
}

- (NSUInteger)movableViewIndexAtPoint:(CGPoint)point
{
    CGPoint movableViewCenterConvertedToMovableView = [self convertPoint:point fromView:self.superview];

    NSMutableArray *componentsSortedByDistance = [self.components mutableCopy];
    
    //sort all components by distance to point
    [componentsSortedByDistance sortUsingComparator:^NSComparisonResult(MTMovableView *obj1, MTMovableView *obj2) {
        CGFloat obj1SumDistance = CGPointGetDistance(movableViewCenterConvertedToMovableView, obj1.center);
        CGFloat obj2SumDistance = CGPointGetDistance(movableViewCenterConvertedToMovableView, obj2.center);
        
        NSComparisonResult result = 0;
        
        if (obj1SumDistance == obj2SumDistance) {
            result = NSOrderedSame;
        } else if (obj1SumDistance < obj2SumDistance) {
            result = NSOrderedAscending;
        } else {
            result = NSOrderedDescending;
        }
        
        return result;
    }];
    
    MTMovableView *nearestMovableView = nil;
    
    NSInteger result = 0;

    MTMovableView *lastComponent = [self.components lastObject];
    CGRect frameBoundedByLastComponent = (CGRect){self.frame.origin, CGRectGetMaxX(lastComponent.frame), self.frame.size.height};
    
    if (!CGRectContainsPoint(frameBoundedByLastComponent, point)) {
        result = [self.components count];
    } else if ([componentsSortedByDistance count]) {
        nearestMovableView = [componentsSortedByDistance objectAtIndex:0];
        result = [self.components indexOfObject:nearestMovableView];
    }

    return result;
}

#pragma mark - Private

- (BOOL)isOutOfFocusView:(MTMovableView *)movableView
{
    CGRect movableViewFrameConverted = [self convertRect:movableView.frame toView:[self superview]];
    
    if (CGRectContainsRect(self.frame, movableViewFrameConverted)) {
        return NO;
    }
    else if (self.outOfBoundsDecisionType == OutOfBoundsDecisionTypeTypeBounds) {
        return YES;
    }
    
    movableViewFrameConverted = [self convertRect:movableView.frame fromView:movableView.superview];
    
    NSInteger yOffset = abs(self.pivotPoint.y) - abs(movableViewFrameConverted.origin.y);
    NSInteger xOffset = abs(self.pivotPoint.x) - abs(movableViewFrameConverted.origin.x);
    
    NSInteger movedXDistance = xOffset;
    NSInteger movedYDistance = yOffset;
    
    if (yOffset < 0) {
        movedYDistance = abs(yOffset);
    }
    else if (self.pivotPoint.y < movableViewFrameConverted.origin.y) {
        movedYDistance = abs(yOffset - movableViewFrameConverted.size.height);
    }
    
    if (xOffset < 0) {
        movedXDistance = abs(xOffset);
    }
    else if (self.pivotPoint.x < movableViewFrameConverted.origin.x) {
        movedXDistance = abs(xOffset - movableViewFrameConverted.size.width);
    }
    
    //    NSLog(@"movedXDistance: %i movedYDistance: %i", movedXDistance, movedYDistance);
    return (movedXDistance >= self.focusXYBounds.x) || (movedYDistance >= self.focusXYBounds.y);
}

- (void)updateLayout
{
    CGFloat pos_x = 0;
    CGFloat maxX = 0;
    
    for (int i = 0, numberOfComponents = [self.components count]; i < numberOfComponents; i++) {
        MTMovableView *movableView = self.components[i];
        CGPoint origin = CGPointZero;
        
        if (!movableView.isOnMove) {
            origin = (CGPoint){pos_x, self.itemsOffsetY};
        }
        else {
            CGPoint convertedOffset = [self convertPoint:(CGPoint){pos_x, self.itemsOffsetY} toView:movableView.superview];
            
            origin = (CGPoint){convertedOffset.x + (movableView.frame.origin.x - convertedOffset.x), convertedOffset.y + (movableView.frame.origin.y - convertedOffset.y)};
        }
        
        movableView.frame = (CGRect){origin, movableView.frame.size};
        pos_x +=  self.itemsSpacing + movableView.frame.size.width;        
        
        if (!movableView.isOnMove && CGRectGetMaxX(movableView.frame) > maxX) {
            maxX = CGRectGetMaxX(movableView.frame);
        }
    }
    
    self.scrollCanvas.contentSize = CGSizeMake(maxX + self.frame.origin.x, self.frame.size.height);    
}

- (void)removeMovableView:(MTMovableView *)view
{
    if ([self.dataSource respondsToSelector:@selector(collection:removeView:atIndex:)]) {
        NSInteger index = [self.components indexOfObject:view];
        [self.components removeObject:view];
        [self.dataSource collection:self removeView:view atIndex:index];
        [view removeFromSuperview];
    }
}

- (void)addMovableView:(MTMovableView *)view
{
    [self.components addObject:view];
    [self addSubview:view];
    [self snapComponent:view];
}

- (void)swapIfNeededView:(MTMovableView *)touchedView withView:(MTMovableView *)staticView
{
    //safe since they're the same object (we use this method just for iterating through array)
    if (touchedView == staticView) {
        return;
    }
    
    CGPoint touchedViewCenter = [self convertPoint:touchedView.center fromView:touchedView.superview];
    
    BOOL doesIntersectHorizontally = touchedViewCenter.x >= staticView.frame.origin.x &&
                                     touchedViewCenter.x <= CGRectGetMaxX(staticView.frame);
    
    if (doesIntersectHorizontally) {
        NSInteger touchedViewIndex = [self.components indexOfObject:touchedView];
        NSInteger staticViewIndex = [self.components indexOfObject:staticView];
        
        self.pivotPoint = staticView.frame.origin;
        [self.components exchangeObjectAtIndex:staticViewIndex withObjectAtIndex:touchedViewIndex];
        
        if ([self.dataSource respondsToSelector:@selector(collection:moveView:fromIndex:toIndex:)]) {
            [self.dataSource collection:self moveView:staticView fromIndex:staticViewIndex toIndex:touchedViewIndex];
        }
                
        [self updateLayout];
    }
}

- (void)layoutSubviewsOnChangingPositionOfView:(MTMovableView *)touchedView
{
    for (int i = 0, arrayCount = [self.components count]; i < arrayCount; i++) {
        MTMovableView *view = self.components[i];
        [self swapIfNeededView:touchedView withView:view];
    }
}

- (void)snapComponent:(MTMovableView *)touchedView
{
    //prepare result after rearrange
    if ([self.dataSource respondsToSelector:@selector(collection:didMoveView:toIndex:)]) {
        [self.dataSource collection:self didMoveView:touchedView toIndex:[self.components indexOfObject:touchedView]];
    }
}

#pragma mark - Setters&Getters

- (NSString *)textRepresentation
{
    NSMutableString *textRepresentationOfViews = [NSMutableString new];
    
    for (MTMovableView *view in self.components) {
        [textRepresentationOfViews appendString:view.text];
    }
    
    return textRepresentationOfViews;
}

- (NSMutableArray *)components
{
    if (!_components) {
        self.components = [NSMutableArray new];
    }
    return _components;
}

- (CGPoint)focusXYBounds
{
    if (CGPointEqualToPoint(_focusXYBounds, CGPointZero)) {
        self.focusXYBounds = kDefaultFocusXYBounds;
    }
    return _focusXYBounds;
}

- (NSUInteger)hash {
    NSUInteger hash = [self.class hash] + self.frame.origin.x + self.frame.origin.y + self.frame.size.width + self.frame.size.height;
    
    return hash;
}

#pragma mark NSCopying proto

- (id)copyWithZone:(NSZone *)zone {
    MTMovableViewCollection *copy = [[MTMovableViewCollection allocWithZone:zone] init];
    
    copy.frame              = self.frame;
    /*copy.text               = self.text;
    copy.carriedView        = [NSKeyedUnarchiver unarchiveObjectWithData:[NSKeyedArchiver archivedDataWithRootObject:self.carriedView]];
    copy.isReturnOnMoveEnd  = self.isReturnOnMoveEnd;
    copy.isOnMove           = self.isOnMove;
    copy.toolsView          = self.toolsView;
    copy.onMoveBlock        = self.onMoveBlock;
    copy.onMoveEndedBlock   = self.onMoveEndedBlock;
    copy.onMoveBeganBlock   = self.onMoveBeganBlock;*/
    
    return copy;
}

@end
