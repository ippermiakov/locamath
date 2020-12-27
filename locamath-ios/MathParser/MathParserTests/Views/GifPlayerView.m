//
//  GifPlayerView.m
//  Temp
//
//  Created by Dmitriy Gubanov on 14.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "GifPlayerView.h"
#import <AVFoundation/AVFoundation.h>
#import "MBProgressHUD.h"
#import "MTHTTPClient.h"
#import "UIAlertView+Error.h"
#import "MBProgressHUD+Mathematic.h"

#define HTML @"<!DOCTYPE html><html><body><img src=\"%@\" width=\"%f\" height=\"%f\"/></body></html>"

@interface GifPlayerView ()
@property(strong, nonatomic) UIWebView      *webView;
@property(strong, nonatomic) AVAudioPlayer  *audioPlayer;
@end


@implementation GifPlayerView

- (void)initialize
{
    self.webView = [[UIWebView alloc] initWithFrame:self.bounds];
    self.webView.delegate = self;
    [self addSubview:self.webView];
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:aDecoder];
    if (self) {
        [self initialize];
    }
    
    return self;
}

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        [self initialize];
    }
    return self;
}

- (void)dealloc
{
    [MBProgressHUD hideHUDForWindow];
}

#pragma mark Animation

- (void)loadHTMLWithURL:(NSURL *)aURL
{
    UIWebView *webView = self.webView;
    
    NSString *htmlStr = [NSString stringWithFormat:HTML, aURL, CGRectGetWidth(webView.frame), CGRectGetHeight(webView.frame)];
    [webView loadHTMLString:htmlStr baseURL:nil];
}

- (void)loadHTMLWithSourceBundle:(NSString *)bundle
{
    NSString *content = [NSString stringWithContentsOfFile:bundle
                                                  encoding:NSUTF8StringEncoding
                                                     error:nil];
    
    if (![[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [UIAlertView showAlertViewWithMessage:NSLocalizedString(@"You need an internet connection in order to watch the animation", nil)];
    }
    
    UIWebView *webView = self.webView;
    
    NSString *basePath = [[NSBundle mainBundle] bundlePath];
    NSURL *baseURL = [NSURL fileURLWithPath:basePath];
    
    [webView loadHTMLString:content baseURL:baseURL];
    
}

- (void)startAnimating
{
    [self.webView setHidden:YES];
    [self loadHTMLWithSourceBundle:self.sourceBundle];
    
    if(self.imgURL && !self.sourceBundle) {
        [self loadHTMLWithURL:self.imgURL];
    }
}

- (void)stopAnimating
{
    [[NSURLCache sharedURLCache] removeAllCachedResponses];
    [self loadHTMLWithURL:self.imgURL];
}

- (void)setIsAnimating:(BOOL)isAnimating
{
    if (isAnimating && !_isAnimating) {
        [self startAnimating];
    } else if (!isAnimating && _isAnimating) {
        [self stopAnimating];
    }
    
    _isAnimating = isAnimating;
}

#pragma mark Sound

- (void)startPlayingSound
{
    if (self.soundURL != nil) {
        self.audioPlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:self.soundURL error:nil];
        self.audioPlayer.delegate = self;
        
        [self.audioPlayer play];
    }
}

- (void)stopPlayingSound
{
    [self.audioPlayer stop];
    self.audioPlayer = nil;
}

- (void)setIsSoundPlaying:(BOOL)isSoundPlaying
{
    if (isSoundPlaying && !_isSoundPlaying) {
        [self startPlayingSound];
    } else if ( ! isSoundPlaying && _isSoundPlaying) {
        [self stopPlayingSound];
    }
    
    _isSoundPlaying = isSoundPlaying;
}

#pragma mark - AVAudioPlayerDelegate

- (void)audioPlayerDidFinishPlaying:(AVAudioPlayer *)player successfully:(BOOL)flag
{
    [self startPlayingSound];
}

#pragma mark Actions

- (void)onTapPlay:(id)sender
{
    [self startAnimating];
}

- (void)onTapPause:(id)sender
{
    [self stopAnimating];
}

- (void)webViewDidStartLoad:(UIWebView *)webView
{
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [MBProgressHUD showHUDForWindow];
    }
}

- (void)webViewDidFinishLoad:(UIWebView *)webView
{
    if ([[MTHTTPClient sharedMTHTTPClient] isReachable]) {
        [MBProgressHUD hideHUDForWindow];
    }
    
    [self.webView setHidden:NO];
    
    if (self.onAnimationStart) {
        self.onAnimationStart();
    }
}

@end
