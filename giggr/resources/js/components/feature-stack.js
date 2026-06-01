import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import { featureStack as config } from '../settings.js';

gsap.registerPlugin(ScrollTrigger);

let timeline = null;

function buildFeatureStack() {
    const section = document.querySelector('[data-stack]');
    if (!section) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const pin = section.querySelector('[data-stack-pin]');
    const cards = gsap.utils.toArray('[data-stack-card]', section);
    if (!pin || cards.length < 2) return;

    const incoming = cards.slice(1);
    pin.classList.add('is-stacking');
    cards.forEach((card) => gsap.set(card, { rotation: parseFloat(card.dataset.rot) || 0 }));
    gsap.set(incoming, { autoAlpha: 0, yPercent: config.incomingOffset });

    timeline = gsap.timeline({
        defaults: { ease: config.ease },
        scrollTrigger: {
            trigger: section,
            start: 'top top',
            end: () => '+=' + window.innerHeight * incoming.length,
            scrub: config.scrub,
            pin: pin,
            anticipatePin: config.anticipatePin,
            invalidateOnRefresh: true,
        },
    });

    incoming.forEach((card) => timeline.to(card, { autoAlpha: 1, yPercent: 0, duration: config.cardDuration }));
}

function refresh() {
    timeline?.scrollTrigger?.kill();
    timeline?.kill();
    timeline = null;
    buildFeatureStack();
}

document.addEventListener('DOMContentLoaded', buildFeatureStack);
document.addEventListener('livewire:navigated', refresh);
