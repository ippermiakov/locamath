//
//  DWFParticleView.m
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

#import "DWFParticleView.h"
#import <QuartzCore/QuartzCore.h>

@interface DWFParticleView ()

@property (unsafe_unretained, nonatomic) BOOL isAnimating;
@property (unsafe_unretained, nonatomic) CGFloat emittingBirthRate;

@end

@implementation DWFParticleView
{
    CAEmitterLayer* _fireEmitter;
}

- (id)initWithFrame:(CGRect)frame
{
    self = [self initWithFrame:frame
                         image:[UIImage imageNamed:@"Particles_fire2.png"]
               emitterPosition:CGPointMake(50, 50)
                   emitterSize:CGSizeMake(5, 5)
                     birthRate:0
             emittingBirthRate:200
                 emissionRange:M_PI_2];
    if (self) {
        // Initialization code
    }
    return self;
}

- (id)initWithFrame:(CGRect)frame
              image:(UIImage *)image
    emitterPosition:(CGPoint)position
        emitterSize:(CGSize)size
          birthRate:(CGFloat)birthRate
  emittingBirthRate:(CGFloat)emittingBirthRate
      emissionRange:(CGFloat)emissionRange
{
    self = [super initWithFrame:frame];
    if (self) {
        // Initialization code
        
        self.emittingBirthRate = emittingBirthRate;
        
        //set ref to the layer
        self.fireEmitter = (CAEmitterLayer*)self.layer;
        
        //configure the emitter layer
        self.fireEmitter.emitterPosition = position;
        self.fireEmitter.emitterSize = size;
        
        CAEmitterCell* fire = [CAEmitterCell emitterCell];
        fire.birthRate = birthRate;
        fire.lifetime = 0.5;
        fire.lifetimeRange = 0.1;
        fire.contents = (id)[image CGImage];
        [fire setName:@"fire"];
        
        fire.velocity = 60;
        fire.velocityRange = 80;
        fire.emissionRange = emissionRange;
        
        fire.scaleSpeed = 0.1;
        fire.spin = 1;
        
        //add the cell to the layer and we're done
        self.fireEmitter.emitterCells = [NSArray arrayWithObject:fire];
    }
    return self;
}

+ (Class) layerClass //3
{
    //configure the UIView to have emitter layer
    return [CAEmitterLayer class];
}

-(void)setEmitterPositionFromTouch: (UITouch*)t
{
    //change the emitter's position
    self.fireEmitter.emitterPosition = [t locationInView:self];
}

-(void)setEmitterPositionAtPoint:(CGPoint)point
{
    self.fireEmitter.emitterPosition = point;
}

-(void)setIsEmitting:(BOOL)isEmitting
{
    //turn on/off the emitting of particles
    [self.fireEmitter setValue:[NSNumber numberWithInt:isEmitting ? self.emittingBirthRate:0]
               forKeyPath:@"emitterCells.fire.birthRate"];
}

-(void)moveAnimatedFromPoint:(CGPoint)point toPoint:(CGPoint)toPoint duration:(CGFloat)duration
{
    if (!self.isAnimating) {
        
        self.isAnimating = YES;
        
        CABasicAnimation* ba = [CABasicAnimation animationWithKeyPath:@"emitterPosition"];
        ba.fromValue = [NSValue valueWithCGPoint:point];
        ba.toValue = [NSValue valueWithCGPoint:toPoint];
        ba.duration = duration;
        ba.autoreverses = YES;
        ba.repeatCount = 1;
        
        [self setIsEmitting:YES];
        [self.layer addAnimation:ba forKey:nil];
        
        double delayInSeconds = duration;
        dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
        dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
            [self setIsEmitting:NO];
            self.isAnimating = NO;
        });
    }
}

#pragma mark - Helper

- (UIView *)hitTest:(CGPoint)point withEvent:(UIEvent *)event
{
    UIView *hitView = [super hitTest:point withEvent:event];
    
    // If the hitView is THIS view, return nil and allow hitTest:withEvent: to
    // continue traversing the hierarchy to find the underlying view.
    if (hitView == self) {
        return nil;
    }
    // Else return the hitView (as it could be one of this view's buttons):
    return hitView;
}


@end
