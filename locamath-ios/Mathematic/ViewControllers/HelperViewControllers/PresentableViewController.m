//
//  PresentingViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"
#import "objc/runtime.h"

@interface PresentableViewController ()
@end

@implementation PresentableViewController

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
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (BaseViewController *)myPresentingViewController
{
    NSLog(@"presentingViewController %@", [super presentingViewController] ?: self.parentVC);
    return (BaseViewController *)[super presentingViewController] ?: self.parentVC;
}

#pragma mark - Actions

- (void)dismiss
{
    [self viewWillDisappear:YES];
    
    self.seguesStructure.numOfCurrentVC--;
    
    if ([self.parentVC isKindOfClass:[PresentableViewController class]]) {
        [self.parentVC.view setHidden:NO];
        self.parentVC.isViewUnloadingLocked = NO;
    }
    
    [self.view removeFromSuperview];
    [self viewDidDisappear:YES];
    
    ^() {
        objc_setAssociatedObject(self.parentVC, kThisController, nil, OBJC_ASSOCIATION_RETAIN_NONATOMIC);

        //NOTE: not clear why this check is needed!!!
//        if (self.onEnd == nil) {
            [self.parentVC viewWillAppear:YES];
            [self.parentVC viewDidAppear:YES];
//        }
        
        if (![self.parentVC respondsToSelector:@selector(parentVC)]) {
            if (self.onEnd != nil) {
                self.onEnd();
            }
        }
    } ();
}

- (void)presentOnViewController:(BaseViewController *)vc finish:(OnTransitionEndBlock)onEnd
{
    self.parentVC = vc;
    self.onEnd = onEnd;
    
    BaseViewController *canvas = nil;
    PresentableViewController *presentingVC = nil;
    
    if ([vc isKindOfClass:[PresentableViewController class]]) {
        presentingVC = (PresentableViewController*)vc;
        
        presentingVC.seguesStructure = self.seguesStructure;
        
        canvas = [[presentingVC chainPerformSelector:@selector(self)] parentVC];
        //[presentingVC.view setHidden:YES];
        //presentingVC.isViewUnloadingLocked = YES;
    } else {
        canvas = vc;
        canvas.isViewUnloadingLocked = YES;
    }
    
    [self.parentVC viewWillDisappear:YES];
    [self view];                // Loading view (loadView, viewDidLoad)
    [self viewWillAppear:YES];  // Appearing view (viewWillAppear)
    [canvas.view addSubview:self.view]; // Real appearing view
     canvas.presentedVC = self;//add to presented for dismiss flow
    
    [self viewDidAppear:YES];   // Appearing view (viewDidAppear)
    [self.parentVC viewDidDisappear:YES];
    
    vc.isViewUnloadingLocked = NO;
    presentingVC.isViewUnloadingLocked = NO;
    
    //create runtime property for parent
    //in order to do not create property for every parent, from which we're presented
    objc_setAssociatedObject(self.parentVC, kThisController, self, OBJC_ASSOCIATION_RETAIN_NONATOMIC);
}

//get first item in chain
- (id)chainPerformSelector:(SEL)sel
{
    __unsafe_unretained id retValue = nil;
    
    NSInvocation *nextLinkInvocation = [NSInvocation invocationWithMethodSignature:[[self class] instanceMethodSignatureForSelector:sel]];
    [nextLinkInvocation setSelector:sel];
    [nextLinkInvocation setTarget:self];
    
    [nextLinkInvocation invoke];
    
    if ([[nextLinkInvocation methodSignature] methodReturnType] [0] == '@') {
        [nextLinkInvocation getReturnValue:&retValue];
    }
    
    if ([self.parentVC respondsToSelector:@selector(chainPerformSelector:)]) {
        PresentableViewController *presentingParent = (PresentableViewController *)self.parentVC;
        retValue = [presentingParent chainPerformSelector:sel];
    }
    
    return retValue;
}

- (void)prepareForPresentingViewController:(PresentableViewController *)vc
{
    // Empty implementation
}

- (void)prepareForDismissing
{
    // Empty implementation
}

- (void)prepareForTransition
{
    // Empty implementation
}

- (void)presentNextViewController
{
    id nextVC = [self.seguesStructure nextViewController];
    
    if (nextVC) {
        [self prepareForPresentingViewController:nextVC];
        [self prepareForTransition];
        [nextVC presentOnViewController:self finish:self.onEnd];
    } else {
        [self prepareForDismissing];
        [self prepareForTransition];
        [self chainPerformSelector:@selector(dismiss)];
    }
}

- (void)dismissToRootViewController
{
    [self chainPerformSelector:@selector(dismiss)];
}

- (void)performOnViewAppearAfterDelayIfNeeded:(OnTransitionEndBlock)block
{
    double delayInSeconds = 0.3;
    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
    dispatch_after(popTime, dispatch_get_main_queue(), ^(void) {
        if ([self.view superview]) {
            block();
        }
    });
}

@end
