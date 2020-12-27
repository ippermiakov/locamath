//
//  DWFParticleView.h
//  DrawWithFire
//
//  Created by Ray Wenderlich on 10/6/11.
//  Copyright 2011 Razeware LLC. All rights reserved.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
//

#import <UIKit/UIKit.h>

@interface DWFParticleView : UIView

@property (strong, nonatomic) CAEmitterLayer *fireEmitter;

-(void)setEmitterPositionFromTouch: (UITouch*)t;
-(void)setEmitterPositionAtPoint:(CGPoint)point;
-(void)setIsEmitting:(BOOL)isEmitting;

-(void)moveAnimatedFromPoint:(CGPoint)point toPoint:(CGPoint)toPoint duration:(CGFloat)duration;

- (id)initWithFrame:(CGRect)frame
              image:(UIImage *)image
    emitterPosition:(CGPoint)position
        emitterSize:(CGSize)size
          birthRate:(CGFloat)birthRate
  emittingBirthRate:(CGFloat)emittingBirthRate
      emissionRange:(CGFloat)emissionRange;

@end
