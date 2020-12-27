@interface NSManagedObject (Serialization)

- (NSDictionary*) toDictionary;

- (void) populateFromDictionary:(NSDictionary*)dict;

+ (NSManagedObject*) createManagedObjectFromDictionary:(NSDictionary*)dict
                                             inContext:(NSManagedObjectContext*)context;

@end