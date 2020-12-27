//
//  AddAccountCellDelegate.h
//  Mathematic
//
//  Created by Developer on 19.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol AddAccountCellDelegate <NSObject>

- (void)textHasBeenEdited:(NSString *)text forIndex:(NSInteger)index;

@end
