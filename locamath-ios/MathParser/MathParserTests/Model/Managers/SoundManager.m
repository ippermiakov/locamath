//
//  SoundManager.m
//  Mathematic
//
//  Created by Developer on 18.01.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "SoundManager.h"
#import "ChildManager.h"
#import <AVFoundation/AVAudioPlayer.h>
#import "GameManager.h"

NSString * const kIsNotFirstLaunch   = @"kIsNotFirstLaunch";
NSUInteger const kFirstSoundIndex = 1;
NSUInteger const kSoundsNum = 12;

@interface SoundManager ()

@property(nonatomic, unsafe_unretained) BOOL isMusicPlaying;
@property(nonatomic, strong) ChildManager *childManager;
@property(nonatomic, strong) NSMutableArray *playlistItems;
@property(nonatomic, strong) NSDictionary *duplicateDialogSoundsMapping;

- (AVAudioPlayer *)playSoundNamed:(NSString *)fileName loop:(BOOL)loop;

@end

@implementation SoundManager {
    AVAudioPlayer *theBackGroundAudioPlayer;
    AVAudioPlayer *touchAudioPlayer;
    NSMutableArray *dialogAudioPlayers;
}

@synthesize soundNames = _soundNames;

- (id)init
{
    self = [super init];
    if (self) {
        self.childManager = [ChildManager sharedInstance];
        [self playBackgroundMusicIfNeeded];
    }
    return self;
}

+ (SoundManager *)sharedInstance
{
    //return nil; //comment to block music
    static __weak SoundManager *sharedInstance = nil;
    if (sharedInstance == nil) {
        SoundManager *soundManager = [self new];
        sharedInstance = soundManager;
        
        NSString *plistPath = [[NSBundle mainBundle] pathForResource:@"DuplicateSoundsMapping" ofType:@"plist"];
        sharedInstance.duplicateDialogSoundsMapping = [NSDictionary dictionaryWithContentsOfFile:plistPath];
        
        return soundManager;
    } else {
        return sharedInstance;
    }
}

- (AVAudioPlayer *)playSoundNamed:(NSString *)vSFXName loop:(BOOL)vLoop
{
    NSURL *url = [NSURL fileURLWithPath:[NSString stringWithFormat:@"%@/%@", [[NSBundle mainBundle] resourcePath], vSFXName]];
    
	return [self playSoundURL:url loop:vLoop delay:0];
}

- (AVAudioPlayer *)playSoundURL:(NSURL *)soundURL loop:(BOOL)vLoop delay:(NSTimeInterval)delay
{
    NSError *error;
	AVAudioPlayer *audioPlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:soundURL error:&error];
    
    if (vLoop) {
        audioPlayer.numberOfLoops = -1;
    } else {
        audioPlayer.numberOfLoops = 0;
    }
    
	if (audioPlayer == nil) {
        NSLog(@"sound error description: %@", [error description]);
    } else {
        NSTimeInterval now = audioPlayer.deviceCurrentTime;
        [audioPlayer playAtTime:now + delay];
    }
    
    return audioPlayer;
}


- (void)playBackgroundMusicIfNeeded
{
    if ((!self.childManager.currentChild ||
        //there is a child and musing is on
        [self.childManager.currentChild.isMusicEnabled boolValue]) &&
        !self.isMusicPlaying) {
        theBackGroundAudioPlayer = [self playSoundNamed:@"BaseTheme.mp3" loop:YES];
        self.isMusicPlaying = YES;
    } else {
        [self stopPlayBackgroundMusicIfNeeded];
    }
}

- (void)stopPlayBackgroundMusicIfNeeded
{
    if (![self.childManager.currentChild.isMusicEnabled boolValue] || [self isDialogPlaying]) {
        theBackGroundAudioPlayer = nil;
        self.isMusicPlaying = NO;
    }
}

- (void)playTouchSoundNamed:(NSString *)vSFXName loop:(BOOL)vLoop
{
    if (!self.childManager.currentChild ||
        [self.childManager.currentChild.isSoundEnabled boolValue]) {
        touchAudioPlayer = [self playSoundNamed:vSFXName loop:vLoop];
    }
}

- (void)playDialogSounds:(NSArray *)dialogSounds
{
    //nothing to play
    if (0 == [dialogSounds count]) {
        return;
    }
    
    //update duplicates with mapping
    self.playlistItems = [[dialogSounds map:^id(NSString *dialogSoundName) {
        NSString *mappedSoundName = [self.duplicateDialogSoundsMapping objectForKey:dialogSoundName];

        NSLog(@"dialogSoundName: %@ mapping: %@", dialogSoundName, mappedSoundName);

        return mappedSoundName ?: dialogSoundName;
    }] mutableCopy];
    
    NSLog(@"playlist items: %@", self.playlistItems);
    
    if (!dialogAudioPlayers) {
        dialogAudioPlayers = [NSMutableArray new];
    }
    
    //play first item in the queue
    //play just localized sounds
    
    __block NSTimeInterval delay = 0;
    const NSTimeInterval soundsPlaybackOffset = 0.1;
    
    [self.playlistItems enumerateObjectsUsingBlock:^(id obj, NSUInteger idx, BOOL *stop) {

        NSURL *soundURL = [self currentFileFromLocalization:[NSLocale preferredLanguages][0] forIndex:idx];
        
        AVAudioPlayer *player = [self playSoundURL:soundURL loop:NO delay:delay];
        delay += player.duration + soundsPlaybackOffset;
        
        if (player) {
            [self->dialogAudioPlayers addObject:player];
        }
    }];
    
    if ([dialogAudioPlayers count] > 0) {
        [self stopPlayBackgroundMusicIfNeeded];
    }
}

- (void)stopPlayDialogSounds
{
    [self resetDialogsPlayback];
}

- (void)resetDialogsPlayback
{
    NSLog(@"player is resetted");
    self.playlistItems = nil;
    [dialogAudioPlayers makeObjectsPerformSelector:@selector(stop)];
    [dialogAudioPlayers removeAllObjects];
    [self playBackgroundMusicIfNeeded];
}

- (BOOL)isDialogPlaying
{
    return [self.playlistItems count] || [dialogAudioPlayers count];
}

#pragma mark - Setters&Getters

- (NSArray *)soundNames
{
    if (!_soundNames) {
        NSMutableArray *sounds = [NSMutableArray new];
        
        for (int i = kFirstSoundIndex; i <= kSoundsNum; i++) {
            [sounds addObject:[NSString stringWithFormat:@"Sound_%i.mp3", i]];
        }
        
        _soundNames = [sounds copy];
    }

    return _soundNames;
}

- (NSURL *)currentFileFromLocalization:(NSString *)stringLocale forIndex:(NSInteger)index
{
    NSURL *soundURL = [[NSBundle mainBundle] URLForResource:[self.playlistItems objectAtIndex:index]
                                              withExtension:@"mp3"
                                               subdirectory:nil
                                               localization:stringLocale];
    
    NSLog(@"soundURL: %@ localization: %@", soundURL, [GameManager currentLocalization]);
    
    if (![[NSFileManager defaultManager] fileExistsAtPath:[soundURL path] isDirectory:NO])
    {
        // handling (en-US,en..)
        if (stringLocale.length > 2) {
            NSString *locale = [stringLocale substringToIndex:2];
            return [self currentFileFromLocalization:locale forIndex:index];
        } else {
            return soundURL;
        }
    }
    
    return soundURL;
}

@end
