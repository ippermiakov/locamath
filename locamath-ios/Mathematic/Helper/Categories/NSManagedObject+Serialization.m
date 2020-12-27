#import "NSManagedObject+Serialization.h"
#import "NSDate+UnixtimeWithoutLocaleOffset.h"

@implementation NSManagedObject (Serialization)

#define DATE_ATTR_PREFIX @"dAtEaTtr:"

#pragma mark -
#pragma mark Dictionary conversion methods

- (NSDictionary*) toDictionaryWithTraversalHistory:(NSMutableArray*)traversalHistory {
    NSDictionary* attributes = [[self entity] attributesByName];// allKeys];
    NSDictionary* relationships = [[self entity] relationshipsByName]; //allKeys];
    NSMutableDictionary* dict = [NSMutableDictionary dictionaryWithCapacity:
                                 [attributes count] + [relationships count] + 1];

    NSMutableArray *localTraversalHistory = nil;
    
    if (traversalHistory == nil) {
        localTraversalHistory = [NSMutableArray arrayWithCapacity:[attributes count] + [relationships count] + 1];
    } else {
        localTraversalHistory = traversalHistory;
    }
    
    [localTraversalHistory addObject:self];
    
    // not needed in our case, uncomment if need
//    [dict setObject:[[self class] description] forKey:@"class"];
    
    for (NSString* attr in [attributes allKeys]) {
        // skip identifier
        NSAttributeDescription *attrDiscr = attributes[attr];
        if ([attrDiscr.userInfo[@"shouldSkip"] isEqualToString:@"1"]) continue;
        
        NSObject* value = [self valueForKey:attr];

        if (value != nil) {
			
			NSString *key = nil;
			NSAttributeDescription *attrDiscr = attributes[attr];
			if (attrDiscr.userInfo != nil)
				key = (attrDiscr.userInfo)[attr];
			if (key == nil)
				key = attr;
			
            if ([value isKindOfClass:[NSDate class]]) {
                NSTimeInterval GMTUnixTime = [(NSDate *)value timeIntervalSince1970GMT];
//                NSString *dateAttr = [NSString stringWithFormat:@"%@%@", DATE_ATTR_PREFIX, key];
                dict[key] = @(GMTUnixTime);
            } else {
                dict[key] = value;
            }
        }
    }
    
    for (NSString* relationship in relationships) {
         // skip player
        NSAttributeDescription *attrDiscr = relationships[relationship];
        if ([attrDiscr.userInfo[@"shouldSkip"] isEqualToString:@"1"]) continue;
        
        NSObject* value = [self valueForKey:relationship];
        
        if ([value isKindOfClass:[NSSet class]] || [value isKindOfClass:[NSOrderedSet class]]) {
            // To-many relationship
            
            // The core data set holds a collection of managed objects
            id relatedObjects = value;
            
            // Our set holds a collection of dictionaries
            NSMutableArray* dictSet = [NSMutableArray arrayWithCapacity:[relatedObjects count]];
            
            for (NSManagedObject* relatedObject in relatedObjects) {
                if ([localTraversalHistory containsObject:relatedObject] == NO) {
                    [dictSet addObject:[relatedObject toDictionaryWithTraversalHistory:localTraversalHistory]];
                }
            }
            
            dict[relationship] = [NSArray arrayWithArray:dictSet];
        }
        else if ([value isKindOfClass:[NSManagedObject class]]) {
            // To-one relationship
            
            NSManagedObject* relatedObject = (NSManagedObject*) value;
            
            if ([localTraversalHistory containsObject:relatedObject] == NO) {
                // Call toDictionary on the referenced object and put the result back into our dictionary.
                dict[relationship] = [relatedObject toDictionaryWithTraversalHistory:localTraversalHistory];
            }
        }
    }
    
    if (traversalHistory == nil) {
        [localTraversalHistory removeAllObjects];
    }
    
    return dict;
}

- (NSDictionary*) toDictionary {
    return [self toDictionaryWithTraversalHistory:nil];
}

+ (id) decodedValueFrom:(id)codedValue forKey:(NSString*)key {
    if ([key hasPrefix:DATE_ATTR_PREFIX] == YES) {
        // This is a date attribute
        NSTimeInterval dateAttr = [(NSNumber*)codedValue doubleValue];
        
        return [NSDate dateWithTimeIntervalSinceReferenceDate:dateAttr];
    } else {
        // This is an attribute
        return codedValue;
    }
}

- (void) populateFromDictionary:(NSDictionary*)dict
{
    NSManagedObjectContext* context = [self managedObjectContext];
    
    for (NSString* key in dict) {
        if ([key isEqualToString:@"class"]) {
            continue;
        }
        
        NSObject* value = dict[key];
        
        if ([value isKindOfClass:[NSDictionary class]]) {
            // This is a to-one relationship
            NSManagedObject* relatedObject =
            [NSManagedObject createManagedObjectFromDictionary:(NSDictionary*)value
                                                           inContext:context];
            
            [self setValue:relatedObject forKey:key];
        }
        else if ([value isKindOfClass:[NSArray class]]) {
            // This is a to-many relationship
            NSArray* relatedObjectDictionaries = (NSArray*) value;
            
            // Get a proxy set that represents the relationship, and add related objects to it.
            // (Note: this is provided by Core Data)
            NSMutableSet* relatedObjects = [self mutableSetValueForKey:key];
            
            for (NSDictionary* relatedObjectDict in relatedObjectDictionaries) {
                NSManagedObject* relatedObject =
                [NSManagedObject createManagedObjectFromDictionary:relatedObjectDict
                                                               inContext:context];
                [relatedObjects addObject:relatedObject];
            }
        }
        else if (value != nil) {
            [self setValue:[NSManagedObject decodedValueFrom:value forKey:key] forKey:key];
        }
    }
}

+ (NSManagedObject*) createManagedObjectFromDictionary:(NSDictionary*)dict
                                                   inContext:(NSManagedObjectContext*)context
{
    NSString* class = dict[@"class"];
        
    NSManagedObject* newObject =
    (NSManagedObject*)[NSEntityDescription insertNewObjectForEntityForName:class
                                                          inManagedObjectContext:context];
    
    [newObject populateFromDictionary:dict];
    
    return newObject;
}

@end
