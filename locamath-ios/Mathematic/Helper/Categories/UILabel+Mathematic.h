//
//  UILabel+Mathematic.h
//  Mathematic
//
//  Created by alexbutenko on 6/27/13.
//  Copyright (c) 2013 Loca Apps. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "OlympiadTask.h"

@interface UILabel (Mathematic)

+ (UILabel *)olympiadTasksLabelWithText:(NSString *)text
                            placeholder:(NSString *)placeholder
                          withAlignment:(HintsAlignmentType)alignmentType;

@end
