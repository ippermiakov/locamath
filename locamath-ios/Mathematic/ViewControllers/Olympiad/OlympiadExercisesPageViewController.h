//
//  ExercisesPage.h
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"
#import "PopUpControllerDelegate.h"

@class OlympiadLevel;
@class OlympiadTask;

@interface OlympiadExercisesPageViewController : BaseViewController

@property (strong, nonatomic) OlympiadLevel *level;
@property (strong, nonatomic) OlympiadTask  *task;

- (IBAction)onTapStartSolve:(id)sender;
- (IBAction)onTapBackHome:(id)sender;
- (IBAction)onTapSolutionButton:(id)sender;
- (IBAction)onTapErrorButton:(id)sender;
- (IBAction)onTapNextTask:(id)sender;
- (IBAction)onButtonHelp:(id)sender;


@end
