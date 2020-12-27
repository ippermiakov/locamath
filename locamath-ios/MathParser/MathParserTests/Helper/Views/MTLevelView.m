//
//  MTLevelView.m
//  Mathematic
//
//  Created by Developer on 28.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "MTLevelView.h"
#import "Level.h"

@implementation MTLevelView

- (void)updateViewWithLevelInfo
{
    self.title.text = self.level.name;
    self.levelScoreLabel.text = [self.level.levelScore stringValue];
    self.solvedTasksLabel.text = [self.level.countSolvedTasks stringValue];
    self.startedTasksLabel.text = [self.level.countStartedTasks stringValue];
    self.totalTasksLabel.text = [NSString stringWithFormat:@"%d", [self.level.tasks count]];
    self.currentScoreLabel.text = [self.level.currentScore stringValue];
}

- (IBAction)onTapButton:(UIButton *)sender
{
    [sender setHighlighted:NO];
    [self openLevel];
}

- (void)openLevel
{
    NSData *dataLevelView = [NSKeyedArchiver archivedDataWithRootObject:self];
    [self.delegate openLevel:self.level withDataLevelView:dataLevelView];
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    if(self = [super initWithCoder:aDecoder]) {
        self.originalSize = [[aDecoder decodeObjectForKey:@"originalSize"] CGSizeValue];
        self.levelButton = [aDecoder decodeObjectForKey:@"levelButton"];
        
        NSData *imageData = [aDecoder decodeObjectForKey:@"levelButtonImage"];
        [self.levelButton setBackgroundImage:[UIImage imageWithData:imageData]
                                    forState:UIControlStateNormal];
    }
    
    return self;
}

- (void)encodeWithCoder:(NSCoder *)enCoder
{
    [super encodeWithCoder:enCoder];
    [enCoder encodeObject:[NSValue valueWithCGSize:self.originalSize] forKey:@"originalSize"];
    
    //on ios 5.0 not encoded propertly
    [enCoder encodeObject:self.levelButton forKey:@"levelButton"];

    UIImage *bgImage = [self.levelButton backgroundImageForState:UIControlStateNormal];
    NSData *data = UIImagePNGRepresentation(bgImage);
        
    [enCoder encodeObject:data forKey:@"levelButtonImage"];
}


@end
