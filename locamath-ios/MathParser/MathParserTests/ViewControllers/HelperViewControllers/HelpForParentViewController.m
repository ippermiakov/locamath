//
//  HelpForParentViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 28.08.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "HelpForParentViewController.h"
#import <QuartzCore/QuartzCore.h>
#import "ChildManager.h"
#import "Child.h"
#import "LevelsPath.h"
#import "Level.h"
#import "OlympiadLevel.h"
#import "Task.h"
#import "OlympiadTask.h"
#import "DescriptionLevelPathView.h"
#import "StrokeLabel.h"
#import "TTTAttributedLabel.h"

@interface HelpForParentViewController ()

- (IBAction)onTapNext:(id)sender;
@property (strong, nonatomic) IBOutlet UIScrollView *scrollView;

@property (strong, nonatomic) IBOutlet UILabel *nextOrDoneButton;
@property (strong, nonatomic) IBOutlet StrokeLabel *neededForPlayLabel;
@property (strong, nonatomic) IBOutlet UILabel *totalLabel;
@property (strong, nonatomic) IBOutletCollection(StrokeLabel) NSArray *page2StrokeLabels;
@property (strong, nonatomic) IBOutlet StrokeLabel *problemLabel;
@property (weak, nonatomic) IBOutlet TTTAttributedLabel *textLabel;

@property (strong, nonatomic) Child *currentChild;
@end

@implementation HelpForParentViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    
    self.currentChild  = [[ChildManager sharedInstance] currentChild];
    
    if (self.isLast) {
        self.nextOrDoneButton.text = NSLocalizedString(@"Done", nil);
        [self addComponents];
        self.totalLabel.text = [NSString stringWithFormat: NSLocalizedString(@"A total of %@ problems, %@ test problems, %@ olympiad problems", nil),[self stringCountOfTask], [self stringAlltestTaskCount],[self stringCountOfOlympiadTask]];
    }
    
    if ([self.nibName isEqualToString:@"HelpForParent2"]) {
        [self.page2StrokeLabels each:^(StrokeLabel *sender) {
            [sender whiteShadowForLabel];
        }];
    }
    
    if ([self.nibName isEqualToString:@"HelpForParent3"]) {
        [self.problemLabel whiteShadowForLabel];
    }

    [self setActualFonts];

    if (self.textLabel) {
        self.textLabel.lineHeightMultiple = 0.8f;
        //to redraw label
        self.textLabel.text = self.textLabel.text;
    }    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)onTapNext:(id)sender
{
    [self presentNextViewController];
}

- (void)viewDidUnload
{
    [self setNextOrDoneButton:nil];
    [self setNeededForPlayLabel:nil];
    [self setScrollView:nil];
    [self setTotalLabel:nil];
    [self setPage2StrokeLabels:nil];
    [self setProblemLabel:nil];
    [self setTextLabel:nil];
    [super viewDidUnload];
}

#pragma mark - Helper

- (void)addComponents
{
    __block CGFloat viewHeight = 0;
    
    [[DataUtils pathsFromCurrentChildForLevelNumber:@1] enumerateObjectsUsingBlock:^(LevelsPath *obj, NSUInteger idx, BOOL *stop) {
        *stop = idx == 3;
        DescriptionLevelPathView *descriptionView = [[NSBundle mainBundle] loadNibNamed:@"DescriptionLevelPathView" owner:self options:nil][0];
        
        CGRect frame = CGRectMake(0, 0, descriptionView.frame.size.width, descriptionView.frame.size.height);
        viewHeight = descriptionView.frame.size.height;
        
        if (idx != 0) {
            frame = CGRectMake(0, idx * (descriptionView.frame.size.height + 10), descriptionView.frame.size.width, descriptionView.frame.size.height);
        }
        
        descriptionView.frame = frame;
        descriptionView.pathNameLabel.text = [NSString stringWithFormat:NSLocalizedString(@"%i.%@", nil), idx + 1, obj.name];
        descriptionView.countOfTaskLabel.text = [NSString stringWithFormat:NSLocalizedString(@"- Solve %@ problems", nil), [self stringCountTaskForPath:obj]];
        descriptionView.countOfTestTaskLabel.text = [NSString stringWithFormat:NSLocalizedString(@"- %@ test problems", nil), [self stringCountTestTaskForPath:obj]];
        descriptionView.levelsLabel.text = [NSString stringWithFormat:NSLocalizedString(@"- %@ level of difficulty", nil), [self stringCountLevelForPath:obj]];
        
        [self.scrollView addSubview:descriptionView];
        
    }];
    
    NSInteger labelHeight = 30;
    
    CGRect frameForLabel = CGRectMake(0, (viewHeight + 10) * [[DataUtils pathsFromCurrentChildForLevelNumber:@1] count], self.scrollView.frame.size.width, labelHeight);
    UILabel *label = [[UILabel alloc] init];
    label.frame = frameForLabel;
    label.textColor = [UIColor whiteColor];
    label.backgroundColor = [UIColor clearColor];
    label.font = [UIFont fontWithName:@"Helvetica-Bold" size:17];
    
    label.text = [NSString stringWithFormat:NSLocalizedString(@"%i. Witty olympiad problems %i levels with %@ problems in each", nil),
                  [[DataUtils pathsFromCurrentChildForLevelNumber:@1] count] + 1,
                  [self.currentChild.olympiadLevels count]/4,
                  [self stringCountOfOlympiadTaskInLevel]];
    
    [self.scrollView addSubview:label];
    self.scrollView.scrollEnabled = NO;
//    self.scrollView.contentSize = CGSizeMake(self.scrollView.frame.size.width, label.frame.origin.y + labelHeight
//                                             + 10 *[DataUtils pathsFromCurrentChildForLevelNumber:@1].count);
    
}

- (NSString *)stringCountOfTask
{
    __block  NSNumber *countOfTask = @0;
    [[DataUtils pathsFromCurrentChildForLevelNumber:@1] each:^(LevelsPath *levelPath) {
        NSNumber *countOfTaskForLevel = [levelPath.levels reduce:@0 withBlock:^id(NSNumber *sum, Level *obj) {
            return @([sum integerValue] + obj.tasks.count);
        }];
        countOfTask = @([countOfTask integerValue] + [countOfTaskForLevel integerValue]);
    }];
   
    return [countOfTask stringValue];
}

- (NSString *)stringCountOfOlympiadTask
{
    //TODO: change 2 on correct value(stub)!!!
    NSNumber *countOfOlympiadTask = @([self.currentChild.olympiadTasks count]);
    return [countOfOlympiadTask stringValue];
}

- (NSString *)stringCountOfOlympiadTaskInLevel
{
    OlympiadLevel *olevel = [DataUtils olympiadLevelsWithTasksFromCurrentChild][0];
    
    NSNumber *countOfOlympiadTask = @([olevel.tasks count]);
    return [countOfOlympiadTask stringValue];
}

- (NSString *)stringCountLevelForPath:(LevelsPath *)levelPath
{
    NSNumber *countOfOlympiadTask = @([levelPath.levels count] - 1);
    return [countOfOlympiadTask stringValue];
}

- (NSString *)stringCountTaskForPath:(LevelsPath *)levelPath
{
    NSNumber *countTaskForPath = [levelPath.levels reduce:@0 withBlock:^id(NSNumber *sum, Level *level) {
        if ([level.isTest boolValue]) {
            return sum;
        }
        return @([sum integerValue] + [level.tasks count]);
    }];
    
    return [countTaskForPath stringValue];
}

- (NSString *)stringCountTestTaskForPath:(LevelsPath *)levelPath
{
    Level *testLevel = [levelPath.levels match:^BOOL(Level *obj) {
        return [obj.isTest boolValue];
    }];
    
    NSNumber *countOfTestLevelTask = @([testLevel.tasks count]);
    
    return [countOfTestLevelTask stringValue];
}

- (NSString *)stringAlltestTaskCount
{
    NSArray *testLevels = [[self.currentChild.levels allObjects] select:^BOOL(Level *obj) {
        return [obj.isTest boolValue] && [[obj.identifier substringToIndex:1] isEqualToString:@"1"];
    }];
    
    NSNumber *countTaskForPath = [testLevels reduce:@0 withBlock:^id(NSNumber *sum, Level *level) {
        return @([sum integerValue] + [level.tasks count]);
    }];
    
    return [countTaskForPath stringValue];
}

@end
