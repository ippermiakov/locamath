//
//  DKPredicateBuilder.m
//  DiscoKit
//
//  Created by Keith Pitt on 12/07/11.
//  Copyright 2011 Mostly Disco. All rights reserved.
//

#import "DKPredicateBuilder.h"

@implementation DKPredicateBuilder

@synthesize predicates, sorters, limit, offset;

- (id)init {
        
    if ((self = [super init])) {
                
        // Create the predicates mutable array
        predicates = [[NSMutableArray alloc] init];
        
        // Create the sorters mutable array
        sorters = [[NSMutableArray alloc] init];
        
    }
    
    return self;
    
}

- (id)where:(DKPredicate *)predicate {
    
    [self.predicates addObject:predicate];
    
    return self;
    
}

- (id)where:(NSString *)key isFalse:(BOOL)value {
    
    [self where:key isTrue:!value];
    
    return self;
    
}

- (id)where:(NSString *)key isTrue:(BOOL)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K = %@", key, @(value)]
                                predicateType:value ? DKPredicateTypeIsTrue : DKPredicateTypeIsFalse
                                         info:@{@"column": key}]];
    
    return self;
    
}

- (id)where:(NSString *)key isNull:(BOOL)value {
    
    if (value == YES) {
        
        [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K == nil", key]
                                    predicateType:value ? DKPredicateTypeIsTrue : DKPredicateTypeIsFalse
                                             info:@{@"column": key}]];
        
    } else {
        
        [self where:key isNotNull:YES];
        
    }
    
    return self;
    
}

- (id)where:(NSString *)key isNotNull:(BOOL)value {
    
    if (value == YES) {
        
        [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K != nil", key]
                                    predicateType:value ? DKPredicateTypeIsTrue : DKPredicateTypeIsFalse
                                             info:@{@"column": key}]];
        
    } else {
        
        [self where:key isNull:YES];
        
    }
    
    return self;
    
}

- (id)where:(NSString *)key equals:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K = %@", key, value]
                                predicateType:DKPredicateTypeEquals
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key doesntEqual:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K != %@", key, value]
                                predicateType:DKPredicateTypeNotEquals
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key isIn:(NSArray *)values {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K IN (%@)", key, values]
                                predicateType:DKPredicateTypeIn
                                         info:@{@"column": key,
                                               @"values": values}]];
    
    return self;
    
}

- (id)where:(NSString *)key isNotIn:(NSArray *)values {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"NOT %K IN (%@)", key, values]
                                predicateType:DKPredicateTypeNotIn
                                         info:@{@"column": key,
                                               @"values": values}]];
    
    return self;
    
}

- (id)where:(NSString *)key startsWith:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K BEGINSWITH[cd] %@", key, value]
                                predicateType:DKPredicateTypeStartsWith
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key doesntStartWith:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"NOT %K BEGINSWITH[cd] %@", key, value]
                                predicateType:DKPredicateTypeDoesntStartWith
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key endsWith:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K ENDSWITH[cd] %@", key, value]
                                predicateType:DKPredicateTypeEndsWith
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key doesntEndWith:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"NOT %K ENDSWITH[cd] %@", key, value]
                                predicateType:DKPredicateTypeDoesntEndWith
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key contains:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K CONTAINS[cd] %@", key, value]
                                predicateType:DKPredicateTypeContains
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key like:(NSString *)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K LIKE[cd] %@", key, value]
                                predicateType:DKPredicateTypeLike
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key greaterThan:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K > %@", key, value]
                                predicateType:DKPredicateTypeGreaterThan
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
}

- (id)where:(NSString *)key greaterThanOrEqualTo:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K >= %@", key, value]
                                predicateType:DKPredicateTypeGreaterThanOrEqualTo
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key lessThan:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K < %@", key, value]
                                predicateType:DKPredicateTypeLessThan
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key lessThanOrEqualTo:(id)value {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"%K <= %@", key, value]
                                predicateType:DKPredicateTypeLessThanOrEqualTo
                                         info:@{@"column": key,
                                               @"value": value}]];
    
    return self;
    
}

- (id)where:(NSString *)key between:(id)first andThis:(id)second {
    
    [self where:[DKPredicate withPredicate:[NSPredicate predicateWithFormat:@"(%K >= %@) AND (%K <= %@)", key, first, key, second]
                                predicateType:DKPredicateTypeBetween
                                         info:@{@"column": key,
                                               @"first": first,
                                               @"second": second}]];
    
    return self;
    
}

- (id)orderBy:(NSString *)column ascending:(BOOL)ascending {
    
    // Create the sort descriptor
    NSSortDescriptor * sort = [[NSSortDescriptor alloc] initWithKey:column
                                                          ascending:ascending];
    
    // Add it to the sorters array
    [self.sorters addObject:sort];
    
    // Release the sort
    [sort release];
    
    return self;
    
}

- (id)offset:(int)value {
    
    // Set the offset
    self.offset = @(value);
    
    return self;
    
}

- (id)limit:(int)value {
    
    // Set the limit
    self.limit = @(value);
    
    return self;
    
}

- (NSCompoundPredicate *)compoundPredicate {
    
    // Collect all the predicates
    NSMutableArray * collectedPredicates = [NSMutableArray array];
    for (DKPredicate * relPredicate in predicates) {
        [collectedPredicates addObject:relPredicate.predicate];
    }
    
    // Add the predicates to a NSCompoundPredicate
    NSCompoundPredicate * compoundPredicate = [[NSCompoundPredicate alloc] initWithType:NSAndPredicateType
                                                                          subpredicates:collectedPredicates];
    
    return [compoundPredicate autorelease];
}

- (void)dealloc {
    
    [predicates release];
    [sorters release];
    
    [limit release];
    [offset release];
    
    [super dealloc];
    
}

@end