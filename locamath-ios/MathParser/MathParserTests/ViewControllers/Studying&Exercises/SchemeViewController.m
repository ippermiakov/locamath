//
//  SchemeViewController.m
//  Mathematic
//
//  Created by Developer on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SchemeViewController.h"
#import "MTToolsView.h"
#import "ObjectiveView.h"
#import "MTMovableView.h"
#import "DistanceCalculation.h"
#import "Scheme.h"
#import "SchemeElement.h"
#import "Child.h"
#import "Task.h"

static NSInteger const kImageSizeWidth = 407;
static NSInteger const kImageSizeHeight = 374;
static NSInteger const kImageMovesScreenX = 160;
static NSInteger const kImageMovesScreenY = 280;

@interface SchemeViewController ()

@property (strong, nonatomic) NSMutableDictionary *dataSources;
@property (strong, nonatomic) IBOutlet UIView *boardView;
@property (strong, nonatomic) NSMutableArray *movableViewsForTask;
@property (weak, nonatomic) IBOutlet UIButton *doneButton;
@property (readonly, nonatomic) NSArray *movableViewsForTools;
@property (strong, nonatomic) NSArray *tools;
@property (unsafe_unretained) NSInteger counterPutComponent;
@property (strong, nonatomic) IBOutlet UIView *schemeBackgroundView;

- (IBAction)onDone:(id)sender;

@end


@implementation SchemeViewController

- (id)initWithTask:(Task *)task andTaskNumber:(NSString *)taskNumber
{
    self = [super init];
    if (self) {
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(putComponent:)
                                                     name:kNotificationPutComponent
                                                   object:nil];
        
        self.task = task;
        self.numberTask = taskNumber;
        
        [self setActualFonts];
        self.counterPutComponent = 0;
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    
    self.objective = [[ObjectiveView alloc] initWithTask:self.task];
    
    [self.view addSubview:self.objective];
    
    //hide all board view
    [[self movableViewsForTask] each:^(MTMovableView *sender) {
        sender.hidden = YES;
    }];
    
    self.tools = [[NSArray alloc] initWithArray:self.movableViewsForTask copyItems:YES];
    
    [self reloadToolsView];
    [self reloadBoardView];
    
    NSInteger count = [self.task.expressions count];
    maxSolutions = count;
    
    if ([self.task.solutions isEqualToString:kBothSolutionsType]) {
        [self.labelTitle setText:[NSString stringWithFormat:@"%@ #%@ (%@ %d %@ %@ %d)",
                                  NSLocalizedString(@"Task", nil),
                                  self.task.numberTask,
                                  NSLocalizedString(@"expression", nil),
                                  count, NSLocalizedString(@"and", nil),
                                  NSLocalizedString(@"solution", nil),
                                  count]];
        
        maxSolutions = maxSolutions * 2;
    }
    else {
        [self.labelTitle setText:[NSString stringWithFormat:@"%@ #%@ (%@ %d)",
                                  NSLocalizedString(@"Task", nil),
                                  self.task.numberTask,
                                  NSLocalizedString(@"expression", nil),
                                  count]];
    }
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];

    self.toolsView.overlayView = [UIView overlayForStudyingAndExervices];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setToolsView:nil];
    [self setSchemes:nil];
    [self setBoardView:nil];
    [self setDoneButton:nil];
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onTapSaveAndExit:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)onDone:(id)sender
{
    self.schemeBackgroundView.hidden = NO;
    
    [self saveSchemeImage];
    
    if (self.onDoneBlock) {
        self.onDoneBlock();
    }
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (void)putComponent:(NSNotification *)notification
{
    MTMovableView *movableView = [notification object];
    self.counterPutComponent++;
    if ([self.schemes.subviews containsObject:movableView]) {
        
    } else {
        
        CGRect convertedRect = [movableView.superview convertRect:movableView.frame toView:self.schemes];
        CGPoint point;
        
        point.x = CGRectGetMidX(convertedRect);
        point.y = CGRectGetMidY(convertedRect);
        
        BOOL contains = CGRectContainsPoint(self.schemes.frame, [self.schemes convertPoint:point toView:self.view]);
        
        if (contains) {
            movableView.frame = convertedRect;
            
            MTMovableView *movableViewCopy = movableView.copy;
            
            NSArray *schemeElementsView = [[self.boardView subviews] select:^BOOL(MTMovableView *obj) {
                return obj.tag == movableViewCopy.tag;
            }];
            
            __block MTMovableView *viewWithMinDistance = nil;
            __block CGFloat distance = 0;
            
            [schemeElementsView enumerateObjectsUsingBlock:^(MTMovableView *obj, NSUInteger idx, BOOL *stop) {
                CGFloat temp = CGRectGetDistance(obj.frame, movableViewCopy.frame);
                
                if (distance == 0) {
                    distance = temp;
                    viewWithMinDistance = obj;
                } else if (distance > temp) {
                    distance = temp;
                    viewWithMinDistance = obj;
                }
                
            }];
            
            SchemeElement *schemeElement = [[[self taskScheme] elements] match:^BOOL(SchemeElement *obj) {
                return [obj.position_x integerValue] == (NSInteger)viewWithMinDistance.frame.origin.x && [obj.position_y integerValue] == (NSInteger)viewWithMinDistance.frame.origin.y && viewWithMinDistance.tag == [obj.typeNumber integerValue];
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
                [self reloadBoardView];
                [self reloadToolsView];
            }
        }
    }
    
    if (self.counterPutComponent == 3) {
        [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"Mistake put scheme message", nil)];
        self.counterPutComponent = 0;
    }
}

#pragma mark - Helper

- (void)reloadBoardView
{
    __block BOOL isAllItemsSet = YES;

    NSArray *showedView = [[self.taskScheme.elements allObjects] select:^BOOL(SchemeElement *schemeElement) {
        return [schemeElement.isFilled boolValue];
    }];
    
    [[self.boardView subviews] makeObjectsPerformSelector:@selector(removeFromSuperview)];
    
    NSArray *elements = [self sortedElements];
    
    [elements each:^(SchemeElement *senderElement) {
        
        [[self movableViewsForTask] enumerateObjectsUsingBlock:^(MTMovableView *view, NSUInteger idx, BOOL *stop) {
            
            if (view.tag == [senderElement.typeNumber integerValue]) {
                
                CGRect viewAtBoardRect = CGRectMake([senderElement.position_x integerValue], [senderElement.position_y integerValue],
                                                    view.frame.size.width, view.frame.size.height);
                
                MTMovableView *viewAtBoard = [self.boardView.subviews match:^BOOL(MTMovableView *obj) {
                    return CGRectEqualToRect(obj.frame, viewAtBoardRect);
                }];
                
                if (!viewAtBoard) {
                    viewAtBoard = [view copy];
                    
                    viewAtBoard.isMoveEnabled = NO;
                    viewAtBoard.frame = viewAtBoardRect;
                    [self.boardView addSubview:viewAtBoard];
                    
                    NSArray *addedView = [[self.boardView subviews] select:^BOOL(UIView *view) {
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
    
    self.doneButton.enabled = isAllItemsSet;
}

- (void)reloadToolsView
{
    //skip items, which have been placed to board
    NSArray *movableViewsForTask = [self movableViewsForTools];
    
    self.tools = [self.tools select:^BOOL(MTMovableView *tool) {
        return [movableViewsForTask any:^BOOL(MTMovableView *view) {
            return view.tag == tool.tag;
        }];
    }];
        
    [self.toolsView reloadDataWithViews:self.tools];
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

- (NSMutableArray *)movableViewsForTask
{
    if (!_movableViewsForTask) {
        NSSet *schemeElementTypes = [self.taskScheme.elements valueForKey:@"typeNumber"];
            
        NSArray *selectedTools = [self.toolsView.tools select:^BOOL(MTMovableView *view) {
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

- (NSArray *)movableViewsForTools
{    
    NSSet *notFilledElements = [self.taskScheme.elements select:^BOOL(SchemeElement *element) {
        return ![element.isFilled boolValue];
    }];
    
    NSSet *notFilledElementTypes = [notFilledElements valueForKey:@"typeNumber"];
    
    NSArray *selectedTools = [self.movableViewsForTask select:^BOOL(MTMovableView *view) {
        return [notFilledElementTypes containsObject:@(view.tag)];
    }];
    
    return selectedTools;
}

#pragma mark - Helper

- (BOOL)isTaskTrening
{
    return [[[[[self.task.identifier componentsSeparatedByString:@"-"] lastObject] componentsSeparatedByString:@"."] objectAtIndex:0]
            isEqualToString:@"1"];
}

- (CGFloat)startDrowImageY
{
    NSArray *sortView = [[self.boardView subviews] sortedArrayUsingComparator:^NSComparisonResult(MTMovableView *obj1, MTMovableView *obj2) {
        return obj1.frame.origin.y < obj2.frame.origin.y;
    }];
    
    MTMovableView *first = [sortView lastObject];
    
    return first.frame.origin.y - 5;
}

- (CGFloat)endDrowImageY
{
    NSArray *sortView = [[self.boardView subviews] sortedArrayUsingComparator:^NSComparisonResult(MTMovableView *obj1, MTMovableView *obj2) {
        return obj1.frame.origin.y > obj2.frame.origin.y;
    }];
    
    MTMovableView *last = [sortView lastObject];
    
    return last.frame.origin.y + last.frame.size.height + 5;
}

- (void)saveSchemeImage
{
    UIGraphicsBeginImageContext(self.view.bounds.size);
    [self.view.layer renderInContext:UIGraphicsGetCurrentContext()];
    UIImage *sourceImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    //now we will position the image, X/Y away from top left corner to get the portion we want
    CGSize imageSize = CGSizeMake(kImageSizeWidth, kImageSizeHeight - ([self startDrowImageY] + kImageSizeHeight - [self endDrowImageY]));
    UIGraphicsBeginImageContextWithOptions(imageSize, YES, 1);
    [sourceImage drawAtPoint:CGPointMake(-kImageMovesScreenX , -(kImageMovesScreenY + [self startDrowImageY]))];
    UIImage *croppedImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    NSString *imagePath = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    NSString *privateDocs = [imagePath stringByAppendingPathComponent:@"images"];
    NSError *error = nil;
    
    BOOL success = [[NSFileManager defaultManager] createDirectoryAtPath:privateDocs withIntermediateDirectories:YES attributes:nil error:&error];
    NSLog(@"success : %@", success ? @"YES" : @"NO");
    
    NSString *imageName = [NSString stringWithFormat:@"/images/image%@", self.task.identifier];
    [self saveImage:croppedImage withFileName:imageName ofType:@"png" inDirectory:imagePath];
    
    self.schemeBackgroundView.hidden = YES;
}

- (void) saveImage:(UIImage *)image withFileName:(NSString *)imageName ofType:(NSString *)extension inDirectory:(NSString *)directoryPath {
    if ([[extension lowercaseString] isEqualToString:@"png"]) {
        [UIImagePNGRepresentation(image) writeToFile:[directoryPath stringByAppendingPathComponent:[NSString stringWithFormat:@"%@.%@", imageName, @"png"]] options:NSAtomicWrite error:nil];
    } else if ([[extension lowercaseString] isEqualToString:@"jpg"] || [[extension lowercaseString] isEqualToString:@"jpeg"]) {
        [UIImageJPEGRepresentation(image, 1.0) writeToFile:[directoryPath stringByAppendingPathComponent:[NSString stringWithFormat:@"%@.%@", imageName, @"jpg"]] options:NSAtomicWrite error:nil];
    } else {
        //        NSLog(@"Image Save Failed\nExtension: (%@) is not recognized, use (PNG/JPG)", extension);
    }
}

@end
