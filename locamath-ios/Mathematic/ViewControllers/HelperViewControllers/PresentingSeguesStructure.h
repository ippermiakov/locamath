//
//  PresentingSeguesStructure.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 18.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@class PresentableViewController;

typedef PresentableViewController *(^Instantiator)();


@interface PresentingSeguesStructure : NSObject

@property (strong, nonatomic) NSMutableArray *instantiators;
@property(nonatomic, unsafe_unretained) NSInteger numOfCurrentVC;

- (void)addLink:(Class)vcClass;
- (void)addLinkWithObject:(id)object;
- (void)addLinkWithInstantiator:(Instantiator)instantiator;
- (PresentableViewController *)nextViewController;
//- (PresentableViewController *)nextViewControllerToViewController:(PresentableViewController *)vc;

@end
