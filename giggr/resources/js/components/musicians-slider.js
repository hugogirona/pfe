const SMOOTH_MS = 350; // matches CSS smooth-scroll settle time
const CLONE_MS  = 200; // debounce before teleporting out of clone zone

document.addEventListener('alpine:init', () => {
    window.Alpine.data('musiciansSlider', (real, clones) => ({
        current: 0,
        real,
        clones,
        teleporting: false,
        _timer: null,
        _items: null,

        init() {
            const track  = this.$refs.track;
            this._items  = Array.from(track.children);
            const first  = track.children[this.clones];
            track.scrollLeft = first.offsetLeft - (track.offsetWidth - first.offsetWidth) / 2;
        },

        domIdx(r)       { return r + this.clones; },
        isClone(domIdx) { return domIdx < this.clones || domIdx >= this.clones + this.real; },

        centerLeft(idx) {
            const track = this.$refs.track;
            const item  = track.children[idx];
            return item.offsetLeft - (track.offsetWidth - item.offsetWidth) / 2;
        },

        go(idx, smooth = true) {
            const track = this.$refs.track;
            const left  = this.centerLeft(idx);
            if (smooth) {
                track.scrollTo({ left, behavior: 'smooth' });
            } else {
                this.teleporting = true;
                track.style.scrollSnapType = 'none';
                track.classList.remove('scroll-smooth');
                track.scrollLeft = left;
                void track.getBoundingClientRect(); // force reflow so scroll-snap re-enables from the new position
                requestAnimationFrame(() => {
                    track.style.scrollSnapType = '';
                    track.classList.add('scroll-smooth');
                    this.teleporting = false;
                });
            }
        },

        navigate(dir) {
            const idx = this.domIdx(this.current) + dir;
            this.go(idx);
            this.current = (this.current + dir + this.real) % this.real;
            if (this.isClone(idx)) {
                setTimeout(() => this.go(this.domIdx(this.current), false), SMOOTH_MS);
            }
        },

        next()      { this.navigate(1); },
        prev()      { this.navigate(-1); },
        goToReal(r) { this.current = r; this.go(this.domIdx(r)); },

        onScroll() {
            if (this.teleporting) return;
            const track      = this.$refs.track;
            const viewCenter = track.scrollLeft + track.offsetWidth / 2;
            let closest = 0, minDist = Infinity;
            this._items.forEach((item, i) => {
                const d = Math.abs(item.offsetLeft + item.offsetWidth / 2 - viewCenter);
                if (d < minDist) { minDist = d; closest = i; }
            });
            this.current = ((closest - this.clones) % this.real + this.real) % this.real;

            clearTimeout(this._timer);
            if (this.isClone(closest)) {
                this._timer = setTimeout(() => this.go(this.domIdx(this.current), false), CLONE_MS);
            }
        },
    }));
});
