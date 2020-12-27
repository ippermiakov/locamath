//
//  AvatarChoosingPopupVCViewController.m
//  Mathematic
//
//  Created by serg on 12/25/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import "ChooseAvatarPopupViewController.h"
#import "Constants.h"
#import "ChildManager.h"
#import "Child.h"
#import "ChooseLocationPopupViewController.h"

@interface ChooseAvatarPopupViewController ()
{
    UIImageView *star;
    
    NSArray *boys;
    NSArray *girls;
}

@property (weak, nonatomic) IBOutlet UIScrollView *scroll_View;
@property (strong, nonatomic) ChildManager *childManager;

@end

@implementation ChooseAvatarPopupViewController

static const int tagDifference = 11;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        [self getAllAvatarsNames];
        self.childManager = [ChildManager sharedInstance];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    _scroll_View.contentSize = CGSizeMake((avatarWidth + margin * 2) * boys.count, CGRectGetHeight(_scroll_View.frame));
    [self displayAvatars];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

- (void)viewDidUnload
{
    [self setScroll_View:nil];
    [super viewDidUnload];
}

#pragma mark - Private

- (void)addStar
{
    UIView *view = [self.view viewWithTag:1];
    [self addStarToAvatarWithRect:view.frame];
}

- (void)addStarToAvatarWithRect:(CGRect)aRect
{
    if (!star) {
        UIImage *img = [UIImage imageNamed:@"Selected_Star"];
        star = [[UIImageView alloc] initWithImage:img];
        star.frame = CGRectMake(0.0, 0.0, img.size.width, img.size.height);
        [_scroll_View addSubview:star];
    }
    
    CGRect newFrame = star.frame;
    newFrame.origin.x = CGRectGetMaxX(aRect) - CGRectGetWidth(star.frame) * 0.85;
    newFrame.origin.y = CGRectGetMaxY(aRect) - CGRectGetHeight(star.frame) * 1.5;
    
    star.alpha = 0.0;
    star.frame = newFrame;
    [UIView animateWithDuration:0.2 animations:^{
        self->star.alpha = 1.0;
    }];
}

static const CGFloat avatarWidth = 95.0;
static const CGFloat avatarHeight = 177.0;
static const CGFloat margin = 30.0;
static const CGFloat y = 50.0;

- (void)displayAvatars
{
    [self initAndAddAvatarsAtIndex:0];
    
    NSArray *avatars = nil;
    SEL tapSelector;
    NSUInteger currentTagDifference = 1 ;
    if (self.childManager.currentChild.gender == Male) {
        avatars = boys;
        tapSelector = @selector(onTapBoy:);
    } else {
        avatars = girls;
        tapSelector = @selector(onTapGirl:);
        currentTagDifference = tagDifference;
    }
    
    for (NSUInteger i = 0, end = avatars.count; i < end; ++i) {
        NSString *avatar = avatars[i];
        
        if ([self.childManager.currentChild.avatar isEqualToString:avatar]) {
            SuppressPerformSelectorLeakWarning(
                [self performSelector:tapSelector withObject:[self.view viewWithTag:(i + 1) * currentTagDifference]];
            );
            return;
        }
    }
    
    [self onTapBoy:(UIButton *)[self.view viewWithTag:1]];
}

- (void)initAndAddAvatarsAtIndex:(NSInteger)aIndex
{
    static CGFloat x = margin;
    
    if (aIndex == boys.count) {
        x = margin;
        
        return;
    }
    
    NSInteger tag = aIndex + 1;
    UIButton *boy = [self buttonWithImage:[UIImage imageNamed:boys[aIndex]]
                                    point:CGPointMake(x, y)
                                      tag:tag
                                 selector:@selector(onTapBoy:)];
    
    UIButton *girl = [self buttonWithImage:[UIImage imageNamed:girls[aIndex]]
                                     point:CGPointMake(x, avatarHeight + y * 2)
                                       tag:(tag * tagDifference)
                                  selector:@selector(onTapGirl:)];
    
    x += avatarWidth + margin * 2;
    
    [_scroll_View addSubview:boy];
    [_scroll_View addSubview:girl];
    [boy setNeedsLayout];
    [girl setNeedsLayout];
    
    [self initAndAddAvatarsAtIndex:(aIndex + 1)];
}

- (UIButton *)buttonWithImage:(UIImage *)aImage point:(CGPoint)aPoint tag:(NSInteger)aTag selector:(SEL)aSelector
{
//    NSLog(@"image size : %f , %f" , aImage.size.width, aImage.size.height);
    UIButton *button = [UIButton buttonWithType:UIButtonTypeCustom];
    [button setImage:aImage forState:UIControlStateNormal];
    button.frame = CGRectMake(aPoint.x, aPoint.y - aImage.size.height + 170, aImage.size.width , aImage.size.height );
    button.tag = aTag;
    [button addTarget:self action:aSelector forControlEvents:UIControlEventTouchUpInside];
    
    return button;
}

- (void)getAllAvatarsNames
{
    NSArray *allFiles = [self getAllFiles];
    
    if (allFiles.count) {
        NSPredicate *predicate = nil;
        
        predicate = [NSPredicate predicateWithFormat:@"self beginswith 'avatar_b_' and not (self contains 'Big' OR self contains 'Small')"];
        boys = [allFiles filteredArrayUsingPredicate:predicate];
        
        predicate = [NSPredicate predicateWithFormat:@"self beginswith 'avatar_g_' and not (self contains 'Big' OR self contains 'Small')"];
        girls = [allFiles filteredArrayUsingPredicate:predicate];
        
        return;
    }
    
    NSLog(@"Error: something wrong with avatars");
}

- (NSArray *)getAllFiles
{
    NSString *bundleRoot = [[NSBundle mainBundle] bundlePath];
    NSError *error = nil;
    NSArray *dirContents = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:bundleRoot error:&error];
    
    if (error) {
        NSLog(@"failed to get all files: %@", error.localizedDescription);
        return nil;
    }
    
    return dirContents;
}

#pragma mark - Actions

- (void)onTapBoy:(UIButton *)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[1] loop:NO];
    
    [self addStarToAvatarWithRect:sender.frame];
    self.childManager.currentChild.gender = Male;
    self.childManager.currentChild.avatar = boys[sender.tag - 1];
//    self.avatarName = boys[sender.tag - 1];

    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)onTapGirl:(UIButton *)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[1] loop:NO];
    
    [self addStarToAvatarWithRect:sender.frame];
    self.childManager.currentChild.gender = Female;
    self.childManager.currentChild.avatar = girls[sender.tag / tagDifference - 1];
//    self.avatarName = boys[sender.tag / tagDifference - 1];
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (IBAction)onTapContinue:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    if (self.onFinish) {
        self.onFinish();
    }
    
    [[ChildManager sharedInstance] updateChildWithSuccess:^{
        NSLog(@"update success!!!");
    } failure:^(NSError *error) {
        NSLog(@"update failure with error : %@",[error localizedDescription] );
    }];
    
//    if(self.childManager.currentChild.avatar == nil) {
//        self.childManager.currentChild.avatar = self.avatarName;
//    }
//    [[NSManagedObjectContext defaultContext] saveToPersistentStoreAndWait];
    
    [self presentNextViewController];
}

- (void)dismiss
{
    if ([self.parentVC respondsToSelector:@selector(didChangedAvatar)]) {
        [self.parentVC didChangedAvatar];
    }
    [super dismiss];
}

@end
