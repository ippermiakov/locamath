//
//  UIFont+Mathematic.m
//  Mathematic
//
//  Created by alexbutenko on 6/27/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UIFont+Mathematic.h"
#import "UIView+LWAutoFont.h"

static CGFloat const kOlympiadFontSize = 40.0f;
static CGFloat const kComparativeStatisticsFontSize = 16.0f;
static CGFloat const kComparativeStatisticsCurrentChildFontSize = 20.0f;

@implementation UIFont (Mathematic)

+ (UIFont *)olympiadTasksFont
{
    return [UIFont fontWithName:[UIView defaultBoldFontName] size:kOlympiadFontSize];
}

+ (UIFont *)comparativeStatisticsFont
{
    return [UIFont fontWithName:[UIView defaultBoldFontName] size:kComparativeStatisticsFontSize];
}

+ (UIFont *)comparativeStatisticsCurrentChildFont
{
    return [UIFont fontWithName:[UIView defaultBoldFontName] size:kComparativeStatisticsCurrentChildFontSize];
}

@end
