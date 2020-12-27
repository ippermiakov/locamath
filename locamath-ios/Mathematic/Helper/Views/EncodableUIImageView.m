//
//  EncodableUIImageView.m
//  Mathematic
//
//  Created by alexbutenko on 8/21/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "EncodableUIImageView.h"

@implementation EncodableUIImageView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
    }
    return self;
}

- (id)initWithCoder:(NSCoder *)aDecoder
{
    if(self = [super initWithCoder:aDecoder]) {
        NSData *imageData = [aDecoder decodeObjectForKey:@"image"];
        
        if ([imageData length]) {
            self.image = [UIImage imageWithData:imageData];
        }
    }
    
    return self;
}

- (void)encodeWithCoder:(NSCoder *)enCoder
{
    [super encodeWithCoder:enCoder];
    
    NSData *data = UIImagePNGRepresentation(self.image);
    [enCoder encodeObject:data forKey:@"image"];
}


@end
