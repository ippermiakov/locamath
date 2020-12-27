//
//  AlertViewManager.h
//  Mathematic
//
//  Created by alexbutenko on 7/2/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class MBProgressHUD;

@interface AlertViewManager : NSObject

+ (AlertViewManager *)sharedInstance;

@property (weak, nonatomic) UIAlertView *displayedAlertView;
@property (weak, nonatomic) MBProgressHUD *displayedHUD;

@end
