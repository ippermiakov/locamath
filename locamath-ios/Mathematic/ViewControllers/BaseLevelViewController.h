//
//  BaseLevelViewController.h
//  Mathematic
//
//  Created by SanyaIOS on 24.07.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "BaseViewController.h"
#import "MTLevelViewDelegate.h"

@class ConcretLevelViewController;

typedef void(^BackBlock)();

@interface BaseLevelViewController : BaseViewController <MTLevelViewDelegate> {
    CGPoint         charactersPoint;
}

@property (copy, nonatomic) BackBlock backBlock;
@property (weak, nonatomic) IBOutlet UIView *charactersView;
@property (strong, nonatomic) ConcretLevelViewController *concretLevel;

- (IBAction)onTapCharacters:(id)sender;
- (void)changePointsForCharacters;

- (void)updateLevelsView;


@end
