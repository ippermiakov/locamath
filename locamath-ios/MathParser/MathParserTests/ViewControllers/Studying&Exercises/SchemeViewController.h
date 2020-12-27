//
//  SchemeViewController.h
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"

@class MTToolsView;
@class Task;
@class ObjectiveView;

typedef void(^OnDoneBlock)();

@interface SchemeViewController : BaseViewController<UIGestureRecognizerDelegate> {
    NSInteger       maxSolutions;
}

@property (weak, nonatomic)   IBOutlet MTToolsView *toolsView;
@property (strong, nonatomic) IBOutlet UIView   *schemes;
@property (weak, nonatomic)   IBOutlet UILabel  *labelTitle;

@property (strong, nonatomic) Task              *task;
@property (strong, nonatomic) NSString          *numberTask;
@property (strong, nonatomic) ObjectiveView     *objective;

@property (copy, nonatomic) OnDoneBlock onDoneBlock;

- (id)initWithTask:(Task *)task andTaskNumber:(NSString *)taskNumber;

- (IBAction)onTapSaveAndExit:(id)sender;

@end
