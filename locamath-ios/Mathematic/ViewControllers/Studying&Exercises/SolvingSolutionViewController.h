//
//  SolvingSolutionViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 18.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"
#import "SolutionView.h"
#import "TaskError.h"

@protocol SolvingSolutionViewControllerDelegate <NSObject>
- (void)didChangeComponent;
@end

@interface SolvingSolutionViewController : BaseViewController <SolutionViewDataSource, SolutionViewDelegate>

@property (strong, nonatomic) TaskError *taskError;
@property (weak, nonatomic) id<SolvingSolutionViewControllerDelegate> delegate;

- (id)initWithAchievement:(id)achievement;
- (void)reloadSolvingPage;

@end
