//
//  MTStarView.m
//  Mathematic
//
//  Created by alexbutenko on 8/14/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTStarView.h"

@interface MTStarView ()

@property (weak, nonatomic) IBOutlet UIImageView *starImageView;

@end

@implementation MTStarView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)awakeFromNib
{
    //to avoid multiple completion animations, we mark as incomplete just stars for not solved levels
    //and then redraw them all according to isCompleted flag
    _isCompleted = YES;
}

/*
// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect
{
    // Drawing code
}
*/

- (void)updateView
{
    if (self.isCompleted) {
        //update image
        self.starImageView.image = [UIImage imageNamed:@"star@2x.png"];
    } else {
        self.starImageView.image = [UIImage imageNamed:@"star_empty@2x.png"];
    }
}

@end
