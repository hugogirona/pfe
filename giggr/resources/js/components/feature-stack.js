import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import { featureStack as config } from '../settings.js';

gsap.registerPlugin(ScrollTrigger);

let timeline = null;
let enabled = false;
let resizeTimer = null;

function canStack() {
    return !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        && window.innerHeight >= config.minViewportHeight;
}

function teardown() {
    timeline?.scrollTrigger?.kill();
    timeline?.kill();
    timeline = null;
    document.querySelectorAll('[data-stack-pin]').forEach((pin) => pin.classList.remove('is-stacking'));
    gsap.set('[data-stack-card]', { clearProps: 'transform,opacity,visibility' });
    enabled = false;
}

function build() {
    const section = document.querySelector('[data-stack]');
    if (!section || !canStack()) return;

    const pin = section.querySelector('[data-stack-pin]');
    const cards = gsap.utils.toArray('[data-stack-card]', section);
    if (!pin || cards.length < 2) return;

    const incoming = cards.slice(1);
    pin.classList.add('is-stacking');
    cards.forEach((card) => gsap.set(card, { rotation: parseFloat(card.dataset.rot) || 0 }));
    gsap.set(incoming, { yPercent: config.incomingOffset });

    timeline = gsap.timeline({
        defaults: { ease: config.ease },
        scrollTrigger: {
            trigger: section,
            start: 'top top',
            end: () => `+=${window.innerHeight * incoming.length}`,
            scrub: config.scrub,
            pin,
            anticipatePin: config.anticipatePin,
            invalidateOnRefresh: true,
        },
    });

    incoming.forEach((card) => timeline.to(card, { yPercent: 0, duration: config.cardDuration }));
    enabled = true;
}

function rebuild() {
    teardown();
    build();
}

function onResize() {
    if (canStack() === enabled) return;
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(rebuild, config.resizeDebounce);
}

document.addEventListener('DOMContentLoaded', build);
document.addEventListener('livewire:navigated', rebuild);
window.addEventListener('resize', onResize);
