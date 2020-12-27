//
//  BaseKeyboardAppearancePopupViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 24.09.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseKeyboardAppearancePopupViewController.h"
#import "DAKeyboardControl.h"

@interface BaseKeyboardAppearancePopupViewController ()

@end


@implementation BaseKeyboardAppearancePopupViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
	// Do any additional setup after loading the view.
    self.lowestViewYOffset = self.continueButton.frame.origin.y;
}

- (void)viewDidAppear:(BOOL)animated
{
    if (nil != self.continueButton) {
        BaseKeyboardAppearancePopupViewController *weakSelf = self;
                
        UIView *superView = self.view.superview;
        
        [superView addKeyboardPanningWithActionHandler:^(CGRect keyboardFrameInView) {
            
            CGRect lowestFieldConvertedToMainViewRect = weakSelf.lowestFieldRect;
            
            CGFloat lowestFieldYOrigin = lowestFieldConvertedToMainViewRect.origin.y;
            
            CGFloat newLowestYOrigin = keyboardFrameInView.origin.y - weakSelf.lowestFieldRect.size.height;
            
            if (newLowestYOrigin > weakSelf.lowestViewYOffset) {
                newLowestYOrigin = weakSelf.lowestViewYOffset;
            }
            
            CGFloat viewOffset = newLowestYOrigin - lowestFieldYOrigin;
            
            CGRect viewFrame = weakSelf.view.frame;
            
            viewFrame.origin.y += viewOffset;
            weakSelf.view.frame = viewFrame;
        }];
    }
    
    [super viewDidAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
    
    [self.view endEditing:YES];
    [self.view.superview removeKeyboardControl];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (CGRect)lowestFieldRect
{
    return [self.view.superview convertRect:self.continueButton.frame fromView:self.view];
}

@end
