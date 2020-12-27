//
//  UIView+UIView_OverlayForMovableViews.m
//  Mathematic
//
//  Created by alexbutenko on 6/24/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UIView+OverlayForMovableViews.h"
#import "AppDelegate.h"
#import "AlertViewManager.h"

@implementation UIView (OverlayForMovableViews)

+ (UIView *)overlayForStudyingAndExervices
{
    AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;

    NSArray *visibleSubviews = [appDelegate.window.subviews reject:^BOOL(UIView *view) {
        return view.alpha == 0;
    }];
        
    NSUInteger actualVisibleSubviewsCount = [visibleSubviews count];
    
    if ([AlertViewManager sharedInstance].displayedHUD) {
        actualVisibleSubviewsCount--;
    }
        
    if (actualVisibleSubviewsCount >= 2) {
        return visibleSubviews[1];
    } else {
        return visibleSubviews[0];
    }
}

+ (UIView *)overlayForOlympiads
{
    AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
    
    NSArray *visibleSubviews = [appDelegate.window.subviews reject:^BOOL(UIView *view) {
        return view.alpha == 0;
    }];
    
    return visibleSubviews[0];
}

@end
