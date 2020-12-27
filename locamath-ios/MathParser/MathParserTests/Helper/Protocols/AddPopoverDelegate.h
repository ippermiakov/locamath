//
//  AddPopoverDelegate.h
//  Mathematic
//
//  Created by Developer on 09.10.12.
//  Copyright (c) 2012 Developer. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol AddPopoverDelegate <NSObject>
@required
- (void)theButtonHasTapped:(id)sender;
@optional
- (void)theAnswerHasTapped:(id)sender;

@end
