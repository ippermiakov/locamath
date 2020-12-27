//
//  BaseViewController.m
//  Mathematic
//
//  Created by Developer on 10.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "UIView+LWAutoFont.h"
#import "MTHTTPClient.h"
#import "ChildManager.h"
#import "MBProgressHUD.h"
#import "SynchronizationManager.h"
#import "MBProgressHUD+Mathematic.h"
#import "AlertViewManager.h"

@interface BaseViewController ()

@end

@implementation BaseViewController

@synthesize didViewAppear = _didViewAppear;

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:aDecoder];
    if (self) {
        [self commonInit];
    }
    
    return self;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        [self commonInit];
    }
    
    return self;
}

- (id)init
{
    self = [super init];
    
    if (self) {
        [self commonInit];
    }
    
    return self;
}

- (void)commonInit
{
    self.soundManager = [SoundManager sharedInstance];
    self.isViewUnloadingLocked = ![self canViewUnloadingBeUnlocked];
}

- (void)setIsViewUnloadingLocked:(BOOL)isViewUnloadingLocked
{
//    NSLog(@"%@ attempt to set isViewUnloadingLocked: %@", NSStringFromClass([self class]), isViewUnloadingLocked ? @"YES":@"NO");
    
    if (isViewUnloadingLocked ||
        (!isViewUnloadingLocked && [self canViewUnloadingBeUnlocked])) {
//        NSLog(@"%@ ALLOWED to set isViewUnloadingLocked: %@", NSStringFromClass([self class]), isViewUnloadingLocked ? @"YES":@"NO");
        _isViewUnloadingLocked = isViewUnloadingLocked;
    } else {
//        NSLog(@"%@ FORBIDDEN to set isViewUnloadingLocked: %@", NSStringFromClass([self class]), isViewUnloadingLocked ? @"YES":@"NO");
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    [self.view setActualFonts];
}

- (void)viewWillAppear:(BOOL)animated
{
    [self registerObservers];
}

- (void)viewDidAppear:(BOOL)animated
{
    self.didViewAppear = YES;

    [self performSynchronizationIfNeeded];
}

- (void)viewDidDisappear:(BOOL)animated
{
    if (!self.isViewUnloadingLocked) {
//        NSLog(@"! %@ unload view", NSStringFromClass([self class]));
        self.view = nil;
    } else {
//        NSLog(@"! %@ skipped unloading", NSStringFromClass([self class]));
    }

    self.didViewAppear = NO;

    [self removeNotificationObservation];
}

- (void)setActualFonts
{
    [self.view setActualFonts];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (BOOL)shouldAutorotate
{
    return YES;
}

- (NSUInteger)application:(UIApplication *)application supportedInterfaceOrientationsForWindow:(UIWindow *)window
{
    return (UIInterfaceOrientationMaskLandscapeLeft | UIInterfaceOrientationLandscapeRight);
}

#pragma mark - Actions

- (void)goBackAnimated:(BOOL)animated withDelegate:(id)delegate withOption:(BOOL)option
{
    [self goBackAnimated:animated withDelegate:delegate withOption:option completion:nil];
}

- (void)goBackAnimated:(BOOL)animated
          withDelegate:(id)delegate
            completion:(BackCompletionBlock)completion
{
    [self goBackAnimated:animated withDelegate:delegate withOption:NO completion:completion];
}

- (void)goBackAnimated:(BOOL)animated
          withDelegate:(id)delegate
            withOption:(BOOL)option
            completion:(BackCompletionBlock)completion
{
    if ([delegate respondsToSelector:@selector(didFinishBackWithOption:)]) {
        [delegate didFinishBackWithOption:option];
    } else {
        //        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"didFinishBack: did not respond." userInfo:nil];
        //        [exeption raise];
    }
    
    [self dismissViewControllerAnimated:animated completion:completion];
}

- (void)didFinishBackWithOption:(BOOL)option
{
    // Override in the child.
}

- (void)updateViewOnSyncFinished
{
    [self performSynchronizationIfNeeded];
}

- (void)updateLevelBackgroundImage:(UIImageView *)correctBackground
{
    switch (self.levelType) {
        case kLevelType1:
            correctBackground.image = [UIImage imageNamed:@"exercises_page_bg.png"];
            break;
            
        case kLevelType2:
            correctBackground.image = [UIImage imageNamed:@"Back_Gray@2x.png"];
            break;
            
        default:
            break;
    }
}

- (BOOL)canViewUnloadingBeUnlocked
{
    return YES;
}

@end
