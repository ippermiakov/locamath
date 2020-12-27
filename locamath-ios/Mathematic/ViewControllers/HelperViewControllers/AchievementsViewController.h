//
//  AchievementsViewController.h
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "BaseViewControllerDelegate.h"

@interface AchievementsViewController : BaseViewController

@property (strong, nonatomic) UITableView *tableView;
@property (weak, nonatomic) BaseViewController<BaseViewControllerDelegate> *viewControllerToPresentContent;

- (void)reloadData;

@end
