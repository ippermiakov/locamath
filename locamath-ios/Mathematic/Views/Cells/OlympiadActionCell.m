//
//  OlympiadActionCell.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 27.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "OlympiadActionCell.h"
#import "MTMovableView.h"
#import "MTMovableViewCollectionWrapper.h"
#import "OlympiadTask.h"
#import "OlympiadHint.h"
#import "OlympiadAction.h"
#import "UILabel+Mathematic.h"

@interface OlympiadActionCell ()

@property(nonatomic, strong) NSMutableArray *hintLabels;
@property(nonatomic, strong) NSMutableArray *answerViews;
@property(nonatomic, strong) NSString *placeholder;

@end


@implementation OlympiadActionCell

#pragma mark -

- (void)initialize
{
    self.cellType = ActionCellEditing;
    self.answerViews  = [NSMutableArray new];
    self.hintLabels   = [NSMutableArray new];
}

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        [self initialize];
    }
    return self;
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:aDecoder];
    if (self) {
        [self initialize];
    }
    return self;
}

- (void)dealloc
{
//    NSLog(@"clean action cell for task #%@ for level %@", self.task.identifier, [self.task.level identifier]);
}

#pragma mark - Helper

- (UILabel *)labelWithText:(NSString *)text andFrame:(CGRect *)frame
{
    UILabel *label = [UILabel olympiadTasksLabelWithText:text
                                             placeholder:self.placeholder
                                           withAlignment:self.task.alignmentType];
    
    CGRect textFittedFrame = label.frame;
    textFittedFrame.origin = (*frame).origin;
    label.frame = textFittedFrame;

    (*frame) = textFittedFrame;
    (*frame).origin.x = CGRectGetMaxX(*frame) + 15;
    
    return label;
}

- (void)generateHintAnswerViews
{
    __block CGRect labelFrame = CGRectZero;
    
    [self.hintLabels makeObjectsPerformSelector:@selector(removeFromSuperview)];
    [self.hintLabels removeAllObjects];

    [self.hints enumerateObjectsUsingBlock:^(OlympiadHint *hint, NSUInteger idx, BOOL *stop) {
        if (hint.hintString.length > 0) {
            UILabel *hintLabel = [self.hintLabels match:^BOOL(id obj) {
                return [obj tag] == idx;
            }];
            
            if (!hintLabel) {
                hintLabel = [self labelWithText:hint.hintString andFrame:&labelFrame];
                hintLabel.tag = idx;
                [self addSubview:hintLabel];
                [self.hintLabels addObject:hintLabel];
            }
        } else if ([hint.hasUserInput boolValue]) {
            
            NSString *text = @"";
            
            if (hint.userInput != nil && [hint.userInput length]) {
                text = hint.userInput;
            }
                        
            NSString *labelText = [text isEqualToString:@""] ? self.placeholder:text;
            
            UILabel *answerLabel = [self.answerViews match:^BOOL(id obj) {
                return [obj tag] == idx && [[obj class] isKindOfClass:[UILabel class]];
            }];
            
            if (!answerLabel) {
                answerLabel = [self labelWithText:labelText andFrame:&labelFrame];
                answerLabel.tag = idx;
                
                if (self.cellType != ActionCellEditing) {
                    [self.answerViews addObject:answerLabel];
                    [self addSubview:answerLabel];
                }
            }
            
            if (self.cellType == ActionCellEditing) {
                MTMovableViewCollectionWrapper *answerView = [self.answerViews match:^BOOL(id obj) {
                    return [obj tag] == idx;
                }];
                
                if (!answerView) {
                    MTMovableViewCollectionWrapper *answerView = [[MTMovableViewCollectionWrapper alloc] initWithFrame:answerLabel.frame];
                    answerView.task = self.task;
                    
                    answerView.isTaskCompleted = self.task.isCorrect;
                    answerView.numberToDisplay = [[[self.task.actions anyObject] numOfToolsToFill] integerValue];
                    
                    __weak OlympiadActionCell *weakSelf = (OlympiadActionCell *)self;
                    
                    answerView.didStartMoveBlock = ^(){
                        if (weakSelf.scrollingDisablingBlock) {
                            weakSelf.scrollingDisablingBlock();
                        }
                    };
                    
                    answerView.didEndMoveBlock = ^(){
                        if (weakSelf.scrollingEnablingBlock) {
                            weakSelf.scrollingEnablingBlock();
                        }
                    };
                    
                    answerView.delegate = self;
                    answerView.tag = idx;
                    answerView.placeholder = self.placeholder;
                    answerView.text = text;
                                        
                    [self addSubview:answerView];
                    [self.answerViews addObject:answerView];
                } else {
                    answerView.frame = answerLabel.frame;
                    answerView.text = text;
                }
            }
        }
    }];
    
    if (self.didReloadBlock) self.didReloadBlock();
}

#pragma mark Setters & Getters

- (void)setHints:(NSArray *)hints
{
    _hints = hints;
    
    [self.hintLabels makeObjectsPerformSelector:@selector(removeFromSuperview)];
    [self.answerViews makeObjectsPerformSelector:@selector(removeFromSuperview)];
    
    [self.hintLabels removeAllObjects];
    [self.answerViews removeAllObjects];
        
    [self generateHintAnswerViews];
}

- (void)setTask:(OlympiadTask *)task
{
    _task = task;
    
    NSString *placeholder = @"";

    for (NSInteger i = 0, end = self.task.longestToolsLength; i < end; ++i) {
        placeholder = [placeholder stringByAppendingString:@"_"];
    }
    
    self.placeholder = placeholder;
}

#pragma mark - MTMovableCollectionWrapperDelegate

- (void)collectionWrapper:(MTMovableViewCollectionWrapper *)wrapper didChangeTextToNew:(NSString *)text
{
    NSUInteger idx = [self.answerViews indexOfObjectIdenticalTo:wrapper];
    
    NSArray *answerHints = [self.hints select:^BOOL(OlympiadHint *hint) {
        return [hint.hasUserInput boolValue];
    }];
    
    if (idx < [answerHints count]) {
        OlympiadHint *hint = answerHints[idx];
        
        if ([hint.hasUserInput boolValue]) {
            [hint setUserInput:text];
        }
    }
    
    [self generateHintAnswerViews];
}

- (InsertionType)insertionTypeOfCollectionWrapper:(MTMovableViewCollectionWrapper *)wrapper
{
    OlympiadHint *hint = self.hints.lastObject;
    
    if ([hint.action.numOfToolsToFill integerValue] == 1) {
        return InsertionTypeReplace;
    } else {
        return InsertionTypeAdd;
    }
}

@end
