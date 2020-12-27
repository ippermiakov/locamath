//
//  MTUnderlineButton.h
//  Mathematic
//
//  Created by SanyaIOS on 12/12/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol MTUnderlineButtonDelegate <NSObject>

- (void)didSelectButtunWithTag:(NSInteger)tag;

@end

@interface MTUnderlineButton : UIView

@property (weak, nonatomic) IBOutlet id<MTUnderlineButtonDelegate> delegate;

- (void)removeLine;

@end
