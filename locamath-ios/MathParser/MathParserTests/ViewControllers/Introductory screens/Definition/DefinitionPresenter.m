//
//  DefinitionPresenter.m
//  Mathematic
//
//  Created by Developer on 21.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "DefinitionPresenter.h"
#import "PresentingSeguesStructure.h"
#import "Definition1ViewController.h"
#import "Definition2ViewController.h"
#import "Definition3ViewController.h"

@interface DefinitionPresenter ()

@end

@implementation DefinitionPresenter

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    [seguesStructure addLink:[Definition1ViewController class]];
    [seguesStructure addLink:[Definition2ViewController class]];
    [seguesStructure addLink:[Definition3ViewController class]];

    [[seguesStructure nextViewController] presentOnViewController:self finish:nil];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self dismissViewControllerAnimated:NO completion:nil];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

@end
