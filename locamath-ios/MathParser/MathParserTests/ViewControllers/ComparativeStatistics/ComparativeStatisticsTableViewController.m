//
//  ComparativeStatisticsTableViewController.m
//  Mathematic
//
//  Created by SanyaIOS on 27.06.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "ComparativeStatisticsTableViewController.h"
#import "ChildManager.h"
#import "UIFont+Mathematic.h"

@interface ComparativeStatisticsTableViewController ()

@property (strong, nonatomic) NSArray *childs;
@property (strong, nonatomic) NSArray *allRateChilds;

@end

@implementation ComparativeStatisticsTableViewController

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [self updateRateChildsWithFinishBlock:nil];

}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return [self.childs count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellIdentifier];
    }
    
    cell.backgroundColor = [UIColor clearColor];
    
    NSDictionary *childDict = [self.childs objectAtIndex:indexPath.row];
    
    NSNumber *childID = [childDict[kChildID] isKindOfClass:[NSNull class]] ? nil : childDict[kChildID];
    
    cell.textLabel.adjustsFontSizeToFitWidth = YES;
    
    if ([childID integerValue] == [[ChildManager sharedInstance].currentChild.identifier integerValue]) {
        cell.textLabel.font = [UIFont comparativeStatisticsCurrentChildFont];
        cell.textLabel.textColor = [UIColor yellowColor];
    } else {
        cell.textLabel.font = [UIFont comparativeStatisticsFont];
        cell.textLabel.textColor = [UIColor whiteColor];
    }
    
    cell.textLabel.text = [self ratingStringFromObjectAtIndex:indexPath.row];
    
    return cell;
}

#pragma mark - Helper

- (NSString *)ratingStringFromObjectAtIndex:(NSUInteger)index
{
    NSDictionary *childDict = [self.childs objectAtIndex:index];
    
    NSString *ratingString = [NSString stringWithFormat:@"%i.%@  %@  %@  %@", index + 1, childDict[kName],
                              [self changeNullOrEmptySpaceToUndefinedString:childDict[kCity] isString:YES],
                              [self changeNullOrEmptySpaceToUndefinedString:childDict[kCountry] isString:YES],
                              [self changeNullOrEmptySpaceToUndefinedString:childDict[kPoints] isString:NO]];
    
    return ratingString;
}

- (void)updateRateChildsWithFinishBlock:(ComparativeStatisticsFinishBlock)finishBlock
{
    [[ChildManager sharedInstance] childsRateWithSuccess:^(NSDictionary *childs) {
        
        [self sortWithKey:kPoints];
        self.allRateChilds = [[childs[kRate] allObjects] mutableCopy];
        [self.tableView reloadData];
        
        if (finishBlock) {
            finishBlock();
        }
        
    } failure:^(NSError *error) {
        [UIAlertView showErrorAlertViewWithMessage:[error localizedDescription]];
    }];
}

- (NSString *)changeNullOrEmptySpaceToUndefinedString:(id)obj isString:(BOOL)isString
{
    NSString *correctString = [obj isKindOfClass:[NSNumber class]] ? [obj stringValue] : obj;

    if ([obj isKindOfClass:[NSNull class]] && isString) {
        correctString = kUndefined;
    } else if ([obj isKindOfClass:[NSNull class]]) {
        correctString = @"0";
    }
    
    correctString = correctString.length == 0 ? kUndefined : correctString;

    return correctString;
}

- (void)selectWithKey:(NSString *)key andValue:(id)value
{
    self.childs = [self.allRateChilds mutableCopy];
    
    //find current child from childs array
    NSDictionary *dataForCurrentChild = [self.childs match:^BOOL(NSDictionary *obj) {
        return [obj[@"id"] integerValue] == [value integerValue];
    }];
    
    value = dataForCurrentChild[key];
    
    [self sortWithKey:kPoints];
    
    if (![key isEqualToString:kWorld]) {
        NSString *valueName = kUndefined;
        
        if ([value length] > 0) {
            valueName = value;
        }

        self.childs = [self.childs select:^BOOL(NSDictionary *obj) {
            NSString *objName = kUndefined;
            
            if (![obj[key]isKindOfClass:[NSNull class]]) {
                objName = obj[key];
            }
            
            objName = objName.length > 0 ? objName : kUndefined;
            
            return [objName isEqualToString:valueName];
        }];
    }
    
    [self.tableView reloadData];
}

- (void)sortWithKey:(NSString *)key 
{
    self.childs = [self.childs sortedArrayUsingComparator:^NSComparisonResult(NSDictionary *obj1, NSDictionary *obj2) {
        NSInteger idx1 = 0;
        NSInteger idx2 = 0;
        
        if (![obj1[key]isKindOfClass:[NSNull class]])
            idx1 = [obj1[key] integerValue];
        
        if (![obj2[key]isKindOfClass:[NSNull class]])
            idx2 = [obj2[key] integerValue];
        
        return idx1 < idx2;
    }];
}

@end
