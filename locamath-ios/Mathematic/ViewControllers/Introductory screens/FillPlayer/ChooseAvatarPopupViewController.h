//
//  AvatarChoosingPopupVCViewController.h
//  Mathematic
//
//  Created by serg on 12/25/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"

@protocol ChooseAvatarPopupViewControllerDelegate <NSObject>
- (void)didChangedAvatar;
@end

@interface ChooseAvatarPopupViewController : PresentableViewController

@property (weak, nonatomic) BaseViewController<ChooseAvatarPopupViewControllerDelegate> *parentVC;

- (IBAction)onTapContinue:(id)sender;

@end
