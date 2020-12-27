//
//  OlympiadNextTaskPopupViewController.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 16.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"
#import "OlympiadTask.h"

typedef enum {
    NextActionRedo,
    NextActionTask,
    NextActionBack
} NextTaskActionType;

@interface OlympiadNextTaskPopupViewController : PresentableViewController

@property(nonatomic, unsafe_unretained) NextTaskActionType actionType;
@property (unsafe_unretained, nonatomic) BOOL isFailPopup;
@property (strong, nonatomic) OlympiadTask * task;

- (IBAction)onTapRedo:(id)sender;
- (IBAction)onTapNextTask:(id)sender;
- (IBAction)onTapBack:(id)sender;

@end
