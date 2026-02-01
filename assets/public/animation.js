/**
 * Fancy Scroll Anims - Frontend Animation Engine
 *
 * @package Fancy_Scroll_Anims
 */

document.addEventListener('DOMContentLoaded', () => {
	'use strict';

	const animations = document.querySelectorAll('.fancy-scroll-anim');
	
	if (animations.length === 0) {
		return;
	}

	/**
	 * Scroll Animation Controller
	 */
	class ScrollAnimation {
		constructor(container) {
			this.container = container;
			this.frames = JSON.parse(container.dataset.frames || '[]');
			this.easing = container.dataset.easing || 'linear';
			this.loops = parseFloat(container.dataset.loops || '1');
			this.currentFrame = 0;
			this.img = container.querySelector('.fsa-frame');
			this.ticking = false;
			
			if (!this.img || this.frames.length === 0) {
				return;
			}

			this.preloadFrames();
			this.setupScrollListener();
		}

		/**
		 * Preload all frames for smooth playback
		 */
		preloadFrames() {
			this.frames.forEach(src => {
				const img = new Image();
				img.src = src;
			});
		}

		/**
		 * Setup scroll listener with IntersectionObserver
		 */
		setupScrollListener() {
			const observer = new IntersectionObserver(
				(entries) => {
					entries.forEach(entry => {
						if (entry.isIntersecting) {
							this.activate();
						} else {
							this.deactivate();
						}
					});
				},
				{
					threshold: 0,
					rootMargin: '0px'
				}
			);

			observer.observe(this.container);
		}

		/**
		 * Activate scroll tracking
		 */
		activate() {
			this.scrollHandler = this.requestTick.bind(this);
			window.addEventListener('scroll', this.scrollHandler, { passive: true });
			this.requestTick();
		}

		/**
		 * Deactivate scroll tracking
		 */
		deactivate() {
			if (this.scrollHandler) {
				window.removeEventListener('scroll', this.scrollHandler);
			}
		}

		/**
		 * Request animation frame for scroll handling
		 */
		requestTick() {
			if (!this.ticking) {
				this.ticking = true;
				requestAnimationFrame(() => {
					this.handleScroll();
					this.ticking = false;
				});
			}
		}

		/**
		 * Handle scroll events
		 */
		handleScroll() {
			const rect = this.container.getBoundingClientRect();
			const viewportHeight = window.innerHeight;
			
			// Calculate when element enters bottom of viewport (start)
			// to when it exits top of viewport (end)
			const elementHeight = rect.height;
			const scrollStart = viewportHeight;
			const scrollEnd = -elementHeight;
			const scrollRange = scrollStart - scrollEnd;
			
			// Current position (inverted so positive = scrolling down)
			const currentScroll = scrollStart - rect.top;
			
			// Calculate progress (0 to 1)
			let progress = currentScroll / scrollRange;
			progress = Math.max(0, Math.min(1, progress));
			
			// Apply easing
			progress = this.applyEasing(progress);
			
			// Apply loops
			const loopedProgress = (progress * this.loops) % 1;
			
			// Calculate frame index
			const frameIndex = Math.floor(loopedProgress * this.frames.length);
			const clampedIndex = Math.max(0, Math.min(this.frames.length - 1, frameIndex));
			
			// Update frame if changed
			if (clampedIndex !== this.currentFrame) {
				this.currentFrame = clampedIndex;
				this.updateFrame();
			}
		}

		/**
		 * Apply easing function to progress
		 */
		applyEasing(t) {
			switch (this.easing) {
				case 'ease-in':
					return t * t;
				case 'ease-out':
					return t * (2 - t);
				case 'ease-in-out':
					return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
				default: // linear
					return t;
			}
		}

		/**
		 * Update displayed frame
		 */
		updateFrame() {
			if (this.img && this.frames[this.currentFrame]) {
				this.img.src = this.frames[this.currentFrame];
			}
		}
	}

	// Initialize all animations
	animations.forEach(container => {
		new ScrollAnimation(container);
	});
});
