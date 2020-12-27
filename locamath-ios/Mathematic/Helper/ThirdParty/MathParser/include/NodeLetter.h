//
//  NodeLetter.h
//  Mathematic
//
//  Created by Dmitriy Gubanov on 07.02.13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import "NodeNumber.h"

@interface NodeLetter : NodeNumber

- (id)initWithLetter:(NSString*)letter;
+ (id)nodeWithLetter:(NSString*)letter;

@end
