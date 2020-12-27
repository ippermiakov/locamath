//
//  ActionView.m
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ActionView.h"
#import "Action.h"
#import "SubActionView.h"
#import "Task.h"
#import "GameManager.h"
#import "Level.h"
#import "Game.h"

typedef enum
{
    AlertTypeAdd,
    AlertTypeDelete
} AlertType;

@interface ActionView ()

@property (strong, nonatomic) SubActionView *subActionViewAnswer;
@property (strong, nonatomic) UIView *alertViewAdd;
@property (strong, nonatomic) UIView *alertViewDelete;
@property (strong ,nonatomic) UIImageView *errorImageView;
@property (strong ,nonatomic) UIButton *addSubAction;

@end

@implementation ActionView

- (id)initWithAction:(Action *)action
{
    self = [super init];
    if (self) {
        self.action = action;
        self.headerLabel = [[UILabel alloc] initWithFrame:CGRectMake(2, 22, 380, 30)];
        [self addSubview:self.headerLabel];
        
        
        if (self.action.error != kActionErrorTypeNone) {
            self.errorImageView = [[UIImageView alloc] initWithFrame:CGRectMake(535, 29, 9, 21)];
            self.errorImageView.image = [UIImage imageNamed:@"Mark-Mistake_v2@2x.png"];
            [self addSubview:self.errorImageView];
        } else if (self.action.answer.length > 0 &&
                   (self.action.task.status == kTaskStatusSolved || self.action.task.status == kTaskStatusSolvedNotAll)) {
            self.errorImageView = [[UIImageView alloc] initWithFrame:CGRectMake(532, 27, 26, 26)];
            self.errorImageView.image = [UIImage imageNamed:@"Mark-Done_v2@2x.png"];
            [self addSubview:self.errorImageView];
        }

        if ([action.task.solutions isEqualToString:kBothSolutionsType]) {
            self.addSubAction = [[UIButton alloc] initWithFrame:CGRectMake(480, 22, 35, 35)];
            [self.addSubAction setBackgroundImage:[UIImage imageNamed:@"Button-Add_Operation_v2@2x.png"] forState:UIControlStateNormal];
            [self.addSubAction addTarget:self action:@selector(addSubAction:) forControlEvents:UIControlEventTouchUpInside];
            [self.addSubAction setBackgroundColor:[UIColor clearColor]];
            [self addSubview:self.addSubAction];
        }

        self.alertViewAdd = [self alertOfType:AlertTypeAdd];
        self.alertViewDelete = [self alertOfType:AlertTypeDelete];
    }
    return self;
}

- (UIView *)alertOfType:(AlertType)type
{
    UIView *alertViewOperation = [[UIView alloc] initWithFrame:(CGRect){CGPointZero, 196.5f, 65.0f}];
    
    UIImageView *imageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, 196.5f, 65.0f)];
    [imageView setImage:[UIImage imageNamed:@"Alert_del_answer_operation@2x.png"]];
    [alertViewOperation addSubview:imageView];
    
    UIButton *doOperation = [[UIButton alloc] initWithFrame:CGRectMake(42.0f, 22.0f, 60, 30)];
    
    UILabel *btnLabelYES = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, 60, 30)];
    btnLabelYES.text = NSLocalizedString(@"YES", nil);
    btnLabelYES.textColor = [UIColor whiteColor];
    btnLabelYES.backgroundColor = [UIColor clearColor];
    
    [doOperation addSubview:btnLabelYES];
        
    [doOperation addTarget:self
                    action:(type == AlertTypeAdd) ? @selector(addOperation):@selector(deleteOperation)
          forControlEvents:UIControlEventTouchUpInside];
    [alertViewOperation addSubview:doOperation];
    
    UIButton *dontDoOperation = [[UIButton alloc] initWithFrame:CGRectMake(118.0f, 22.0f, 60, 30)];
    
    UILabel *btnLabelNO = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, 60, 30)];
    btnLabelNO.text = NSLocalizedString(@"NO", nil);
    btnLabelNO.textColor = [UIColor whiteColor];
    btnLabelNO.backgroundColor = [UIColor clearColor];
    
    [dontDoOperation addSubview:btnLabelNO];
        
    [dontDoOperation addTarget:self
                        action:(type == AlertTypeAdd) ? @selector(dontAddOperation):@selector(dontDeleteOperation)
              forControlEvents:UIControlEventTouchUpInside];
    
    [alertViewOperation addSubview:dontDoOperation];
    
    CGRect labelFrame = (type == AlertTypeAdd) ? CGRectMake(30.0f, 9.0f, 141.0f, 21.0f):
                                                 CGRectMake(35.0f, 9.0f, 140.0f, 21.0f);
    
    UILabel *label = [[UILabel alloc] initWithFrame:labelFrame];
    [label setTextColor:[UIColor whiteColor]];
    [label setBackgroundColor:[UIColor clearColor]];
    label.font = [UIFont systemFontOfSize:14];
    [label setText:(type == AlertTypeAdd) ? NSLocalizedString(@"Add operation?", @"Add action to Solving page"):NSLocalizedString(@"Delete operation?", @"Remove action from Solving page")];
    
    [alertViewOperation addSubview:label];
    
    return alertViewOperation;
}

- (CGFloat)drawSubActionsWithWidth:(CGFloat)width
{
    CGFloat pos_y = 0;
    NSInteger index = 0;
    
    BOOL isCorrect = NO;
    
    for (Action *subAction in self.subActions) {
        
        NSString *subActionString = subAction.string;
        SubActionView *subActionView = [[SubActionView alloc] initWithType:self.action.type];
        subActionView.action = self.action;
        
        //check if its task error
        if (subAction.parentAction.task == nil) {
            isCorrect = YES;
        } else if ([GameManager sharedInstance].statisticMode) {
            isCorrect = YES;
        } else {
            if (subAction.parentAction.task.status == kTaskStatusSolved) {
                isCorrect = YES;
            } else {
                isCorrect = [subAction.parentAction.isCorrect boolValue];
            }
        }
        
        subActionView.isTaskCorrect = isCorrect;
        
        subActionView.delegate = self;
        subActionView.index = index;
        
        if ([[self superview] isKindOfClass:[UIScrollView class]]) {
            subActionView.parentScrollView = (UIScrollView *)[self superview];
        }
        
        [subActionView setBackgroundColor:[UIColor clearColor]];
        [subActionView setFrame:CGRectMake(2, kActionViewHeaderHeight + kSubActionViewMargin + pos_y, width - 4, kSubActionViewHeight)];
        pos_y += kSubActionViewHeight + kSubActionViewMargin;
        
        if (![subActionString isEqualToString:@""]) {
            [subActionView createComponentFromString:subActionString];
        }
        
        [subActionView displaySupportObjects];
        [self addSubview:subActionView];
        index++;
    }
    
    Level *taskLevel = (Level *)self.action.task.level;
    
    if (![taskLevel.isTest boolValue]) {
        self.subActionViewAnswer = [[SubActionView alloc] initWithType:kActionTypeAnswer];
        
        self.subActionViewAnswer.isTaskCorrect = isCorrect;
        self.subActionViewAnswer.delegate = self;
        [self.subActionViewAnswer setBackgroundColor:[UIColor clearColor]];
        [self.subActionViewAnswer setFrame:CGRectMake(2, kActionViewHeaderHeight + pos_y + 2, width - 4, kSubActionViewHeight)];
        [self.subActionViewAnswer drawAnswerLabel:YES];
        
        if ([[self superview] isKindOfClass:[UIScrollView class]]) {
            self.subActionViewAnswer.parentScrollView = (UIScrollView *)[self superview];
        }
        
        if (![self.answer isEqualToString:@""]) {
            [self.subActionViewAnswer createComponentFromString:self.answer];
        }
        [self addSubview:self.subActionViewAnswer];
    }
    
    [self setFrame:CGRectMake(self.frame.origin.x, self.frame.origin.y, self.frame.size.width, pos_y + kActionViewHeaderHeight + kSubActionViewMargin)];
    return pos_y + kActionViewHeaderHeight + kSubActionViewMargin + kSubActionViewHeight + 2;
}

#pragma mark - Main View Actions

- (void)deleteMe:(id)sender
{
    if (self.action.error > 0) {
        [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"You can't delete error.", @"action remove error")];
        return;
    }
    [self closeAllAlert];
    CGRect rect = ( (UIView *)sender ).frame;    
    [self.alertViewDelete setFrame:(CGRect){rect.origin.x + rect.size.width - 5.0f, -10.0f, self.alertViewDelete.frame.size}];

    [self addSubview:self.alertViewDelete];
}

- (void)deleteOperation
{
    if ([self.delegate respondsToSelector:@selector(deleteAction:)]) {
        [self.delegate deleteAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"deleteAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)dontDeleteOperation
{
    [self.alertViewDelete removeFromSuperview];
}

- (void)addSubAction:(id)sender
{
    [self closeAllAlert];
    
    if (![self.action.isCorrect boolValue] && [self shouldAddSolutins]) {
        [self addOperation];
    }
}

- (void)addOperation
{
    if ([self.delegate respondsToSelector:@selector(addSubActionToAction:)]) {
        [self.delegate addSubActionToAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addSubActionToAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)dontAddOperation
{
    [self.alertViewAdd removeFromSuperview];
}

- (void)closeAllAlert
{
    [self.alertViewAdd removeFromSuperview];
    [self.alertViewDelete removeFromSuperview];
}

#pragma mark SubActionView delegate mehtods

- (void)deleteSubActionViewAtIndex:(NSInteger)index
{
    if ([self.delegate respondsToSelector:@selector(deleteSubActionViewWithIndex:forAction:)]) {
        [self.delegate deleteSubActionViewWithIndex:index forAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"deleteSubActionViewWithIndex:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex
{
    if ([self.delegate respondsToSelector:@selector(addComponent:subActionWithIndex:forAction:)]) {
        [self.delegate addComponent:component subActionWithIndex:subActionIndex forAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addComponent:subActionWithIndex:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)addAnswerComponent:(NSString *)component
{
    if ([self.delegate respondsToSelector:@selector(addAnswerWithComponent:forAction:)]) {
        [self.delegate addAnswerWithComponent:component forAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addAnswerForActionWithIndex:withComponent: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)didChangeComponent:(NSString *)component forSubActionWithIndex:(NSInteger)subActionIndex
{
    if ([self.delegate respondsToSelector:@selector(didChangeComponent:withSubActionIndex:forAction:)]) {
        [self.delegate didChangeComponent:component withSubActionIndex:subActionIndex forAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"didChangeComponent:withSubActionIndex:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)didChangeAnswerComponent:(NSString *)component
{
    if ([self.delegate respondsToSelector:@selector(didChangeAnswerComponent:forAction:)]) {
        [self.delegate didChangeAnswerComponent:component forAction:self.action];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"didChangeAnswerComponent:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

#pragma mark - Helper

- (BOOL)shouldAddSolutins
{
    BOOL shouldAdd = YES;
    NSArray *solutins = [[self.action.task.actions allObjects] select:^BOOL(Action *action) {
        return action.type == kActionTypeSolution;
    }];
    
    NSArray *expression = [[self.action.task.actions allObjects] select:^BOOL(Action *action) {
        return action.type == kActionTypeExpression;
    }];

    
    if (solutins.count == [self.action.task.expressions count] && expression.count == 0) {
        shouldAdd  = NO;
    }
    
    if (self.action.type == kActionTypeExpression && shouldAdd) {
        shouldAdd = NO;
    }
    
    return shouldAdd;
}

- (void)updateActionContentIfNeeded
{
    CGFloat arabicCorrectionSpace = 0;
    
    if ([DataUtils isArabicLocale]) {
        self.headerLabel.tag = kRightTextPositionsTag;
        self.headerLabel.frame = CGRectMake(2, 22, 553, 30);
        CGSize textSize = [self.headerLabel.text sizeWithFont:
                           [UIFont fontWithName:@"Helvetica-Bold" size:22]];
        arabicCorrectionSpace = textSize.width > 0 ? textSize.width + 10 : 180;
    }
    
    if (self.action.error != kActionErrorTypeNone) {
        self.errorImageView.frame = CGRectMake(535 - arabicCorrectionSpace, 29, 9, 21);
    } else if (self.action.answer.length > 0 &&
               (self.action.task.status == kTaskStatusSolved || self.action.task.status == kTaskStatusSolvedNotAll)) {
        self.errorImageView.frame = CGRectMake(532 - arabicCorrectionSpace, 27, 26, 26);
    }
    
    if (self.addSubAction) {
        self.addSubAction.frame = CGRectMake(480 - arabicCorrectionSpace, 22, 35, 35);
    }

}

@end