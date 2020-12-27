//
//  SolvingSolutionViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 18.11.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SolvingSolutionViewController.h"
#import "Task.h"
#import "Level.h"
#import "Scheme.h"
#import "SchemeElement.h"
#import "TaskError.h"
#import "Action.h"

#import "MTToolsView.h"
#import "MTMovableView.h"

#import "SoundManager.h"
#import "SolutionView.h"
#import "GameManager.h"
#import "ChildManager.h"

#include <mach/mach_time.h>
#import "DistanceCalculation.h"

@interface SolvingSolutionViewController ()

@property (strong, nonatomic) SolutionView *solutionView;
@property (strong, nonatomic) Task *task;

@property (strong, nonatomic) NSFetchedResultsController *actionsFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *expressionsFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *solutionsFetchedResultsController;
@property (strong, nonatomic) NSFetchedResultsController *tasksFetchedResultsController;

@end

@implementation SolvingSolutionViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (id)initWithAchievement:(id)achievement
{
    self = [super init];
    if (self) {
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(chooseComponent:)
                                                     name:kNotificationChooseComponent
                                                   object:nil];
        [[NSNotificationCenter defaultCenter] addObserver:self
                                                 selector:@selector(putComponent:)
                                                     name:kNotificationPutComponent
                                                   object:nil];
        
        if ([NSStringFromClass([achievement class]) isEqualToString:@"TaskError"]) {
            self.taskError = (TaskError *)achievement;
            self.task = self.taskError.task;
        } else {
            self.task = (Task *)achievement;
        }
    }
    return self;
}


- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    [self reloadSolvingPage];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)chooseComponent:(NSNotification *)notification
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[8] loop:NO];
}

- (void)putComponent:(NSNotification *)notification
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[9] loop:NO];
}

- (void)reloadSolvingPage
{
    [self.solutionView removeFromSuperview];
    self.solutionView =  nil;
    
    self.solutionView = [[SolutionView alloc] initWithFrame:CGRectMake(20.0f, 0.0f, 558.0f, 380.0f)];
    self.solutionView.dataSource = self;
    self.solutionView.delegateSolution = self;

    [self.view addSubview:self.solutionView];
    
    //add all possible expressions and solutions
    if (self.taskError == nil) {
        for (NSInteger i = 0; i < [self.task.expressions count] * 2; i++) {
            if ([self.task.solutions isEqualToString:kBothSolutionsType] && [self.task.actions count] < [self.task.expressions count] * 2) {
                [self createActionWithType:kActionTypeSolution];
            } else if(![self.task.solutions isEqualToString:kBothSolutionsType] && [self.task.actions count] < [self.task.expressions count]){
                [self createActionWithType:kActionTypeExpression];
            }
        }
    }
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    [self.solutionView reloadDataWithAnimation];
    
    if (self.isViewLoaded) {
        [self.solutionView reloadData];
    }
    
    self.solutionView.contentOffset = CGPointZero;
}

- (void)setNeedsFont
{
    [self setActualFonts];
}

- (void)createActionWithType:(ActionType)type
{
    Action *action = [Action createEntity];
    
    action.identifier =  [NSString stringWithFormat:@"%llu", mach_absolute_time()]; //timestamp
    action.task = self.task;
    action.type = type;
    action.answer = @"";
    
    [action addSubActionWithString:@""];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    [self.actionsFetchedResultsController performFetch:NULL];
    
//    [self.solutionView reloadDataWithAnimation];
    //avoid automatic offset change
    [self.solutionView reloadData];
}

- (ActionView *)createActionViewWithAction:(Action *)action
{
    ActionView *actionView = [[ActionView alloc] initWithAction:action];
    
    [actionView setBackgroundColor:[UIColor clearColor]];
    actionView.answer = action.answer;
    actionView.subActions = [NSMutableArray arrayWithArray:[action.subActions array]];
    
    [actionView.headerLabel setBackgroundColor:[UIColor clearColor]];
    [actionView.headerLabel setFont:[UIFont fontWithName:@"Helvetica-Bold" size:22.0f]];
    [actionView.headerLabel setTextColor:[UIColor whiteColor]];
    
    NSArray *sortedArray = [[self.task.actions allObjects] sortedArrayUsingComparator:^NSComparisonResult(Action *obj1, Action *obj2) {
        return [[obj1 identifier] compare:[obj2 identifier]];
    }];
    
    NSArray *operationActions = [sortedArray select:^BOOL(Action *obj) {
        return obj.subActions.count > 1;
    }];
    
    //create expression if need
    if ([action.task.solutions isEqualToString:kBothSolutionsType] &&
        operationActions.count == [action.task.expressions count] &&
        action.subActions.count <= 1) {
        
        action.type = kActionTypeExpression;
    } else if (action.answer.length > 0 && action.subActions.count == 1 &&
                    [self.task.expressions count] > [self countExpressions]) {
        action.type = kActionTypeExpression;
    } else if (![self.task.solutions isEqualToString:kBothSolutionsType]) {
         action.type = kActionTypeExpression;
    } else if (action.type != kActionTypeExpression) {
        action.type = kActionTypeSolution;
    } else if (action.answer.length == 0) {
        action.type = kActionTypeSolution;
    }
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
    
    NSString *labelFormat = [DataUtils isArabicLocale] ? @"%@  %i" : @"%@ #%i";
    
    if (action.type == kActionTypeExpression) {
        NSInteger expIndex = [sortedArray indexOfObject:action] + 1;
        
        [self.expressionsFetchedResultsController performFetch:NULL];
        
        actionView.headerLabel.text = [NSString stringWithFormat
                                       :labelFormat, NSLocalizedString(@"Expression", nil), expIndex < 5 && expIndex > 0 ? expIndex:1];
    } else {
        NSInteger solIndex = [sortedArray indexOfObjectIdenticalTo:action] + 1;
        [self.solutionsFetchedResultsController performFetch:NULL];
        
        if ([self.task.solutions isEqualToString:kBothSolutionsType] && action.type == kActionTypeSolution && action.subActions.count < 2) {
            actionView.headerLabel.text = [NSString stringWithFormat
                                           :labelFormat, NSLocalizedString(@"Solution1", nil), solIndex < 5 && solIndex > 0 ? solIndex :1];
        } else {
            actionView.headerLabel.text = [NSString stringWithFormat
                                           :labelFormat, NSLocalizedString(@"Solution", nil), solIndex < 5 && solIndex > 0 ? solIndex :1];
        }
    }
    
    [actionView updateActionContentIfNeeded];
    
    return actionView;
}


- (NSArray *)fetchedActions
{
    [self.actionsFetchedResultsController performFetch:NULL];
    self.actionsFetchedResultsController = nil;
    return [self.actionsFetchedResultsController fetchedObjects];
}

- (void)deleteAction:(Action *)action
{
    if (self.task.status != kTaskStatusSolved) {
        [action deleteEntity];
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
    }
}

- (void)addSubActionToAction:(Action *)action
{
    [action addSubActionWithString:@""];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

- (void)deleteSubActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    [[action.subActions objectAtIndex:subActionIndex] deleteEntity];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)addComponent:(NSString *)component subActionWithIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    NSString *str = [action.subActions objectAtIndex:subActionIndex];
    str = [str stringByAppendingString:component];
    [action replaceObjectInSubActionsAtIndex:subActionIndex withObject:[Action actionWithString:str]];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)didChangeComponent:(NSString *)component withSubActionIndex:(NSInteger)subActionIndex forAction:(Action *)action
{
    //    [action replaceObjectInSubActionsAtIndex:subActionIndex withObject:[Action actionWithString:component]];
    
    if ([self.delegate respondsToSelector:@selector(didChangeComponent)]) {
        [self.delegate didChangeComponent];
    }
    
    Action *subAction = [action.subActions objectAtIndex:subActionIndex];
    [subAction updateWithString:component];
    
    Level *level = (Level *)subAction.parentAction.task.level;
    if (level.isTest) {
        action.answer = component;
    }
    
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
}

- (void)didChangeAnswerComponent:(NSString *)component forAction:(Action *)action
{
    action.answer = component;
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

- (void)addAnswerWithComponent:(NSString *)component forAction:(Action *)action
{
    action.answer = [action.answer stringByAppendingString:component];
    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];;
}

#pragma mark - Services Methods

- (NSInteger)countExpressions
{
    [self.expressionsFetchedResultsController performFetch:NULL];
    return [[self.expressionsFetchedResultsController fetchedObjects] count];
}

- (NSInteger)countSolutions
{
    [self.solutionsFetchedResultsController performFetch:NULL];
    return [[self.solutionsFetchedResultsController fetchedObjects] count];
}

#pragma mark - Setters&Getters

- (NSFetchedResultsController *)tasksFetchedResultsController
{
    if (!_tasksFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"identifier == %@ && child.identifier == %@", self.task.identifier, [ChildManager sharedInstance].currentChild.identifier];

        self.tasksFetchedResultsController = [Task fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _tasksFetchedResultsController;
}


- (NSFetchedResultsController *)actionsFetchedResultsController
{

    if (!_actionsFetchedResultsController) {

        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@", self.task];

        if (self.taskError) {
            predicate = [NSPredicate predicateWithFormat:@"taskError == %@", self.taskError];
        }

        self.actionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES
                                                          withPredicate:predicate
                                                                groupBy:nil
                                                               delegate:nil];
    }

    return _actionsFetchedResultsController;
}

- (NSFetchedResultsController *)expressionsFetchedResultsController
{
    if (!_expressionsFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@ AND typeNumber == %@", self.task, [NSNumber numberWithInteger:kActionTypeExpression]];

        self.expressionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _expressionsFetchedResultsController;
}

- (NSFetchedResultsController *)solutionsFetchedResultsController
{
    if (!_solutionsFetchedResultsController) {
        NSPredicate *predicate = [NSPredicate predicateWithFormat:@"task == %@ AND typeNumber == %@", self.task, [NSNumber numberWithInteger:kActionTypeSolution]];

        self.solutionsFetchedResultsController = [Action fetchAllSortedBy:@"identifier" ascending:YES withPredicate:predicate groupBy:nil delegate:nil];
    }
    return _solutionsFetchedResultsController;
}


@end
