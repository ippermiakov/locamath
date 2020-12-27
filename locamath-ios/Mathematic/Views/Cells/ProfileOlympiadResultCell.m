//
//  ProfileOlympiadResultCell.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 28.04.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ProfileOlympiadResultCell.h"

@implementation ProfileOlympiadResultCell

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        
    }
    return self;
}

- (void)awakeFromNib
{
    [self.cupView addObserver:self forKeyPath:@"image" options:NSKeyValueObservingOptionNew context:nil];
}

- (void)observeValueForKeyPath:(NSString *)keyPath ofObject:(id)object change:(NSDictionary *)change context:(void *)context
{
    CGFloat divider   = (self.cupView.image.size.width / 2) / self.cupView.frame.size.width;
    CGSize  imageSize = CGSizeMake(self.cupView.image.size.width / divider, self.cupView.image.size.height / divider);
    
    CGRect oldFrame   = self.cupView.frame;
    CGRect newFrame;
    newFrame.origin   = oldFrame.origin;
    newFrame.size     = CGSizeMake(imageSize.width / 2, imageSize.height / 2);
    newFrame.origin.y = oldFrame.origin.y + (oldFrame.size.height - imageSize.height / 2);
    
    self.cupView.frame = newFrame;
}

@end
