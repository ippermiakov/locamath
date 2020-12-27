//
//  PresentingViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "PresentingSeguesStructure.h"

#define kThisController "CustomPresentedVC"

typedef void(^FinishBlock)();
typedef void(^OnTransitionEndBlock)();

@interface PresentableViewController : BaseViewController

@property (strong, nonatomic) PresentingSeguesStructure *seguesStructure;
@property (weak, nonatomic)   BaseViewController *parentVC;

@property (nonatomic, copy) OnTransitionEndBlock onEnd;
@property (nonatomic, copy) FinishBlock onFinish;

- (void)presentOnViewController:(BaseViewController *)vc finish:(OnTransitionEndBlock)onEnd;
- (void)presentNextViewController;
- (void)dismiss;

- (id)chainPerformSelector:(SEL)sel;

- (void)prepareForPresentingViewController:(PresentableViewController *)vc;
- (void)prepareForDismissing;
- (void)prepareForTransition;

- (void)dismissToRootViewController;

- (void)performOnViewAppearAfterDelayIfNeeded:(OnTransitionEndBlock)block;

@end
