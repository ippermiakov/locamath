//
//  DefinitionBaseViewController.h
//  Mathematic
//
//  Created by Developer on 11.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentableViewController.h"

@class GifPlayerView;

@interface DefinitionBaseViewController : PresentableViewController

@property (strong, nonatomic) IBOutlet GifPlayerView *gifPlayerView;
@property (unsafe_unretained, nonatomic) NSInteger page;

@end
