//
//  Level_1.m
//  Mathematic
//
//  Created by Developer on 22.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "Level_1_ViewController.h"
#import "ConcretLevelViewController.h"
#import "DefinitionPresenter.h"
#import "MTLevelView.h"
#import "DataUtils.h"
#import "ChildManager.h"
#import "GameManager.h"
#import "Game.h"
#import "Level.h"
#import "TransitionsManager.h"
#import "MBProgressHUD.h"
#import "GameAlertViewController.h"
#import "AnimationManager.h"
#import "MTStarView.h"
#import "LevelMapViewController.h"
#import "MTHTTPClient.h"

static NSInteger const kStartPositXForCharacters = 1286;
static NSInteger const kStartPositYForCharacters = 490;

@interface Level_1_ViewController ()

@end

@implementation Level_1_ViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {

    }
    return self;
}

#pragma mark - UIViewController lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    [self updateLevelsView];
    [GameManager.levelMap updateChild];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    if (!self.view.superview) {
        //load view if needed
        [GameManager.levelMap.theScrollView addSubview:self.view];
    }
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
//    [self updateLevelsView];
    [[AnimationManager sharedInstance] playSEAnimationsIfNeededWithLevelViews:self.levelsViews
                                                                   starsViews:GameManager.levelMap.stars
                                                                  levelNumber:@1];
    [[AnimationManager sharedInstance] showAnimationIfNeededOnCharactersView:self.charactersView];
}

- (void)viewDidUnload
{
    [self setLevelsViews:nil];
    [self setCharactersView:nil];
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Main methods

- (void)didFinishBackWithOption:(BOOL)option
{
    [self viewWillAppear:NO];
    [self viewDidAppear:NO];

    self.concretLevel = nil;
}

- (void)updateViewOnSyncFinished
{
    [super updateViewOnSyncFinished];

    [self updateLevelsView];
    [[AnimationManager sharedInstance] playSEAnimationsIfNeededWithLevelViews:self.levelsViews
                                                                   starsViews:GameManager.levelMap.stars
                                                                  levelNumber:@1];
}

- (void)updateLevelsView
{
    ChildManager *childManager = [ChildManager sharedInstance];
    [GameManager.levelMap updateChild];
    
    if ([self shouldUpdate]) {
//        NSLog(@"!!!! updating");
        [self changeLevelsViewData];
        [self changePointsForCharacters];
        [[AnimationManager sharedInstance] prepareLevelViews:self.levelsViews
                                                  starsViews:GameManager.levelMap.stars
                                                 levelNumber:@1];
//        [[AnimationManager sharedInstance] playSEAnimationsIfNeededWithLevelViews:self.levelsViews
//                                                                       starsViews:GameManager.levelMap.stars
//                                                                      levelNumber:@1];
    } else {
        childManager.addChildBlock = ^(Child *child) {
            [self updateViewOnSyncFinished];
//            if ([self shouldUpdate]) {
//                NSLog(@"!!!! updating");
//                [self changeLevelsViewData];
//                [self changePointsForCharacters];
//                [[AnimationManager sharedInstance] playSEAnimationsIfNeededWithLevelViews:self.levelsViews
//                                                                               starsViews:GameManager.levelMap.stars
//                                                                              levelNumber:@1];
//            }
        };
    }
}

- (BOOL)shouldUpdate
{
    ChildManager *childManager = [ChildManager sharedInstance];

    BOOL result = [childManager.currentChild.isDataLoaded boolValue] ||
    ![[MTHTTPClient sharedMTHTTPClient] isReachable] || [DataUtils isCurrentChildDefault];
    
//    NSLog(@"<< shouldUpdate: %@", result ? @"YES":@"NO");
    
    return result;
}

- (void)changeLevelsViewData
{
    if ([[ChildManager sharedInstance] currentChild]) {
        NSArray *levels = [DataUtils levelsFromCurrentChild];
        
        NSArray *levelsForLevel1 = [levels select:^BOOL(Level *level) {
            return [[level.identifier substringToIndex:1] isEqualToString:@"1"];
        }];
        
        if (levelsForLevel1.count) {
            for (MTLevelView *view in self.levelsViews) {
                view.delegate = self;
                Level *level = [levelsForLevel1 objectAtIndex:view.tag];
                view.level = level;
                
                [view updateViewWithLevelInfo];
            }
            
            NSArray *testLevels = [levelsForLevel1 select:^BOOL(Level *level) {
                return [level.isTest boolValue];
            }];
            
            testLevels = [DataUtils sortedArrayOfLevels:testLevels];
            
            [testLevels enumerateObjectsUsingBlock:^(Level *level, NSUInteger idx, BOOL *stop) {
                MTStarView *starView = GameManager.levelMap.stars[idx];
                starView.level = level;
            }];
        }
    }
}

- (void)changePointsForCharacters
{
    Level *openedLevel = [ChildManager sharedInstance].currentChild.game.openedLevel;
    
    if (openedLevel) {
        CGFloat x = [openedLevel.pointX floatValue];
        CGFloat y = [openedLevel.pointY floatValue];
        
        [self.charactersView setFrame:CGRectMake(x, y, self.charactersView.frame.size.width, self.charactersView.frame.size.height)];

        [self.view bringSubviewToFront:self.charactersView];
    } else {
        [self.charactersView setFrame:CGRectMake(kStartPositXForCharacters, kStartPositYForCharacters, self.charactersView.frame.size.width, self.charactersView.frame.size.height)];
        [self.view bringSubviewToFront:self.charactersView];
    }
    
    if (self.backBlock) {
        self.backBlock();
    }
}

#pragma mark - Setters&Getters


#pragma mark - MTLevelViewDelegate

- (void)openLevel:(Level *)level withDataLevelView:(NSData *)dataLevelView
{
    NSError *error = nil;
    
    if ([[TransitionsManager sharedInstance] canOpenLevel:level error:&error]) {
        
        [GameManager sharedInstance].game.openedLevel = level;
        
        [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
        
        self.concretLevel = [[ConcretLevelViewController alloc] initWithAchievement:level];
        self.concretLevel.backDelegate = self;
        self.concretLevel.dataLevelView = dataLevelView;
        self.concretLevel.levelType = kLevelType1;
        
        [GameManager.levelMap presentViewController:self.concretLevel animated:YES completion:nil];
    } else {
        [GameAlertViewController showGameAlertWithMessageError:error withPresenter:[[self.view superview] superview]];
    }
}

@end