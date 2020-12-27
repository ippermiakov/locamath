//
//  SubActionView.m
//  Mathematic
//
//  Created by Developer on 18.12.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "SubActionView.h"
#import "MTMovableView.h"
#import "MTMovableViewCollection.h"

#define MAXSIZEFONT 40.0f
#define MINSIZEFONT 30.0f

#define EXPRESSION_OFFSET 35.0f
#define ANSWER_OFFSET 100.0f
#define AR_ANSWER_OFFSET 35.0f

@interface SubActionView ()<MTMovableViewCollectionDataSource>

@property (strong, nonatomic) MTMovableViewCollection *movableViewCollection;
@property (strong, nonatomic) UIButton *deleteButton;
@property (nonatomic, copy) NSString *subActionString;

@end

@implementation SubActionView

- (void)dealloc
{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

- (id)initWithType:(ActionType)type
{
    self = [super init];
    if (self) {
        // Initialization code
        [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(putComponent:) name:kNotificationPutComponent object:nil];
        self.theType = type;        
        if (type == kActionTypeSolution) {
            UISwipeGestureRecognizer *swipeGesture = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(swipedScreen:)];
            swipeGesture.numberOfTouchesRequired = 1;
            swipeGesture.direction = (UISwipeGestureRecognizerDirectionLeft | UISwipeGestureRecognizerDirectionRight);
            [self addGestureRecognizer:swipeGesture];
        }
    }
    return self;
}

#pragma mark - Gesture Recognizer methods

- (void)swipedScreen:(UISwipeGestureRecognizer*)swipeGesture
{
    self.deleteButton = nil;
    if (!self.isTaskCorrect && self.action.subActions.count > 1) {
        
        CGFloat deleteButtonPositionX = self.frame.size.width - 105.0f;
        
        self.deleteButton = [[UIButton alloc] initWithFrame:CGRectMake(deleteButtonPositionX, self.frame.size.height / 2.0f - 20.0f, 100.0f, 30.0f)];
        [self.deleteButton setTitle:NSLocalizedString(@"Delete", @"Delete subaction label at Solving page") forState:UIControlStateNormal];
        [self.deleteButton addTarget:self action:@selector(deleteMe:) forControlEvents:UIControlEventTouchUpInside];
        [self.deleteButton setBackgroundColor:[UIColor redColor]];
        [self addSubview:self.deleteButton];
    }
}

#pragma mark - Main methods

- (void)displaySupportObjects
{
    NSString *formatSubAction = @"%d)";
    CGSize textSize = [[NSString stringWithFormat:formatSubAction, self.index + 1] sizeWithFont:
                       [UIFont fontWithName:@"Helvetica-Bold" size:30]];
    
    CGFloat labelPosition_X = 0;
    CGFloat imagePosition_X = textSize.width;
    
    if ([DataUtils isArabicLocale]) {
        labelPosition_X = 0;
        imagePosition_X = textSize.width;
    }
    
    UILabel *label = [[UILabel alloc] initWithFrame:CGRectMake(labelPosition_X, self.frame.size.height / 2.0f - 12.0f, 30.0f, 30.0f)];
    [label setFont:[UIFont fontWithName:@"Helvetica-Bold" size:30]];
    [label setBackgroundColor:[UIColor clearColor]];
    [label setTextColor:[UIColor solvingToolsYellowColor]];
    
    [label setText:[NSString stringWithFormat:formatSubAction, self.index + 1]];
    label.tag = 1000;
    [self addSubview:label];
    UIImageView *imageView = [[UIImageView alloc] initWithFrame:CGRectMake(imagePosition_X, self.frame.size.height - 5.0f, self.frame.size.width - 30.0f, 2)];
    [imageView setBackgroundColor:[UIColor solvingToolsYellowColor]];
    [self addSubview:imageView];
}

- (void)drawAnswerLabel:(BOOL)flag
{
    if (flag) {
        
        NSString *formatAnswer = @"%@ :";
        CGFloat labelPosition_X = 0.0f;
        CGFloat imageWidthCorrection = 95.0f;
        // example - 1)
        CGFloat stepNumberSize = 0.0f;

        CGSize textSize = [[NSString stringWithFormat:formatAnswer, NSLocalizedString(@"Answer", nil)] sizeWithFont:
                           [UIFont fontWithName:@"Helvetica-Bold" size:22]];
        
        CGFloat imagePosition_X = textSize.width;
        //prepare data for Arabic
        if ([DataUtils isArabicLocale]) {
            stepNumberSize = 30.0f;
            labelPosition_X = self.frame.size.width - textSize.width;
            imagePosition_X = 0;
            imageWidthCorrection = textSize.width + stepNumberSize;
        }

        UILabel *label = [[UILabel alloc] initWithFrame:CGRectMake(labelPosition_X, 0, self.frame.size.width, self.frame.size.height)];
        [label setTextColor:[UIColor whiteColor]];
        [label setText:[NSString stringWithFormat:formatAnswer, NSLocalizedString(@"Answer", nil)]];
        [label setFont:[UIFont fontWithName:@"Helvetica-Bold" size:22]];
        [label setBackgroundColor:[UIColor clearColor]];
        CGFloat strikeWidth = textSize.width;
        [label setFrame:CGRectMake(labelPosition_X, 0, strikeWidth + 10, self.frame.size.height)];
        [self addSubview:label];
        UIImageView *imageView = [[UIImageView alloc] initWithFrame:CGRectMake(imagePosition_X + stepNumberSize, self.frame.size.height - 5.0f, self.frame.size.width - imageWidthCorrection, 2)];
        [imageView setBackgroundColor:[UIColor solvingToolsYellowColor]];
        [self addSubview:imageView];
    }
}

- (void)deleteMe:(id)sender
{
    if ([self.delegate respondsToSelector:@selector(deleteSubActionViewAtIndex:)]) {
        [self.delegate deleteSubActionViewAtIndex:self.index];
    }
    else {
        NSException *exeption = [[NSException alloc] initWithName:@"Delegate could not respond to selector" reason:@"deleteSubActionViewAtIndex: did not respond." userInfo:nil];
        [exeption raise];
    }
}

- (void)createComponentFromString:(NSString *)string
{
    self.subActionString = string;
    [self.movableViewCollection reloadData];
}

- (void)putComponent:(NSNotification *)notif
{
    MTMovableView *movableView = [notif object];
    if (movableView.tag >= 1000) {
           if (!self.isTaskCorrect) {
            
            //convert with respect to ActionView frame in order to distinguish different expressions/solutions
            CGRect movableViewRectConvertedToSelfSuperview = [[[self superview] superview] convertRect:movableView.frame fromView:[movableView superview]];
            CGRect selfRectConvertedToSuperview = [[[self superview] superview] convertRect:self.frame fromView:[self superview]];
            
            CGPoint point = CGPointZero;
            
            point.x = movableViewRectConvertedToSelfSuperview.origin.x + (movableViewRectConvertedToSelfSuperview.size.width / 2.0f);
            point.y = movableViewRectConvertedToSelfSuperview.origin.y + (movableViewRectConvertedToSelfSuperview.size.height / 2.0f);
            
            BOOL contains = CGRectContainsPoint(selfRectConvertedToSuperview, point);
            
            if (contains) {
                CGPoint movableViewCenterConvertedToMovableCollection = [self convertPoint:movableView.center fromView:[movableView superview]];
                
                NSUInteger indexOfExistingMovableView = [self.movableViewCollection movableViewIndexAtPoint:movableViewCenterConvertedToMovableCollection];
                //NSLog(@"movableViewCenterConvertedToMovableCollection: %@ indexOfExistingMovableView: %i", NSStringFromCGPoint(movableViewCenterConvertedToMovableCollection),  indexOfExistingMovableView);

                NSMutableString *stringToInsert = nil;
                NSString *stringToAdd = nil;
                
                if (NSNotFound != indexOfExistingMovableView) {
                    stringToInsert = [self.movableViewCollection.textRepresentation mutableCopy];
                    [stringToInsert insertString:movableView.text atIndex:indexOfExistingMovableView];
                } else {
                    stringToAdd = movableView.text;
                }
                
                NSLog(@"string to add/insert: %@", stringToInsert ? stringToInsert : stringToAdd);
                
                if (self.theType == kActionTypeAnswer) {
                    if (stringToInsert) {
                        [self.delegate didChangeAnswerComponent:stringToInsert];
                    }
                    else if (stringToAdd) {
                        [self.delegate addAnswerComponent:stringToAdd];
                    }
                } else {
                    //remove all not numbers&operations!
                    NSCharacterSet *numbersAndOperations = [NSCharacterSet characterSetWithCharactersInString:@"0123456789=+-*/()abcdefghijklmnopqrstuvwxyz"];

                    if (stringToInsert) {
                        NSString *filteredString = [stringToInsert stringByTrimmingCharactersInSet:[numbersAndOperations invertedSet]];
                        [self.delegate didChangeComponent:filteredString forSubActionWithIndex:self.index];
                    } else if (stringToAdd) {
                        NSString *filteredString = [stringToAdd stringByTrimmingCharactersInSet:[numbersAndOperations invertedSet]];
                        [self.delegate addComponent:filteredString subActionWithIndex:self.index];
                    }
                }
            }
        }
    }
}

#pragma mark - MTMovableCollectionDataSource

- (NSUInteger)numberOfRowsInCollection:(MTMovableViewCollection *)collection
{
    return [self.subActionString length];
}

- (MTMovableView *)collection:(MTMovableViewCollection *)collection viewAtIndex:(NSUInteger)index
{
    NSString *currentSymbol = [self.subActionString substringWithRange:NSMakeRange(index, 1)];
    UIFont *font = [UIFont fontWithName:@"Helvetica-Bold" size:MINSIZEFONT];
    CGSize size = [currentSymbol sizeWithFont:font constrainedToSize:collection.frame.size];
    
    UILabel *label = [[UILabel alloc] initWithFrame:(CGRect){CGPointZero, size}];
    MTMovableView *movableView = [[MTMovableView alloc] initWithFrame:(CGRect){CGPointZero, label.frame.size}];
    movableView.isMoveEnabled = !self.isTaskCorrect;
    
    movableView.carriedView = label;
    movableView.overlayView = [UIView overlayForStudyingAndExervices];
    
    movableView.tag = index;
    
    [label setBackgroundColor:[UIColor clearColor]];
    [label setTextColor:[UIColor solvingToolsYellowColor]];
    [label setFont:font];
    
    [movableView setText:currentSymbol];
    return movableView;
}

- (void)collection:(MTMovableViewCollection *)collection
       didMoveView:(MTMovableView *)movableView
           toIndex:(NSUInteger)index
{
    if (self.theType == kActionTypeAnswer) {
        [self.delegate didChangeAnswerComponent:collection.textRepresentation];
    } else {
        [self.delegate didChangeComponent:collection.textRepresentation forSubActionWithIndex:self.index];
    }
    [self.parentScrollView setScrollEnabled:YES];
}

- (void)collection:(MTMovableViewCollection *)collection removeView:(MTMovableView *)movableView atIndex:(NSUInteger)index
{
    if (self.theType == kActionTypeAnswer) {
        //to delete whole answer pass @""
        [self.delegate didChangeAnswerComponent:collection.textRepresentation];
    } else {
        [self.delegate didChangeComponent:collection.textRepresentation forSubActionWithIndex:self.index];
    }
    
    [self.parentScrollView setScrollEnabled:YES];
}

- (void)collection:(MTMovableViewCollection *)collection didStartMovingView:(MTMovableView *)movableView
{
    [self.parentScrollView setScrollEnabled:NO];
}

#pragma mark - Setters&Getters

- (MTMovableViewCollection *)movableViewCollection
{
    if (!_movableViewCollection) {
        CGPoint offsetPoint = (CGPoint){EXPRESSION_OFFSET, 0.0f};
        
        if (self.theType == kActionTypeAnswer) {
            offsetPoint.x = [DataUtils isArabicLocale] ? AR_ANSWER_OFFSET : ANSWER_OFFSET;
        }
        
        _movableViewCollection = [[MTMovableViewCollection alloc] initWithFrame:(CGRect){offsetPoint, self.frame.size}];
        _movableViewCollection.dataSource = self;
        _movableViewCollection.isRearrangingItemsOnMove = YES;
        
        [self addSubview:_movableViewCollection];
    }
    return _movableViewCollection;
}

@end
