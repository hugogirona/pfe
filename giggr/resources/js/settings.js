// Centralized tuning values for the front-end components.
// Kept in one place so the animation/interaction "feel" can be adjusted
// without digging through component logic.

export const featureStack = {
    // Vertical offset (% of card height) incoming cards start from before stacking.
    incomingOffset: 500,
    // ScrollTrigger scrub smoothing, in seconds.
    scrub: 0.5,
    // Fade/slide-in duration per card, in timeline seconds.
    cardDuration: 1,
    // Easing curve for the stacking timeline.
    ease: 'power1.out',
    // anticipatePin lookahead to avoid a pin flicker.
    anticipatePin: 1,
};

export const homeSlider = {
    // Fallback column gap (px) used when the computed style is unavailable.
    fallbackGap: 24,
};
