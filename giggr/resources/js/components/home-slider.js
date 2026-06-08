import { homeSlider as config } from '../settings.js';

export const homeSlider = {
  init() {
    document.addEventListener('alpine:init', () => {
      window.Alpine.data('homeSlider', (count) => ({
        current: 0,
        count,
        pageCount: count,
        _resizeObserver: null,
        _scrollFrame: null,

        init() {
          this._sync();
          this._resizeObserver = new ResizeObserver(() => this._sync());
          this._resizeObserver.observe(this.$refs.track);
        },

        destroy() {
          this._resizeObserver?.disconnect();
          if (this._scrollFrame) cancelAnimationFrame(this._scrollFrame);
        },

        _metrics() {
          const track = this.$refs.track;
          const card = track.children[0];
          if (!card) return { track, visible: 1, inset: 0 };

          const gap = parseFloat(getComputedStyle(track).columnGap) || config.fallbackGap;
          const visible = Math.max(1, Math.round((track.offsetWidth + gap) / (card.offsetWidth + gap)));
          const groupWidth = visible * card.offsetWidth + (visible - 1) * gap;
          const inset = Math.max(0, (track.offsetWidth - groupWidth) / 2);
          return { track, visible, inset };
        },

        _sync() {
          this.pageCount = Math.max(1, this.count - this._metrics().visible + 1);
          if (this.current >= this.pageCount) this.current = this.pageCount - 1;
        },

        go(idx) {
          this.current = Math.max(0, Math.min(idx, this.pageCount - 1));
          const { track, inset } = this._metrics();
          const item = track.children[this.current];
          track.scrollTo({ left: item.offsetLeft - inset, behavior: 'smooth' });
        },

        prev() { this.go((this.current - 1 + this.pageCount) % this.pageCount); },
        next() { this.go((this.current + 1) % this.pageCount); },

        onScroll() {
          if (this._scrollFrame) return

          this._scrollFrame = requestAnimationFrame(() => {
            this._scrollFrame = null;
            const { track, inset } = this._metrics();
            let closest = 0, minDist = Infinity;
            Array.from(track.children).forEach((item, i) => {
              const d = Math.abs(item.offsetLeft - inset - track.scrollLeft);
              if (d < minDist) { minDist = d; closest = i; }
            });
            this.current = Math.min(closest, this.pageCount - 1);
          });
        },
      }));
    });
  },
}
