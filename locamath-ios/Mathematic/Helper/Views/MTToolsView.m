//
//  MTToolsView.m
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTToolsView.h"
#import "MTMovableView.h"
#import "DistanceCalculation.h"

//olympiads
static CGPoint const kSpaceBetweenViews = {10.0f, 0.0f};
static NSInteger const kStartSpaceBetweenViews = 30;

static NSUInteger const kMinToolWidth = 15;
static NSUInteger const kMinToolHeight = 15;

@interface MTToolsView ()

@end


@implementation MTToolsView

- (void)awakeFromNib
{
    [self reloadData];
}

- (MTMovableView *)movableViewWithComponent:(UIView *)component
{
    MTMovableView *movableView = nil;
    
    //avoid redundant object copying
    if ([component isKindOfClass:[MTMovableView class]]) {
        movableView = (MTMovableView *)component;
    } else {
        movableView = [[MTMovableView alloc] initWithFrame:component.frame];
        movableView.carriedView = component;
    }
    
    movableView.tag = component.tag;
    movableView.overlayView = self.overlayView;
    component.frame = component.bounds;
    
    CGFloat hitAreaWidthEnlarge = 0;
    CGFloat hitAreaHeightEnlarge = 0;
    
    if (kMinToolWidth - component.bounds.size.width > 0) {
        hitAreaWidthEnlarge = kMinToolWidth - component.bounds.size.width;
    }
    
    if (kMinToolHeight - component.bounds.size.height > 0) {
        hitAreaHeightEnlarge = kMinToolHeight - component.bounds.size.height;
    }
    
    movableView.hitAreaEnlarge = CGSizeMake(hitAreaWidthEnlarge, hitAreaHeightEnlarge);
    
//    NSLog(@"%@ hit area enlarge %@", component, NSStringFromCGSize(movableView.hitAreaEnlarge));
    
    movableView.toolsView = self;
    movableView.onMoveEndedBlock = ^(MTMovableView *view) {
        [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationPutComponent object:view];
    };
    movableView.onMoveBeganBlock = ^(MTMovableView *view) {
        [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationChooseComponent object:view];
    };
    movableView.onMoveCompletionBlock = ^(MTMovableView *view) {
        [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationReloadComponents object:view];
    };
    movableView.isReturnOnMoveEnd = YES;
    
    
    return movableView;
}

- (void)reloadData
{
    NSMutableArray *mutableMovableTools = [NSMutableArray new];
    
    for (UIView *component in self.tools) {
        MTMovableView *movableView = [self movableViewWithComponent:component];
        [mutableMovableTools addObject:movableView];
        [self addSubview:movableView];
    }
    
    self.tools = mutableMovableTools;
    self.displayedTools = [mutableMovableTools copy];
    
//    [self calculateDistanceBetweenElements];
    [self calculateDistanceBetweenElementCenters];
    [self calculateRightBound];
}

- (void)excludeAllCharacters
{
    [self.tools makeObjectsPerformSelector:@selector(removeFromSuperview)];
    [self.tools removeAllObjects];
}

- (void)excludeDisplayingCharacters:(NSArray *)characters
{
    NSMutableArray *toRemove = [NSMutableArray new];
    
    for (UIView *component in self.tools) {
        for (NSString *str in characters) {
            if ([((MTMovableView *)component).text isEqualToString:str]) {
                [toRemove addObject:component];
                [component removeFromSuperview];
            }
        }
    }
    
    [self.tools removeObjectsInArray:toRemove];
}

- (void)reloadDataWithViews:(NSArray *)views
{
    [self excludeAllCharacters];
    [self displayAdditionalViews:views];
}

- (void)displayAdditionalViews:(NSArray *)views
{
    //NSLog(@"tools: %@", self.tools);
    //NSLog(@"views to display: %@", views);
    
    //sort to align last the largest by width and largest by height first
    views = [views sortedArrayUsingComparator:^NSComparisonResult(UIView *obj1, UIView *obj2) {
        CGFloat differenceBetweenWidthHeight1 = obj1.frame.size.width - obj1.frame.size.height;
        CGFloat differenceBetweenWidthHeight2 = obj2.frame.size.width - obj2.frame.size.height;

        return differenceBetweenWidthHeight1 > differenceBetweenWidthHeight2;
    }];
    
    [views enumerateObjectsUsingBlock:^(UIView *obj, NSUInteger idx, BOOL *stop) {
        MTMovableView *movableView = [self movableViewWithComponent:obj];
        
        if (self.isTaskCompleted) {
            movableView.isMoveEnabled = NO;
        }
        
        CGRect movableViewFrame = (CGRect){CGPointZero, movableView.frame.size};
        movableViewFrame.origin = [self locationToView:movableView];
        
        movableView.frame = movableViewFrame;
        
        if (![self.tools containsObject:movableView]) {
            [self.tools addObject:movableView];
        }
        
        [self addSubview:movableView];
    }];
    
    [self layoutToolsCentered];
}

- (void)layoutToolsCentered
{
    __block CGFloat maxY = 0;
    __block CGFloat maxX = 0;

    [self.tools each:^(UIView *view) {
        if (CGRectGetMaxY(view.frame) > maxY) {
            maxY = CGRectGetMaxY(view.frame);
        }
        
        if (CGRectGetMaxX(view.frame) > maxX) {
            maxX = CGRectGetMaxX(view.frame);
        }
    }];
    
    CGFloat yOffset = (self.frame.size.height - maxY)/2;
    CGFloat xOffset = (self.frame.size.width - maxX)/2;
    
    //apply centering
    [self.tools each:^(UIView *view) {
        CGRect frame = view.frame;
        frame.origin.x += xOffset;
        frame.origin.y += yOffset;
        view.frame = frame;
    }];
}

- (void)calculateDistanceBetweenElementCenters
{
    NSMutableArray *subviews = self.tools.copy;
    
    NSMutableArray *pairs = [NSMutableArray new];
    
    //all pairs: 1st-2nd, 1st-3rd ... 2nd-3rd, 2nd-4th, ...
    for (NSInteger i = 0, end = subviews.count - 1; i < end; ++i) {
        for (NSInteger j = i + 1, end = subviews.count; j < end; ++j) {
            [pairs addObject:@[subviews[i], subviews[j]]];
        }
    }
    
    if (pairs.count == 0) {
        UIView *view = [self.subviews lastObject];
        self.distanceBetweenElementCenters = view.frame.size;
        return;
    }
        
    //removing pairs, which are laid out too close
    [pairs filterUsingPredicate:[NSPredicate predicateWithBlock:^BOOL(id evaluatedObject, NSDictionary *bindings) {
        UIView *view1 = [evaluatedObject objectAtIndex:0];
        UIView *view2 = [evaluatedObject objectAtIndex:1];
        
        return
        ABS(view1.center.x - view2.center.x) > /*kSpaceBetweenViews.y*/0 &&
        ABS(view1.center.y - view2.center.y) > /*kSpaceBetweenViews.y*/0;
    }]];
    
    //sort by distance pairs from pair with smallest distance to largest
    [pairs sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        UIView *view11 = [obj1 objectAtIndex:0];
        UIView *view12 = [obj1 objectAtIndex:1];
        
        UIView *view21 = [obj2 objectAtIndex:0];
        UIView *view22 = [obj2 objectAtIndex:1];
        
        return CGPointGetDistance(view11.center, view12.center) >
               CGPointGetDistance(view21.center, view22.center);
    }];
    
    UIView *view1 = pairs[0][0];
    UIView *view2 = pairs[0][1];
    
    self.distanceBetweenElementCenters = CGSizeMake(ABS(view1.center.x - view2.center.x),
                                                    ABS(view1.center.y - view2.center.y));
}

- (void)calculateDistanceBetweenElements
{
    NSMutableArray *subviews = self.tools.copy;
    
    NSMutableArray *pairs = [NSMutableArray new];
    
    for (NSInteger i = 0, end = subviews.count - 1; i < end; ++i) {
        for (NSInteger j = i + 1, end = subviews.count; j < end; ++j) {
            [pairs addObject:@[subviews[i], subviews[j]]];
        }
    }
    
    if (pairs.count == 0) {
        return;
    }
    
    [pairs filterUsingPredicate:[NSPredicate predicateWithBlock:^BOOL(id evaluatedObject, NSDictionary *bindings) {
        UIView *view1 = [evaluatedObject objectAtIndex:0];
        UIView *view2 = [evaluatedObject objectAtIndex:1];
        
        return
        ABS(view1.frame.origin.x - view2.frame.origin.x) > /*kSpaceBetweenViews.y*/0 &&
        ABS(view1.frame.origin.y - view2.frame.origin.y) > /*kSpaceBetweenViews.y*/0;
    }]];
    
    [pairs sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        UIView *view11 = [obj1 objectAtIndex:0];
        UIView *view12 = [obj1 objectAtIndex:1];
        
        UIView *view21 = [obj2 objectAtIndex:0];
        UIView *view22 = [obj2 objectAtIndex:1];
        
        return CGPointGetDistance(view11.frame.origin, view12.frame.origin) >
        CGPointGetDistance(view21.frame.origin, view22.frame.origin);
    }];
    
    UIView *view1 = pairs[0][0];
    UIView *view2 = pairs[0][1];
    
    self.distanceBetweenElements = CGSizeMake(ABS(view1.center.x - view2.center.x) -(view1.frame.size.width  + view2.frame.size.width)  / 2,
                                              ABS(view1.center.y - view2.center.y) -(view1.frame.size.height + view2.frame.size.height) / 2);
}

- (void)calculateRightBound
{
    if (self.tools.count == 0) {
        return;
    }
    
    NSMutableArray *right = self.tools.mutableCopy;
    
    [right sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 frame].origin.x < [obj2 frame].origin.x;
    }];

    self.rowWidth = CGRectGetMaxX([right[0] frame]);
}

- (CGPoint)locationToView:(MTMovableView *)view
{
    if (self.tools.count == 0) {
        return CGPointZero;
    }
    
    NSMutableArray *toolsFromBottomToTop = self.tools.mutableCopy;
    NSMutableArray *toolsFromLeftToRight = self.tools.mutableCopy;

    //tools sorted from bottom to top
    [toolsFromBottomToTop sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 frame].origin.y < [obj2 frame].origin.y;
    }];
    
    //tools sorted from left to right
    [toolsFromLeftToRight sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 frame].origin.x > [obj2 frame].origin.x;
    }];
        
    MTMovableView *theMostBottomView = toolsFromBottomToTop[0];
    MTMovableView *theMostLeftView   = toolsFromLeftToRight[0];
    
    NSMutableArray *theMostBottomRow = [toolsFromBottomToTop select:^BOOL(UIView *view) {
        return ABS([view center].y - [theMostBottomView center].y) < self.distanceBetweenElementCenters.height;
    }].mutableCopy;
        
    [theMostBottomRow sortUsingComparator:^NSComparisonResult(id obj1, id obj2) {
        return [obj1 frame].origin.x < [obj2 frame].origin.x;
    }];
    
    MTMovableView *theMostRightBottomView = theMostBottomRow[0];
    
    CGPoint theLocation = theMostRightBottomView.frame.origin;
    
    theLocation.x = MAX(theLocation.x + kSpaceBetweenViews.x,
                        theLocation.x + theMostRightBottomView.frame.size.width + kSpaceBetweenViews.x
                        + view.hitAreaEnlarge.width + theMostRightBottomView.hitAreaEnlarge.width);
    
    return [self locationForView:view
                 currentLocation:theLocation
                mostLeftLocation:theMostLeftView.frame.origin.x];
}

- (CGPoint)locationForView:(MTMovableView *)view
           currentLocation:(CGPoint)location
          mostLeftLocation:(CGFloat)mostLeftViewXLocation
{
    while ([self needsToPutNextColumnView:view withLocation:location]) {
        location.x += kSpaceBetweenViews.x + view.hitAreaEnlarge.width;
       
    };
    
    if (![self needsToPutNextRowView:view withLocation:location]) {
        return location;
    } else {
        location.x = mostLeftViewXLocation;
        //next row offset
        location.y += view.frame.size.height + (kSpaceBetweenViews.y/**2*/) + view.hitAreaEnlarge.height;
        //NSLog(@"tools view  location :%@", NSStringFromCGPoint(location));
        return [self locationForView:view currentLocation:location mostLeftLocation:mostLeftViewXLocation];
    }
}


- (BOOL)needsToPutNextColumnView:(MTMovableView *)view withLocation:(CGPoint)theLocation
{
//    NSLog(@"tools view %@ COLUMN isOverlapping %@ location %@ rowWidth %f", view, [self isOverlappingAnyViewWithRect:(CGRect){theLocation, view.frame.size}] ? @"YES":@"NO",
//         NSStringFromCGPoint(theLocation), self.rowWidth);
    //do we overlap any view and could we shift |view| for kSpaceBetweenViews to try to fix
    return [self isOverlappingAnyViewWithRect:(CGRect){theLocation, view.frame.size}] &&
            theLocation.x + kSpaceBetweenViews.x < self.rowWidth/* - kSpaceBetweenViews*/;
}

- (BOOL)needsToPutNextRowView:(MTMovableView *)view withLocation:(CGPoint)theLocation
{
//   NSLog(@"tools view %@ ROW isOverlapping %@ location %@ rowWidth %f", view, [self isOverlappingAnyViewWithRect:(CGRect){theLocation, view.frame.size}] ? @"YES":@"NO",
//          NSStringFromCGPoint(theLocation), self.rowWidth);
    return theLocation.x + view.frame.size.width > self.rowWidth/* - kSpaceBetweenViews*/ ||
           [self isOverlappingAnyViewWithRect:(CGRect){theLocation, view.frame.size}];
}

- (BOOL)isOverlappingAnyViewWithRect:(CGRect)rect
{
//    NSLog(@"Overlapping Rect :%@ !!!!", NSStringFromCGRect(rect));
    return [self.tools any:^BOOL(UIView *view) {
//        NSLog(@"tools view rect :%@ !!!!", NSStringFromCGRect(view.frame));
        return CGRectIntersectsRect(view.frame, rect);
    }];
}

#pragma mark - Setters&Getters

- (void)setOverlayView:(UIView *)overlayView
{
    _overlayView = overlayView;
    
    [self.tools each:^(id sender) {
        [sender setOverlayView:overlayView];
    }];
}

@end



