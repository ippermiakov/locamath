//
//  PresentingSeguesStructure.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 18.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "PresentingSeguesStructure.h"
#import "PresentableViewController.h"

@implementation PresentingSeguesStructure

- (id)init
{
    if (self = [super init]) {
        self.instantiators  = [NSMutableArray new];
        self.numOfCurrentVC = -1;
    }
    
    return self;
}

- (void)addLinkWithInstantiator:(Instantiator)instantiator
{
    [self.instantiators addObject:instantiator];
}

- (void)addLink:(Class)vcClass
{
    [self.instantiators addObject:^(){
        return [vcClass new];
    }];
}

- (void)addLinkWithObject:(id)object
{
    [self.instantiators addObject:^(){
        return object;
    }];
}

- (PresentableViewController*)nextViewController
{
    PresentableViewController *retval = nil;
    
    self.numOfCurrentVC++;
    
    if (self.instantiators.count > self.numOfCurrentVC) {
        Instantiator block = [self.instantiators objectAtIndex:self.numOfCurrentVC];
        retval = block();
        retval.seguesStructure = self;
    }
    
    return retval;
}

/*- (PresentableViewController*)nextViewControllerToViewController:(PresentableViewController *)vc
{
    PresentableViewController *retval = nil;
    
    NSInteger idx;
    
    if (vc == nil) { // if it is nil, we return first view controller
        idx = 0 - 1;
    } else {
        idx = [self.instantiators indexOfObjectPassingTest:^BOOL(id obj, NSUInteger idx, BOOL *stop) {
            Instantiator block = obj;
            return [block() isKindOfClass:[vc class]];
        }];
    }
    
    if (idx != NSNotFound && self.instantiators.count > 0) {
        if (idx + 1 < self.instantiators.count) {
            Instantiator block = [self.instantiators objectAtIndex:idx + 1];
            retval = block();
            retval.seguesStructure = self;
        }
    }
    
    return retval;
}*/

@end
