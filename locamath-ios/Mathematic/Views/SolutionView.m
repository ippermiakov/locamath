//
//  SolutionView.m
//  Mathematic
//
//  Created by Developer on 17.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "SolutionView.h"
#import "Action.h"

@interface SolutionView ()

- (void)drawActions;

@end

@implementation SolutionView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

#pragma mark - Setup methods

- (void)setDataSource:(id <SolutionViewDataSource>)dataSource
{
    _dataSource = dataSource;
    [self drawActions];
}

- (void)drawActions
{
    CGFloat pos_y = 15.0f;
    CGFloat actionHeight = 0.0f;
    NSArray *actions = [_dataSource fetchedActions];
    
    for (Action *action in actions) {
        ActionView *actionView = [_dataSource createActionViewWithAction:action];
        actionView.delegate = self;
        [self addSubview:actionView];
        actionHeight = [actionView drawSubActionsWithWidth:self.frame.size.width];
        [actionView setFrame:CGRectMake(0.0f, pos_y, self.frame.size.width, actionHeight)];
        pos_y += actionHeight + kActionViewMargin;
        
    }
    
    [self setContentSize:CGSizeMake(self.frame.size.width, pos_y)];
}

- (void)cleanView
{
    for (UIView *v in[self subviews]) {
        [v removeFromSuperview];
    }
}

#pragma mark - Reload methods

- (void)reloadData
{
    [self cleanView];
    [self drawActions];
    [self.delegateSolution setNeedsFont];
}

- (void)reloadDataWithAnimation
{
    [self reloadData];
    
    if (self.contentSize.height - self.frame.size.height < 0.0f) {
        [self setContentOffset:CGPointMake(0.0f, 0.0f)];
    } else {
        [self setContentOffset:CGPointMake(0.0f, self.contentSize.height - self.frame.size.height) animated:YES];
    }
}

#pragma mark - ActionView delegate methods

- (void)deleteAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(deleteAction:)]) {
        [self.delegateSolution deleteAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"deleteAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)addSubActionToAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(addSubActionToAction:)]) {
        [self.delegateSolution addSubActionToAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addSubActionToAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)deleteSubActionViewWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(deleteSubActionWithIndex:forAction:)]) {
        [self.delegateSolution deleteSubActionWithIndex:subActionIndex forAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"deleteSubActionWithIndex:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(addComponent:subActionWithIndex:forAction:)]) {
        [self.delegateSolution addComponent:component subActionWithIndex:subActionIndex forAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addComponent:subActionWithIndex:forActionWithIndex: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)addAnswerWithComponent:(NSString *)component forAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(addAnswerWithComponent:forAction:)]) {
        [self.delegateSolution addAnswerWithComponent:component forAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"addComponent:subActionWithIndex:forActionWithIndex: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)didChangeComponent:(NSString *)component withSubActionIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(didChangeComponent:withSubActionIndex:forAction:)]) {
        [self.delegateSolution didChangeComponent:component withSubActionIndex:subActionIndex forAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"didChangeComponent:withSubActionIndex:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)didChangeAnswerComponent:(NSString *)component forAction:(Action *)action
{
    if ([self.delegateSolution respondsToSelector:@selector(didChangeAnswerComponent:forAction:)]) {
        [self.delegateSolution didChangeAnswerComponent:component forAction:action];
        [self reloadData];
    } else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"didChangeAnswerComponent:forAction: did not respond." userInfo:nil];
        [exeption raise];
    }
}


@end
