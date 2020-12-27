//
//  PopupForDefaultChildViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 26.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PresentableViewController.h"

typedef NS_ENUM(NSUInteger, PopupForDefaultType) {
    kPopupForDefaultTypeHome   = 0,
    kPopupForDefaultTypeProfile   = 1,
    kPopupForDefaultTypeStatistic   = 2,
};

@interface PopupForDefaultChildViewController : PresentableViewController

@property (unsafe_unretained, nonatomic) PopupForDefaultType popupType;
@property (unsafe_unretained, nonatomic) BOOL isOkSelected;
@end
