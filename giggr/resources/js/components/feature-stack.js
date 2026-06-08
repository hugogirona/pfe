import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import { featureStack as config } from '../settings.js';

export const featureStack = {
  timeline: null,
  enabled: false,
  resizeTimer: null,

  init() {
    gsap.registerPlugin(ScrollTrigger);

    document.addEventListener('DOMContentLoaded', () => this.build());
    document.addEventListener('livewire:navigated', () => this.rebuild());
    window.addEventListener('resize', () => this.onResize());
  },

  canStack() {
    return !window.matchMedia('(prefers-reduced-motion: reduce)').matches
      && window.innerHeight >= config.minViewportHeight
  },

  teardown() {
    this.timeline?.scrollTrigger?.kill();
    this.timeline?.kill();
    this.timeline = null;
    document.querySelectorAll('[data-stack-pin]').forEach((pin) => pin.classList.remove('is-stacking'));
    gsap.set('[data-stack-card]', { clearProps: 'transform,opacity,visibility' });
    this.enabled = false;
  },

  build() {
    const section = document.querySelector('[data-stack]');
    if (!section || !this.canStack()) return

    const pin = section.querySelector('[data-stack-pin]');
    const cards = gsap.utils.toArray('[data-stack-card]', section);
    if (!pin || cards.length < 2) return

    const incoming = cards.slice(1);
    pin.classList.add('is-stacking');
    cards.forEach((card) => gsap.set(card, { rotation: parseFloat(card.dataset.rot) || 0 }));
    gsap.set(incoming, { yPercent: config.incomingOffset });

    this.timeline = gsap.timeline({
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

    incoming.forEach((card) => this.timeline.to(card, { yPercent: 0, duration: config.cardDuration }));
    this.enabled = true;
  },

  rebuild() {
    this.teardown();
    this.build();
  },

  onResize() {
    if (this.canStack() === this.enabled) return

    clearTimeout(this.resizeTimer);
    this.resizeTimer = setTimeout(() => this.rebuild(), config.resizeDebounce);
  },
}
