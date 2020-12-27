//
//  ChooseLocationPopupViewController.m
//  Mathematic
//
//  Created by Dmitriy Gubanov on 12.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ChooseLocationPopupViewController.h"
#import "Parent.h"
#import "Definition1ViewController.h"
#import "ChildManager.h"
#import "NSManagedObject+Serialization.h"
#import "MBProgressHUD.h"
#import "UIAlertView+Error.h"
#import "MTHTTPClient.h"
#import "MBProgressHUD+Mathematic.h"

@interface ChooseLocationPopupViewController ()

@property(strong, nonatomic) UIActivityIndicatorView *activityIndicator;
@property(strong, nonatomic) CLGeocoder             *geocoder;
@property(strong, nonatomic) CLLocationManager      *locationManager;
@property(unsafe_unretained, nonatomic) NSInteger   locationNeedsCounter;

@end

@implementation ChooseLocationPopupViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.geocoder        = [CLGeocoder new];
        self.locationManager = [CLLocationManager new];
        self.locationManager.delegate = self;
        self.locationNeedsCounter = 0;
        [self.locationManager startUpdatingLocation];
    }
    return self;
}

- (void)dealloc
{
    [self.locationManager stopUpdatingLocation];
    self.locationManager.delegate = nil;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.country.text = [DataUtils currentParent].country;
    self.city.text    = [DataUtils currentParent].city;
    
    [ChildManager sharedInstance].currentChild.isLocationPopupShown = @YES;
    
    [[NSManagedObjectContext contextForCurrentThread] saveToPersistentStoreAndWait];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewDidUnload
{
    [self setCountry:nil];
    [self setCity:nil];
    [super viewDidUnload];
}

#pragma mark - Actions

- (IBAction)onTapCountry:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
}

- (IBAction)onTapRegion:(id)sender
{
    //[self.soundManager playSoundClient:self.class andClientSelector:_cmd];
}

- (IBAction)onTapContinue:(id)sender
{
    [self.soundManager playTouchSoundNamed:self.soundManager.soundNames[0] loop:NO];
    
    [MBProgressHUD showHUDForWindow];
    
    [[MTHTTPClient sharedMTHTTPClient] parentUpdateLocationWithSuccess:^(BOOL finished, NSError *error) {
        [MBProgressHUD hideHUDForWindow];
        [self presentNextViewController];
    } failure:^(BOOL finished, NSError *error) {
        [MBProgressHUD hideHUDForWindow];
        [self presentNextViewController];
    }];
}

- (void)showActivity
{
    CGSize  size = CGSizeMake(30, 30);
    CGPoint origin;
    origin.x = self.view.center.x - size.width  / 2;
    origin.y = self.view.center.y - size.height / 2;
    CGRect activityFrame = {.origin = origin, size = size};
    
    self.activityIndicator = [[UIActivityIndicatorView alloc] initWithFrame:activityFrame];
    [self.activityIndicator startAnimating];
    
    [self.view addSubview:self.activityIndicator];
}

- (void)hideActivity
{
    [self.activityIndicator removeFromSuperview];
    self.activityIndicator = nil;
}

- (void)setLocationNeedsCounter:(NSInteger)locationNeedsCounter
{
    _locationNeedsCounter = locationNeedsCounter;

    if (locationNeedsCounter == 1) {
        [self showActivity];
        
        [self.geocoder reverseGeocodeLocation:self.locationManager.location completionHandler:^(NSArray *placemarks, NSError *error) {
            [self hideActivity];
            
            if (error == nil) {
                NSDictionary *address = [placemarks.lastObject addressDictionary];
                [DataUtils currentParent].city    = [address objectForKey:@"City"];
                [DataUtils currentParent].country = [address objectForKey:@"Country"];
                
                [DataUtils currentParent].latitude = @(self.locationManager.location.coordinate.latitude);
                [DataUtils currentParent].longitude = @(self.locationManager.location.coordinate.longitude);
                
                self.country.text = [DataUtils currentParent].country;
                self.city.text    = [DataUtils currentParent].city ;
            } else {
                [UIAlertView showErrorAlertViewWithMessage:NSLocalizedString(@"Failed to get location, check your network connection", @"Choose location popup")];
            }
        }];
    }
}

#pragma mark - CLLocationManagerDelegate

- (void)locationManager:(CLLocationManager *)manager didChangeAuthorizationStatus:(CLAuthorizationStatus)status
{
    if (status == kCLAuthorizationStatusAuthorized) {
        self.locationNeedsCounter++;
    }
}

- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error
{
    [UIAlertView showAlertViewWithTitle:NSLocalizedString(@"Location Service Disabled", nil)
                                message:NSLocalizedString(@"To re-enable, please go to Settings and turn on Location Service for this app.", nil)
                                handler:nil];
}

#pragma mark Setters&Getters

- (void)dismiss
{
    if ([self.parentVC respondsToSelector:@selector(didSelectedRegion)]) {
        [self.parentVC didSelectedRegion];
    }
    
    [super dismiss];
}

@end
