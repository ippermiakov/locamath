//
//  MBProgressHUD+Mathematic.h
//  Mathematic
//
//  Created by alexbutenko on 7/31/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MBProgressHUD.h"

@interface MBProgressHUD (Mathematic)

+ (MBProgressHUD *)showHUDForWindow;
+ (void)hideHUDForWindow;
+ (MBProgressHUD *)showSyncHUDForWindow;
- (void)updateWithProgress:(CGFloat)progress;
+ (void)hideOnSyncCompletion;
+ (void)hideOnSyncFailure;

@end
