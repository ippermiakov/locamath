//
//  BaseViewControllerDelegate.h
//  Mathematic
//
//  Created by Developer on 06.03.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol BaseViewControllerDelegate <NSObject>
@required

- (void)didFinishBackWithOption:(BOOL)option;

@end
