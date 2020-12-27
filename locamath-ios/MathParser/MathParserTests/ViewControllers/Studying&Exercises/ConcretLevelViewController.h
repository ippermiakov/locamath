//
//  
//  Mathematic
//
//  Created by Developer on 23.11.12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BaseViewController.h"
#import "AbstractAchievementViewController.h"


@interface ConcretLevelViewController : BaseViewController <AbstractAchievementViewController>

@property (strong, nonatomic) NSData *dataLevelView;
@property (unsafe_unretained, nonatomic) LevelType levelType;

@end
