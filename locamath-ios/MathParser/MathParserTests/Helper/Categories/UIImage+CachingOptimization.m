//
//  UIImageView+CachingOptimization.m
//  Mathematic
//
//  Created by alexbutenko on 10/7/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "UIImage+CachingOptimization.h"
#import <objc/runtime.h>

@implementation UIImage (CachingOptimization)

+ (void)load
{
    //replace image loading from code, not nibs
    SEL originalImageNamedSelector = @selector(imageNamed:);
    SEL swizzledImageNamedSelector = @selector(replacementImageNamed:);
    Method originalImageNamedMethod = class_getClassMethod([UIImage class], originalImageNamedSelector);
    Method swizzledImageNamedMethod = class_getClassMethod([UIImage class], swizzledImageNamedSelector);
    
    class_addMethod(self,
					originalImageNamedSelector,
					class_getMethodImplementation(self, originalImageNamedSelector),
					method_getTypeEncoding(originalImageNamedMethod));
	class_addMethod(self,
					swizzledImageNamedSelector,
					class_getMethodImplementation(self, swizzledImageNamedSelector),
					method_getTypeEncoding(swizzledImageNamedMethod));
    
    method_exchangeImplementations(originalImageNamedMethod, swizzledImageNamedMethod);
    
    //replace nib loading
    Class targetClass = NSClassFromString(@"UIImageNibPlaceholder");
    SEL originalSelector = NSSelectorFromString(@"initWithCoder:");
    SEL swizzledSelector = @selector(hack_UIImageNibPlaceholder_initWithCoder:);
    Method originalMethod = class_getInstanceMethod(targetClass, originalSelector);
    Method swizzledMethod = class_getInstanceMethod(targetClass, swizzledSelector);
    
    class_addMethod(targetClass,
					originalSelector,
					class_getMethodImplementation(targetClass, originalSelector),
					method_getTypeEncoding(originalMethod));
	class_addMethod(targetClass,
					swizzledSelector,
					class_getMethodImplementation(targetClass, swizzledSelector),
					method_getTypeEncoding(swizzledMethod));
    
    method_exchangeImplementations(originalMethod, swizzledMethod);
}

+ (UIImage *)replacementImageNamed:(NSString *)imageName
{
    NSString *filePath = nil;
    
    //add extension if needed
    if (![imageName hasSuffix:@"png"]) {
        imageName = [imageName stringByAppendingString:@".png"];
    }
    
    filePath = [[NSBundle mainBundle] pathForResource:imageName ofType:@""];
    
    if (!filePath) {
        imageName = [imageName stringByReplacingOccurrencesOfString:@".png" withString:@"@2x.png"];
        filePath = [[NSBundle mainBundle] pathForResource:imageName ofType:@""];
    }
    
//    NSLog(@"load image named: %@ at path: %@", imageName, filePath);
    
    return [UIImage imageWithContentsOfFile:filePath];
}

// http://stackoverflow.com/questions/3440055/replacing-the-content-of-uiimages-loaded-from-xib-at-runtime
- (id)hack_UIImageNibPlaceholder_initWithCoder:(NSCoder *)coder
{
    NSString *name = [coder decodeObjectForKey:@"UIResourceName"];
    
    NSString *filePath = [[NSBundle mainBundle] pathForResource:name ofType:@""];
    
    if (!filePath) {
        name = [name stringByReplacingOccurrencesOfString:@".png" withString:@"@2x.png"];
        filePath = [[NSBundle mainBundle] pathForResource:name ofType:@""];
    }
    
//    NSLog(@"loading image named: %@ path: %@", name, filePath);
    
    return [[UIImage alloc] initWithContentsOfFile:filePath];
}

@end
