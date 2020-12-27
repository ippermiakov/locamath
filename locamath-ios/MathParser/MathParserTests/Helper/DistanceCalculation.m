//
//  DistanceCalculation.c
//  Mathematic
//
//  Created by Dmitriy Gubanov on 15.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//



CGFloat CGPointGetDistance(CGPoint point0, CGPoint point1)
{
    CGPoint distance;
    
    distance.x = ABS(point0.x - point1.x);
    distance.y = ABS(point0.y - point1.y);
    
    return sqrt(pow(distance.x, 2.0) + pow(distance.y, 2.0));
}


CGFloat CGRectGetCenterDistance(CGRect rect0, CGRect rect1)
{
    return CGPointGetDistance(CGPointMake(CGRectGetMidX(rect0), CGRectGetMidX(rect0)), CGPointMake(CGRectGetMidX(rect1), CGRectGetMidX(rect1)));
}


CGFloat CGRectGetDistance(CGRect rect0, CGRect rect1)
{
    CGRect vector;
    vector.origin = rect0.origin;
    vector.size   = CGSizeMake(CGRectGetMidX(rect1) - CGRectGetMidX(rect0),
                               CGRectGetMidY(rect1) - CGRectGetMidY(rect0));
    
    CGFloat sideH0;
    CGFloat sideH1;
    
    CGFloat sideV0;
    CGFloat sideV1;
    
    BOOL intersection = NO;
    BOOL fail = NO;
    BOOL isVertical;
    
    if (vector.size.width > 0) {
        sideH0 = CGRectGetMaxX(rect0);
        sideH1 = CGRectGetMinX(rect1);
        if (sideH0 > sideH1) {
            fail = YES;
        }
    } else {
        sideH0 = CGRectGetMinX(rect0);
        sideH1 = CGRectGetMaxX(rect1);
        if (sideH1 > sideH0) {
            fail = YES;
        }
    }
    
    isVertical = fail;
    intersection = intersection || fail;
    fail = NO;
    
    if (vector.size.height > 0) {
        sideV0 = CGRectGetMaxY(rect0);
        sideV1 = CGRectGetMinY(rect1);
        if (sideV0 > sideV1) {
            fail = YES;
        }
    } else {
        sideV0 = CGRectGetMinY(rect0);
        sideV1 = CGRectGetMaxY(rect1);
        if (sideV1 > sideV0) {
            fail = YES;
        }
    }
    intersection = intersection || fail;
    
    CGFloat distance;
    
    if (intersection == YES) {
        if (isVertical) {
            distance = ABS(sideV0 - sideV1);
        } else {
            distance = ABS(sideH0 - sideH1);
        }
    } else {
        CGPoint point0 = CGPointMake(sideV0, sideH0);
        CGPoint point1 = CGPointMake(sideV1, sideH1);
        
        distance = CGPointGetDistance(point0, point1);
    }
    
    if (CGRectIntersectsRect(rect0, rect1))
        distance /= 10;
    
    return distance;
}
