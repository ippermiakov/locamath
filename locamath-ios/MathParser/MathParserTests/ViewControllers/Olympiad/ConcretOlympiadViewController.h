//
//  ConcretOlympiadViewController.h
//  Mathematic
//
//  Created by Developer on 12.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "OlympiadTask.h"
#import "AbstractAchievementViewController.h"

@class OlympiadLevel;
@class LWRatingView;
@class MTScoreView;

@interface ConcretOlympiadViewController : BaseViewController<AbstractAchievementViewController>

@property (strong, nonatomic) OlympiadLevel *level;
@property (strong, nonatomic) IBOutlet LWRatingView *stars;
@property (strong, nonatomic) IBOutletCollection(MTScoreView) NSArray *scores;

- (IBAction)onTapProblemButton:(id)sender;
- (IBAction)onTapBackHome:(id)sender;

- (OlympiadTask *)taskWithIndex:(NSUInteger)idx;

@end
