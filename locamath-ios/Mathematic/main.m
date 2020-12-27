//
//  main.m
//  Mathematic
//
//  Created by Alexander on 11/18/12.
//  Copyright (c) 2012 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "AppDelegate.h"

int main(int argc, char *argv[])
{
    @autoreleasepool {
        NSString *language = [[NSLocale preferredLanguages] objectAtIndex:0];

        //check that we support selected user language
        if ([[[NSBundle mainBundle] localizations] containsObject:language]) {
            //force en regions localization en_US, en_GB etc
            //http://hamishrickerby.com/2010/07/23/iphone-ipad-localizations-regions/
            NSString *domainName = [[NSBundle mainBundle] bundleIdentifier];
            [[NSUserDefaults standardUserDefaults] removePersistentDomainForName:domainName];
            
            NSString *locale = [[NSLocale currentLocale] objectForKey: NSLocaleCountryCode];
            
            NSString *firstChoice = [NSString stringWithFormat:@"%@_%@", language, locale];
            NSString *secondChoice = language;
            NSString *thirdChoice = @"en";
                
            if ([language isEqualToString:thirdChoice]) {
                [[NSUserDefaults standardUserDefaults] setObject:[NSArray arrayWithObjects:firstChoice, secondChoice, thirdChoice, nil] forKey:@"AppleLanguages"];
            }
        } else {
            //set en_US by default
            [[NSUserDefaults standardUserDefaults] setObject:[NSArray arrayWithObject:@"en_US"] forKey:@"AppleLanguages"];
        }

        return UIApplicationMain(argc, argv, nil, NSStringFromClass([AppDelegate class]));
    }
}
