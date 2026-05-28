const equalizeCards = () => {
    for (const selector of ['[data-card="profile"]', '[data-card="announcement"]']) {
        const cards = [...document.querySelectorAll(selector)];
        if (cards.length === 0) continue;
        cards.forEach(c => c.style.minHeight = '');
        const max = Math.max(0, ...cards.map(c => c.offsetHeight));
        if (max > 0) cards.forEach(c => c.style.minHeight = `${max}px`);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(equalizeCards);

    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => requestAnimationFrame(equalizeCards));
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(equalizeCards, 150);
    });
});
