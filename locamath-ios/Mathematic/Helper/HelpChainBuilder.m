//
//  HelpChainBuilder.m
//  
//
//  Created by Dmitriy Gubanov on 22.04.13.
//
//

#import "HelpChainBuilder.h"
#import "PresentableViewController.h"
#import "HelpStaticViewController.h"
#import "ChildManager.h"

#import "HelpPage.h"

@implementation HelpChainBuilder

+ (PresentingSeguesStructure *)helpChainWithLevelID:(NSString *)levelID
{
    PresentingSeguesStructure *seguesStructure = [PresentingSeguesStructure new];
    
    //parent cant have two child with same name ****child.name****
    NSArray *pages = [HelpPage findAllSortedBy:@"pageNum"
                                     ascending:YES
                                 withPredicate:[NSPredicate predicateWithFormat:
                                                @"identifier == %@ && child.name == %@",
                                                levelID,
                                             [ChildManager sharedInstance].currentChild.name]];
    
    NSInteger lastPage  = [[[pages lastObject] pageNum] integerValue];
    
    for (HelpPage *helpPage in pages) {
        if ([helpPage.pageType integerValue] == PageTypeAnimation) {
            [seguesStructure addLinkWithInstantiator:^PresentableViewController *{
                HelpStaticViewController *vc = [HelpStaticViewController new];
                vc.isViewUnloadingLocked = YES;
                
                if ([helpPage.pageNum integerValue] == lastPage) {
                    vc.isLastPage = YES;
                }
                
                vc.help = helpPage;
                return vc;
            }];
        } else {
            [seguesStructure addLinkWithInstantiator:^PresentableViewController *{
                HelpStaticViewController *vc = [HelpStaticViewController new];
                vc.isViewUnloadingLocked = YES;
                
                if ([helpPage.pageNum integerValue] == lastPage) {
                    vc.isLastPage = YES;
                }
                
                vc.help = helpPage;
                return vc;
            }];
        }
    }
    
    return seguesStructure;
}



@end
