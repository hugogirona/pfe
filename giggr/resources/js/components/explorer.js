document.addEventListener('alpine:init', () => {
    window.Alpine.data('explorerPage', (musicians, announcements) => ({
        activeTab:         'musiciens',
        city:              '',
        activeInstruments: [],
        activeGenres:      [],
        drawerOpen:        false,

        init() {
            this.$watch('drawerOpen', val => {
                document.body.style.overflow = val ? 'hidden' : '';
            });

            this.$nextTick(() => this._equalizeCards());
            this.$watch('activeTab',         () => this.$nextTick(() => this._equalizeCards()));
            this.$watch('city',              () => this.$nextTick(() => this._equalizeCards()));
            this.$watch('activeInstruments', () => this.$nextTick(() => this._equalizeCards()));
            this.$watch('activeGenres',      () => this.$nextTick(() => this._equalizeCards()));

            let resizeTimer;
            this._onResize = () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this._equalizeCards(), 150);
            };
            window.addEventListener('resize', this._onResize);
        },

        destroy() {
            window.removeEventListener('resize', this._onResize);
        },

        _equalizeCards() {
            const sel = this.activeTab === 'musiciens'
                ? '[data-card="musician"]'
                : '[data-card="announcement"]';
            const cards = [...this.$el.querySelectorAll(sel)];
            cards.forEach(c => c.style.minHeight = '');
            const max = Math.max(0, ...cards.map(c => c.offsetHeight));
            if (max > 0) cards.forEach(c => c.style.minHeight = `${max}px`);
        },

        get filteredMusicians() {
            return musicians.filter(m => {
                const cityOk  = !this.city || m.city.toLowerCase().includes(this.city.toLowerCase());
                const instrOk = !this.activeInstruments.length || this.activeInstruments.some(i => m.instruments.includes(i));
                const genreOk = !this.activeGenres.length      || this.activeGenres.some(g => m.genres.includes(g));
                return cityOk && instrOk && genreOk;
            });
        },

        get filteredAnnouncements() {
            return announcements.filter(a => {
                const cityOk  = !this.city || a.city.toLowerCase().includes(this.city.toLowerCase());
                const instrOk = !this.activeInstruments.length || this.activeInstruments.some(i => a.instruments.includes(i));
                const genreOk = !this.activeGenres.length      || this.activeGenres.some(g => a.genres.includes(g));
                return cityOk && instrOk && genreOk;
            });
        },

        get activeFiltersCount() {
            return (this.city ? 1 : 0) + this.activeInstruments.length + this.activeGenres.length;
        },

        get hasActiveFilters() {
            return this.activeFiltersCount > 0;
        },

        toggleInstrument(instr) {
            const idx = this.activeInstruments.indexOf(instr);
            idx > -1 ? this.activeInstruments.splice(idx, 1) : this.activeInstruments.push(instr);
        },

        toggleGenre(genre) {
            const idx = this.activeGenres.indexOf(genre);
            idx > -1 ? this.activeGenres.splice(idx, 1) : this.activeGenres.push(genre);
        },

        clearFilters() {
            this.city              = '';
            this.activeInstruments = [];
            this.activeGenres      = [];
        },
    }));
});
