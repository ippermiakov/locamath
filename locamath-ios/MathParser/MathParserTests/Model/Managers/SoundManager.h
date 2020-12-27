//
//  SoundManager.h
//  Mathematic
//
//  Created by Developer on 18.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface SoundManager : NSObject

@property (readonly, nonatomic) NSArray *soundNames;

+ (SoundManager *)sharedInstance;

- (void)playBackgroundMusicIfNeeded;
- (void)stopPlayBackgroundMusicIfNeeded;

- (void)playTouchSoundNamed:(NSString *)fileName loop:(BOOL)loop;
- (void)playDialogSounds:(NSArray *)dialogSounds;
- (void)stopPlayDialogSounds;

@end
