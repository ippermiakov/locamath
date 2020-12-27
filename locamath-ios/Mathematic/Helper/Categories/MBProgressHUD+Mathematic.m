//
//  MBProgressHUD+Mathematic.m
//  Mathematic
//
//  Created by alexbutenko on 7/31/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MBProgressHUD+Mathematic.h"
#import "MBProgressHUD.h"
#import "SynchronizationManager.h"
#import "AlertViewManager.h"
#import "AppDelegate.h"
#import "ChildManager.h"

@implementation MBProgressHUD (Mathematic)

+ (MBProgressHUD *)showHUDForWindow
{
    AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
    
    if (appDelegate.window) {
        if (![AlertViewManager sharedInstance].displayedHUD && ![AlertViewManager sharedInstance].displayedAlertView) {
            MBProgressHUD *hud = [MBProgressHUD showHUDAddedTo:appDelegate.window animated:NO];
            hud.removeFromSuperViewOnHide = YES;
            [AlertViewManager sharedInstance].displayedHUD = hud;

            return hud;
        }
    }
    
    return nil;
}

+ (void)hideHUDForWindow
{
    if (![AlertViewManager sharedInstance].displayedHUD.labelText) {
        [[AlertViewManager sharedInstance].displayedHUD hide:NO];
    } else {
        NSLog(@"attempt to hide Sync HUD");
    }
}

+ (MBProgressHUD *)showSyncHUDForWindow
{
    //avoid infinite HUD animating
    if ([DataUtils isCurrentChildDefault] ||
        [[ChildManager sharedInstance].currentChild.name isEqualToString:kDefaultChildName] ||
        //already displayed
        [AlertViewManager sharedInstance].displayedHUD.labelText /*||
        ![[ChildManager sharedInstance].currentChild.isDataLoaded boolValue] ||
        [[ChildManager sharedInstance].currentChild.isSyncCompleted boolValue]*/) {
        return nil;
    }
    
    NSLog(@"sync HUD displayed: %@", [AlertViewManager sharedInstance].displayedHUD);
    
    [[AlertViewManager sharedInstance].displayedHUD hide:NO];
    
    AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
    MBProgressHUD *HUD = [[MBProgressHUD alloc] initWithView:appDelegate.window];
    [AlertViewManager sharedInstance].displayedHUD = HUD;
    [appDelegate.window addSubview:HUD];
    [HUD show:NO];
    HUD.removeFromSuperViewOnHide = YES;
    HUD.labelText = NSLocalizedString(@"Synchronizing. Tap to cancel", nil);
    [HUD addGestureRecognizer:[[UITapGestureRecognizer alloc] initWithTarget:[HUD class] action:@selector(hudWasCancelled)]];

    return HUD;
}

- (void)updateWithProgress:(CGFloat)progress
{
    //uncomment to display progress of sending data
//    if (self.mode != MBProgressHUDModeDeterminate) {
//        self.mode = MBProgressHUDModeDeterminate;
//        self.detailsLabelText = NSLocalizedString(@"Sending data", nil);
//    }

    self.progress = progress;
}

+ (void)hudWasCancelled
{
    MBProgressHUD *HUD = [AlertViewManager sharedInstance].displayedHUD;

    //labelText is present just for sync HUD
    if ([HUD canBeHidden] && HUD.labelText) {
        NSLog(@"hudWasCancelled");
        [MBProgressHUD hideOnSyncFailure];
        [[SynchronizationManager sharedInstance] cancelSynchronization];
    }
}

+ (void)hideOnSyncCompletion
{
    MBProgressHUD *HUD = [AlertViewManager sharedInstance].displayedHUD;
    
    //labelText is present just for sync HUD
    if ([HUD canBeHidden] && HUD.labelText) {
        HUD.customView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"37x-Checkmark.png"]];
        HUD.mode = MBProgressHUDModeCustomView;
        HUD.labelText = NSLocalizedString(@"Synchronizing", nil);
        HUD.detailsLabelText = nil;
        [HUD hide:YES afterDelay:2];
    }
}

+ (void)hideOnSyncFailure
{
    MBProgressHUD *HUD = [AlertViewManager sharedInstance].displayedHUD;

    //labelText is present just for sync HUD
    if ([HUD canBeHidden] && HUD.labelText) {
        UIImage *image = [UIImage imageNamed:@"x-mark.png"];
        HUD.customView = [[UIImageView alloc] initWithImage:image];
        HUD.mode = MBProgressHUDModeCustomView;
        HUD.labelText = NSLocalizedString(@"Synchronizing", nil);
        HUD.detailsLabelText = nil;
        
        [HUD hide:YES afterDelay:2];
    }
}

- (BOOL)canBeHidden
{
    BOOL result = self.minShowTime == 0.0f &&
                  //completion or failure already shown
                  self.mode != MBProgressHUDModeCustomView;
    
//    if (!result) {
//        NSLog(@"ATTEMPT TO HIDE HUD, while it is already scheduled to hide");
//    }
    
    return result;
}

@end
