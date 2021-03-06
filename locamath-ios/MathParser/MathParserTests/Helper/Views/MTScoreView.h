//
//  MTScoreView.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 18.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@class OlympiadTask;

@interface MTScoreView : UIView

@property(nonatomic, strong) IBOutlet UILabel *topLabel;
@property(nonatomic, strong) IBOutlet UILabel *bottomLabel;
@property(nonatomic, strong) OlympiadTask *task;

@end
