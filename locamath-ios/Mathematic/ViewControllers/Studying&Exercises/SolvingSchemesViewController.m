//
//  SolvingSchemesViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 19.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SolvingSchemesViewController.h"
#import "MTToolsView.h"
#import "ObjectiveView.h"
#import "MTMovableView.h"
#import "DistanceCalculation.h"
#import "Scheme.h"
#import "SchemeElement.h"
#import "Child.h"
#import "Task.h"

#define SCHEME_CORRECTION_X 44
#define SCHEME_CORRECTION_Y 58

@interface SolvingSchemesViewController ()

@property (unsafe_unretained, nonatomic) NSInteger counterPutComponent;

@property (strong, nonatomic) NSMutableArray *movableViewsForTask;
@property (strong, nonatomic) IBOutlet UIView *schemesView;

@end

@implementation SolvingSchemesViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(chooseComponent:)
                                                     name:kNotificationChooseComponent
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(putComponent:)
                                                     name:kNotificationPutComponent
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(reloadViews:)
                                                     name:kNotificationReloadComponents
                                                   object:nil];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    [self reloadBoardView];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Notification

- (void)chooseComponent:(NSNotification *)notification
{
    MTMovableView *movableView = [notification object];

    if (movableView.tag < 1000) {
        [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[8] loop:NO];
    
        //avoid issues with few tools selection
        [self moveToolsComponentsExceptView:movableView enabled:NO];
    }
}

- (void)putComponent:(NSNotification *)notification
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[9] loop:NO];
    
    MTMovableView *movableView = [notification object];
    
    if (movableView.tag < 1000) {
        self.counterPutComponent++;
        
        if ([self.schemesView.subviews containsObject:movableView]) {
            
        } else {
            
            CGRect convertedRect = [movableView.superview convertRect:movableView.frame toView:self.schemesView];
            CGPoint point;
            
            point.x = CGRectGetMidX(convertedRect);
            point.y = CGRectGetMidY(convertedRect);
            
            BOOL contains = CGRectContainsPoint(self.schemesView.frame, [self.schemesView convertPoint:point toView:self.view.superview]);
            
            if (contains) {
                
                NSArray *schemeElementsView = [[self.schemesView subviews] select:^BOOL(MTMovableView *obj) {
                    return obj.tag == movableView.tag;
                }];
                
                __block MTMovableView *viewWithMinDistance = nil;
                __block CGFloat distance = 0;
                
                [schemeElementsView enumerateObjectsUsingBlock:^(MTMovableView *obj, NSUInteger idx, BOOL *stop) {
                    CGFloat temp = CGRectGetDistance(obj.frame, convertedRect);
                    
                    if (distance == 0) {
                        distance = temp;
                        viewWithMinDistance = obj;
                    } else if (distance > temp) {
                        distance = temp;
                        viewWithMinDistance = obj;
                    }
                    
                }];
                
                SchemeElement *schemeElement = [[[self taskScheme] elements] match:^BOOL(SchemeElement *obj) {
                    return [obj.position_x integerValue] - SCHEME_CORRECTION_X == (NSInteger)viewWithMinDistance.frame.origin.x && [obj.position_y integerValue] + SCHEME_CORRECTION_Y == (NSInteger)viewWithMinDistance.frame.origin.y && viewWithMinDistance.tag == [obj.typeNumber integerValue];
                }];
                
                SchemeElement *nextEleme = nil;
                
                NSArray *elements = [self sortedElements];
                
                NSArray *showedView = [elements select:^BOOL(SchemeElement *schemeElement) {
                    return [schemeElement.isFilled boolValue];
                }];
                
                NSInteger indeForNext = 0;
                
                if (showedView.count) {
                    indeForNext = [elements indexOfObject:showedView.lastObject] + 1;
                }
                
                if (indeForNext < elements.count) {
                    nextEleme = [elements objectAtIndex:indeForNext];
                } else {
                    nextEleme = elements.lastObject;
                }
                
                if (schemeElement && [schemeElement.identifier integerValue] == [nextEleme.identifier integerValue]) {
                    schemeElement.isFilled = @YES;
                    self.counterPutComponent = 0;
                }
                
                if (self.counterPutComponent == 3) {
                    [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Mistake put scheme message", nil)];
                    self.counterPutComponent = 0;
                }
            }
        }
        
        [self moveToolsComponentsExceptView:movableView enabled:YES];
    }
}

- (void)moveToolsComponentsExceptView:(MTMovableView *)inView enabled:(BOOL)isEnabled
{
    [self.toolsView.displayedTools each:^(MTMovableView *view) {
        if (view.tag != inView.tag) {
            view.isMoveEnabled = isEnabled;
        }
    }];
}

#pragma mark - Helper

- (void)reloadViews:(NSNotification *)notification
{
    MTMovableView *obj = notification.object;
    if (obj.tag < 1000) {
        
        [self reloadBoardView];
        
        if ([self.delegate respondsToSelector:@selector(needReload)]) {
            [self.delegate needReload];
        }
    }
}

- (NSArray *)movableViews
{
    
    NSSet *notFilledElementTypes = [self.taskScheme.elements  valueForKey:@"typeNumber"];
    
    NSArray *selectedTools = [self.movableViewsForTask select:^BOOL(MTMovableView *view) {
        return [notFilledElementTypes containsObject:@(view.tag)];
    }];
    
    return selectedTools;
}

- (NSMutableArray *)movableViewsForTask
{
    if (!_movableViewsForTask) {
        NSSet *schemeElementTypes = [self.taskScheme.elements valueForKey:@"typeNumber"];
        
        NSArray *selectedTools = [self.toolsView.displayedTools select:^BOOL(MTMovableView *view) {
            return [schemeElementTypes containsObject:@(view.tag)];
        }];
        
        _movableViewsForTask = [[NSMutableArray alloc] initWithArray:selectedTools
                                                           copyItems:YES];
        
        //sort by width
        [_movableViewsForTask sortUsingComparator:^NSComparisonResult(MTMovableView *obj1, MTMovableView *obj2) {
            return obj1.frame.size.width > obj2.frame.size.width;
        }];
    }
    
    return _movableViewsForTask;
}

- (void)reloadBoardView
{
    __block BOOL isAllItemsSet = YES;
    
    NSArray *showedView = [[self.taskScheme.elements allObjects] select:^BOOL(SchemeElement *schemeElement) {
        return [schemeElement.isFilled boolValue];
    }];
    
    [[self.schemesView subviews] makeObjectsPerformSelector:@selector(removeFromSuperview)];
    
    NSArray *elements = [self sortedElements];
    
    [elements each:^(SchemeElement *senderElement) {
        
        [[self movableViewsForTask] enumerateObjectsUsingBlock:^(MTMovableView *view, NSUInteger idx, BOOL *stop) {
            
            if (view.tag == [senderElement.typeNumber integerValue]) {
                
                CGRect viewAtBoardRect = CGRectMake([senderElement.position_x integerValue] - SCHEME_CORRECTION_X, [senderElement.position_y integerValue] + SCHEME_CORRECTION_Y,
                                                    view.frame.size.width, view.frame.size.height);
                
                MTMovableView *viewAtBoard = [self.schemesView.subviews match:^BOOL(MTMovableView *obj) {
                    return CGRectEqualToRect(obj.frame, viewAtBoardRect);
                }];
                
                if (!viewAtBoard) {
                    viewAtBoard = [view copy];
                    
                    viewAtBoard.isMoveEnabled = NO;
                    viewAtBoard.frame = viewAtBoardRect;
                    [self.schemesView addSubview:viewAtBoard];
                    
                    NSArray *addedView = [[self.schemesView subviews] select:^BOOL(UIView *view) {
                        return [view isKindOfClass:[MTMovableView class]];
                    }];
                    
                    if ([addedView count] <= [showedView count] + 1 && [self isTaskTrening]) {
                        viewAtBoard.hidden = NO;
                    } else if ([addedView count] <= [showedView count]){
                        viewAtBoard.hidden = NO;
                    } else viewAtBoard.hidden = YES;
                }
                if ([self isTaskTrening]) {
                    
                    if (![senderElement.isFilled boolValue]) {
                        viewAtBoard.alpha = 0.0f;
                        
                        [UIView animateWithDuration:1.5 animations:^{
                            viewAtBoard.alpha = 0.5f;
                        }];
                        
                        isAllItemsSet = NO;
                    } else {
                        viewAtBoard.alpha = 1;
                    }
                }
            }
        }];
    }];
}

- (BOOL)isTaskTrening
{
    return [[[[[self.task.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
            isEqualToString:@"1"];
}

- (NSArray *)sortedElements
{
    return [[[self taskScheme].elements allObjects] sortedArrayUsingComparator:^NSComparisonResult(SchemeElement *obj1, SchemeElement *obj2) {
        return [obj1.identifier integerValue] > [obj2.identifier integerValue];
    }];
}

#pragma mark - Setters&Getters

- (Scheme *)taskScheme
{
    Scheme *scheme = [self.task.child.schemes match:^BOOL(Scheme *obj) {
        return [obj.identifier isEqualToString:self.task.identifier];
    }];
    
    return scheme;
}

@end
