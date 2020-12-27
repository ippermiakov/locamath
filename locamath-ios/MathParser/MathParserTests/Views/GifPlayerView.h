//
//  GifPlayerView.h
//  Temp
//
//  Created by Dmitriy Gubanov on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AVFoundation/AVFoundation.h>

typedef void(^AnimatingStartBlock)();

@interface GifPlayerView : UIView <AVAudioPlayerDelegate, UIWebViewDelegate>

@property (strong, nonatomic) NSURL *animURL;
@property (strong, nonatomic) NSURL *imgURL;
@property (strong, nonatomic) NSURL *soundURL;
@property (strong, nonatomic) NSString *sourceBundle;

@property (unsafe_unretained, nonatomic) BOOL isAnimating;
@property (unsafe_unretained, nonatomic) BOOL isSoundPlaying;

@property (copy, nonatomic) AnimatingStartBlock onAnimationStart;


- (void)stopAnimating;
- (void)startAnimating;

- (void)startPlayingSound;
- (void)stopPlayingSound;

@end
