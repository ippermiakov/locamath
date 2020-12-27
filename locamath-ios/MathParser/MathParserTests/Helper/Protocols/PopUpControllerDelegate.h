//
//  PopUpControllerDelegate.h
//  Mathematic
//
//  Created by Developer on 07.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol PopUpControllerDelegate <NSObject>

@optional
- (void)popOverDidTapOkButton;
- (void)popOverDidTapRestoreButton;
- (void)popOverDidTapHomeButton;
- (void)popOverDidTapNextButton;

@end
