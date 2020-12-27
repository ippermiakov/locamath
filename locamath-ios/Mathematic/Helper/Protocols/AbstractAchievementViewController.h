//
//  AbstractTaskViewController.h
//  Mathematic
//
//  Created by alexbutenko on 6/25/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AbstractAchievement.h"
#import "BaseViewControllerDelegate.h"

@protocol AbstractAchievementViewController <BaseViewControllerDelegate>

- (id)initWithAchievement:(id<AbstractAchievement>)achievement;

@end
