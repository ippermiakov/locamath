//
//  MTMovableView.m
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTMovableView.h"
#import "UIView+Transform.h"
#import "SimpleTapRecognizer.h"

#define FINGERMARGIN 40.0f

NSString * const kNotificationPutComponent          = @"kNotificationPutComponent";
NSString * const kNotificationChooseComponent       = @"kNotificationChooseComponent";

static CGPoint const kViewScale = {1.25f, 1.25f};
static CGFloat const kAnimationDuration = 0.1f;

@interface MTMovableView()

@property (unsafe_unretained, nonatomic) CGPoint touchLocationAtMoveStart;

@property (weak, nonatomic) UIView *parentView;

@end

@implementation MTMovableView

- (void)initialize
{
    SimpleTapRecognizer    *tapRecognizer = [[SimpleTapRecognizer    alloc] initWithTarget:self action:@selector(onBeginEndMoving:)];
    UIPanGestureRecognizer *panRecognizer = [[UIPanGestureRecognizer alloc] initWithTarget:self action:@selector(onMoving:)];
    tapRecognizer.delegate = self;
    panRecognizer.delegate = self;
    self.isMoveEnabled = YES;
    [self addGestureRecognizer:tapRecognizer];
    [self addGestureRecognizer:panRecognizer];
}

- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldRecognizeSimultaneouslyWithGestureRecognizer:(UIGestureRecognizer *)otherGestureRecognizer
{
    return YES;
}

- (void)encodeWithCoder:(NSCoder *)encoder
{
    [encoder encodeCGRect:self.frame forKey:@"frame"];
    [encoder encodeObject:self.carriedView forKey:@"carriedView"];
    [encoder encodeInteger:self.tag forKey:@"tag"];
}

- (id)initWithCoder:(NSCoder *)decoder
{
    if((self = [super init])) {
        self.frame       = [decoder decodeCGRectForKey:@"frame"];
        self.carriedView = [decoder decodeObjectForKey:@"carriedView"];
        self.tag         = [decoder decodeIntegerForKey:@"tag"];
        
        [self initialize];
    }
    return self;
}

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        [self initialize];
    }
    return self;
}

#pragma mark Gestures

- (void)onBeginEndMoving:(SimpleTapRecognizer *)tapRecognizer
{
    //retain self
    if (self.isMoveEnabled) {
        ^() {
            if (tapRecognizer.state == UIGestureRecognizerStateBegan) {                
                self.touchLocationAtMoveStart = [tapRecognizer locationInView:self];
                self.initialCenter = self.center;
                
                [self setCenter:CGPointMake(self.initialCenter.x, self.initialCenter.y - FINGERMARGIN)]; // set vertical margin, because of finger. 
                
                self.parentView = self.superview;
                
                CGRect rectInWindow = [self.superview convertRect:self.frame toView:self.overlayView];
                self.frame = rectInWindow;
                                
                [self.overlayView addSubview:self];
                
                if (self.onMoveBeganBlock) {
                    self.onMoveBeganBlock(self);
                }
                
                [UIView animateWithDuration:kAnimationDuration delay:0.0 options:UIViewAnimationOptionCurveEaseInOut animations:^ {
                    self.xscale = kViewScale.x;
                    self.yscale = kViewScale.y;
                } completion:nil];
            } else if (tapRecognizer.state == UIGestureRecognizerStateEnded) {
                self.xscale = 1.0f;
                self.yscale = 1.0f;

                CGRect rectInParent = [self.superview convertRect:self.frame toView:self.parentView];
                self.frame = rectInParent;
                                
                [self.parentView addSubview:self];
                
                if (self.onMoveEndedBlock) {
                    self.onMoveEndedBlock(self);
                }
                
                self.center = self.initialCenter;
            }
        }();
    }
}

- (void)onMoving:(UIPanGestureRecognizer *)panRecognizer
{
    if (self.isMoveEnabled) {
        ^() {  // This wierd block is retaining fix: without it, if callback block removes this view, we'll got crash, but block retains self
            if (panRecognizer.state == UIGestureRecognizerStateChanged || panRecognizer.state == UIGestureRecognizerStateBegan) {
                CGPoint touchLocation = [panRecognizer locationInView:self];
                
                CGRect frame = self.frame;
                frame.origin.x = self.frame.origin.x + touchLocation.x - self.touchLocationAtMoveStart.x;
                frame.origin.y = self.frame.origin.y + touchLocation.y - self.touchLocationAtMoveStart.y - FINGERMARGIN;
                self.frame = frame;
                
                if (self.onMoveBlock) {
                    self.onMoveBlock(self);
                }
                
                self.isOnMove = YES;
            } else if (panRecognizer.state == UIGestureRecognizerStateEnded) {
                self.isOnMove = NO;
            }
        }();
    }
}

- (UIView *)hitTest:(CGPoint)point withEvent:(UIEvent *)event {
    CGRect frame = CGRectInset(self.bounds, -self.hitAreaEnlarge.width, -self.hitAreaEnlarge.height);
    
    return CGRectContainsPoint(frame, point) ? self : nil;
}

#pragma mark - Setters & Getters

- (NSString *)text {
    if ([self.carriedView isKindOfClass:[UILabel class]]) {
        return [(UILabel *)self.carriedView text];
    } else if ([self.carriedView isKindOfClass:[UIButton class]]) {
        return [(UIButton *)self.carriedView titleForState:UIControlStateNormal];
    } else {
        return nil;
    }
}

- (void)setText:(NSString *)text {
    if ([self.carriedView isKindOfClass:[UILabel class]]) {
        [(UILabel *)self.carriedView setText:text];
        [self.carriedView sizeToFit];
        self.frame = (CGRect) {
            self.frame.origin, self.carriedView.frame.size
        };
    } else if ([self.carriedView isKindOfClass:[UIButton class]]) {
        [(UIButton *)self.carriedView setTitle:text forState:UIControlStateNormal];
        self.frame = (CGRect) {
            self.frame.origin, self.carriedView.frame.size
        };
    } else {
        return;
    }
}

- (void)setTag:(NSInteger)tag {
    [super setTag:tag];
    self.carriedView.tag = tag;
}

- (void)setCarriedView:(UIView *)carriedView {
    [_carriedView removeFromSuperview];
    _carriedView = carriedView;
    
    UIViewAutoresizing autoresizing =
    UIViewAutoresizingFlexibleLeftMargin  |
    UIViewAutoresizingFlexibleWidth       |
    UIViewAutoresizingFlexibleRightMargin |
    UIViewAutoresizingFlexibleTopMargin   |
    UIViewAutoresizingFlexibleHeight      |
    UIViewAutoresizingFlexibleBottomMargin;
    
    [_carriedView setAutoresizingMask:autoresizing];
    
    [self addSubview:_carriedView];
}

#pragma mark NSCopying proto

- (id)copyWithZone:(NSZone *)zone {
    MTMovableView *copy = [[MTMovableView allocWithZone:zone] init];
    
    copy.frame              = self.frame;
    copy.text               = self.text;
    copy.carriedView        = [NSKeyedUnarchiver unarchiveObjectWithData:[NSKeyedArchiver archivedDataWithRootObject:self.carriedView]];
    
    copy.isReturnOnMoveEnd  = self.isReturnOnMoveEnd;
    copy.isOnMove           = self.isOnMove;
    copy.toolsView          = self.toolsView;
    copy.onMoveBlock        = self.onMoveBlock;
    copy.onMoveEndedBlock   = self.onMoveEndedBlock;
    copy.onMoveBeganBlock   = self.onMoveBeganBlock;
    copy.overlayView        = self.overlayView;
    copy.tag                = self.tag;
    
    return copy;
}


@end