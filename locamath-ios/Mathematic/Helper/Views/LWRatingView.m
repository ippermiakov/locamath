//
//  LWRatingView.m
//  LocaWIFI
//
//  Created by Dmitriy Gubanov on 22.08.12.
//
//

#import "LWRatingView.h"
#import "OlympiadTask.h"

@implementation LWRatingView

@synthesize rating = _rating;

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super initWithCoder:aDecoder];
    
    if (self) {
    }
    
    return self;
}

- (void)awakeFromNib
{
    self.maxRating = kDefaultStarsCount;
    self.rating    = 0;
}

- (CGFloat)spacing
{
    return fabs(self.halfStar.frame.origin.x - self.fullStar.frame.origin.x);
}

- (void)addStar:(UIImageView*)star atIndex:(NSInteger)index
{
    CGRect templateStarFrame = star.frame;
    CGRect starFrame = CGRectZero;
    
    NSInteger decreaseFactor = kDefaultStarsCount - self.maxRating;
    
    if (decreaseFactor == self.maxRating) decreaseFactor = 0;
    
    starFrame = CGRectMake(index * [self spacing] + self.fullStar.frame.size.width/2 * decreaseFactor,
                           templateStarFrame.origin.y,
                           templateStarFrame.size.width,
                           templateStarFrame.size.height);
       
    UIImageView *newStar = [[UIImageView alloc] initWithFrame:starFrame];
    newStar.image = star.image;
    
    [self addSubview:newStar];
}

- (void)setRating:(CGFloat)rating
{
    rating  = MIN(rating, self.maxRating);
    _rating = rating;
    
    [self.subviews makeObjectsPerformSelector:@selector(removeFromSuperview)];
    
    if (self.tasks.count) {
        
        [self.tasks enumerateObjectsUsingBlock:^(OlympiadTask *obj, NSUInteger idx, BOOL *stop) {
            
            NSInteger justAddedStarsCounter = idx;
                
            if (obj.isCorrect) {
                
                //while (justAddedStarsCounter < (NSInteger)rating) {
                    [self addStar:self.fullStar atIndex:justAddedStarsCounter];
                    ++justAddedStarsCounter;
                //}
                
                if(rating - justAddedStarsCounter > 0.66) {
                    [self addStar:self.fullStar atIndex:justAddedStarsCounter];
                    ++justAddedStarsCounter;
                }
                else if(rating - justAddedStarsCounter > 0.33) {
                    [self addStar:self.halfStar atIndex:justAddedStarsCounter];
                    ++justAddedStarsCounter;
                }
            }
        
            while (justAddedStarsCounter < self.maxRating) {
                [self addStar:self.emptyStar atIndex:justAddedStarsCounter];
                ++justAddedStarsCounter;
            }
        }];
    } else {
        NSInteger justAddedStarsCounter = 0;
        
        while (justAddedStarsCounter < (NSInteger)rating) {
            [self addStar:self.fullStar atIndex:justAddedStarsCounter];
            ++justAddedStarsCounter;
        }
        
        if(rating - justAddedStarsCounter > 0.66) {
            [self addStar:self.fullStar atIndex:justAddedStarsCounter];
            ++justAddedStarsCounter;
        }
        else if(rating - justAddedStarsCounter > 0.33) {
            [self addStar:self.halfStar atIndex:justAddedStarsCounter];
            ++justAddedStarsCounter;
        }
        
        while (justAddedStarsCounter < self.maxRating) {
            [self addStar:self.emptyStar atIndex:justAddedStarsCounter];
            ++justAddedStarsCounter;
        }
    }
}

@end
