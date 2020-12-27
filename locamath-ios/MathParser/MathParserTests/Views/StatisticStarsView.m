//
//  StatisticStarsView.m
//  Mathematic
//
//  Created by alexbutenko on 7/1/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "StatisticStarsView.h"
#import "DataUtils.h"
#import "LevelsPath.h"
#import "Level.h"
#import "ChildManager.h"

@interface StatisticStarsView ()

@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *starsColection;
@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *starsLevel_1;
@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *starsLevel_2;
@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *starsLevel_3;
@property (strong, nonatomic) IBOutletCollection(UIImageView) NSArray *starsLevel_4;

@end

@implementation StatisticStarsView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

/*
// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect
{
    // Drawing code
}
*/

- (void)awakeFromNib
{
    [self reloadData];
}

- (void)reloadData
{
    if (!self.isParentsStatistic) {
        if ([ChildManager sharedInstance].currentChild.gender == Male) {
            [self paintStars:self.starsColection withImage:@"Level_star_empty@2x"];
        } else {
            [self paintStars:self.starsColection withImage:@"Level_star_empty_GIRL@2x"];
        }
    } else {
        [self paintStars:self.starsColection withImage:@"Star_Empty_Parents@2x"];
    }
    
    [self highlightStarsIfNeeded:self.starsLevel_1];
}

#pragma mark - Stars Methods

- (void)paintStars:(NSArray *)stars withImage:(NSString *)imageName
{
    for (UIImageView *imageView in stars) {
        [imageView setImage:[UIImage imageNamed:imageName]];
    }
}

- (void)highlightStarsIfNeeded:(NSArray *)stars
{
    __block NSArray *curretStars = stars;
    [DataUtils.pathsFromCurrentChild enumerateObjectsUsingBlock:^(LevelsPath *path, NSUInteger idx, BOOL *stop) {
        Level *testLevel = [DataUtils testLevelFromPath:path];
        NSUInteger index = 0;
        //selct stars to fill
        if ([[testLevel.identifier substringToIndex:1] isEqualToString:@"2"]) {
            curretStars = self.starsLevel_2;
            index = 4;
        }
        
        if ([testLevel.isAllTasksSolved boolValue]) {
            UIImageView *starImageView = [curretStars objectAtIndex:idx - index];
            [starImageView setImage:[UIImage imageNamed:@"Level_star"]];
        }
    }];
}


@end
