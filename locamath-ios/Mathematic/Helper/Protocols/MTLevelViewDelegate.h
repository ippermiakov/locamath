//
//  MTLevelViewDelegate.h
//  Mathematic
//
//  Created by Developer on 04.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class Level;

@protocol MTLevelViewDelegate <NSObject>
@required
- (void)openLevel:(Level *)level withDataLevelView:(NSData *)dataLevelView;

@end
