document.addEventListener('alpine:init', () => {
    window.Alpine.data('musiciansSlider', (count) => ({
        current:   0,
        count,
        pageCount: count,
        _ro:       null,

        init() {
            this._sync();
            this._ro = new ResizeObserver(() => this._sync());
            this._ro.observe(this.$refs.track);
        },

        destroy() {
            this._ro?.disconnect();
        },

        _visible() {
            const track = this.$refs.track;
            if (!track.children.length) return 1;
            const gap = parseFloat(getComputedStyle(track).columnGap) || 24;
            return Math.max(1, Math.round((track.offsetWidth + gap) / (track.children[0].offsetWidth + gap)));
        },

        _sync() {
            this.pageCount = Math.max(1, this.count - this._visible() + 1);
            if (this.current >= this.pageCount) this.current = this.pageCount - 1;
        },

        go(idx) {
            this.current = Math.max(0, Math.min(idx, this.pageCount - 1));
            const item = this.$refs.track.children[this.current];
            this.$refs.track.scrollTo({ left: item.offsetLeft, behavior: 'smooth' });
        },

        prev() { this.go((this.current - 1 + this.pageCount) % this.pageCount); },
        next() { this.go((this.current + 1) % this.pageCount); },
        goTo(i) { this.go(i); },

        onScroll() {
            const track = this.$refs.track;
            let closest = 0, minDist = Infinity;
            Array.from(track.children).forEach((item, i) => {
                const d = Math.abs(item.offsetLeft - track.scrollLeft);
                if (d < minDist) { minDist = d; closest = i; }
            });
            this.current = Math.min(closest, this.pageCount - 1);
        },
    }));
});