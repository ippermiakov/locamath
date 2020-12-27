//
//  SolvingSchemesViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 19.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "Task.h"
#import "MTToolsView.h"

@protocol SolvingSchemesViewControllerDelegate <NSObject>

- (void)needReload;

@end

@interface SolvingSchemesViewController : BaseViewController

@property (strong, nonatomic) MTToolsView *toolsView;
@property (weak, nonatomic) id<SolvingSchemesViewControllerDelegate> delegate;
@property (strong, nonatomic) Task *task;

@end
