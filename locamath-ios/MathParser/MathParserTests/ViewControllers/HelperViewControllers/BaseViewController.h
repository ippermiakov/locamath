//
//  BaseViewController.h
//  Mathematic
//
//  Created by Developer on 10.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewControllerDelegate.h"
#import "SoundManager.h"
#import "ViewControllerViewAppearanceTracking.h"

typedef void(^BackCompletionBlock)();


@interface BaseViewController : UIViewController <BaseViewControllerDelegate, ViewControllerViewAppearanceTracking>

@property (weak, nonatomic) id <BaseViewControllerDelegate> backDelegate;
@property (strong, nonatomic) SoundManager *soundManager;
@property (unsafe_unretained, nonatomic) LevelType levelType;
@property (unsafe_unretained, nonatomic) BOOL isViewUnloadingLocked;

//return NO to forbid view unloading in any case, YES by default
- (BOOL)canViewUnloadingBeUnlocked;

- (void)setActualFonts;

- (void)goBackAnimated:(BOOL)animated
          withDelegate:(id)delegate
            withOption:(BOOL)option
            completion:(BackCompletionBlock)completion;

- (void)goBackAnimated:(BOOL)animated
          withDelegate:(id)delegate
            withOption:(BOOL)option;

- (void)goBackAnimated:(BOOL)animated
          withDelegate:(id)delegate
            completion:(BackCompletionBlock)completion;

- (void)updateViewOnSyncFinished;
- (void)updateLevelBackgroundImage:(UIImageView *)correctBackground;

@end
