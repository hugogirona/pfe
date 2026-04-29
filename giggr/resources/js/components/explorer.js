document.addEventListener('alpine:init', () => {
    Alpine.data('explorerTabs', () => ({
        activeTab: 'musiciens',

        init() {
            const tab = new URLSearchParams(window.location.search).get('tab');
            if (tab === 'annonces' || tab === 'musiciens') this.activeTab = tab;

            this.$watch('activeTab', () => this.$nextTick(() => this._equalizeCards()));
            this.$nextTick(() => this._equalizeCards());

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
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            const el = document.querySelector('[x-data^="explorerTabs"]');
            if (!el) return;
            Alpine.nextTick(() => Alpine.$data(el)._equalizeCards?.());
        });
    });
});
