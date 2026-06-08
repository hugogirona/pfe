export const localDatetime = {
  init() {
    this.render();

    document.addEventListener('livewire:navigated', () => this.render());
    document.addEventListener('livewire:init', () => {
      window.Livewire.hook('commit', ({ succeed }) => {
        succeed(() => this.render());
      });
    });
  },

  render(root = document) {
    root.querySelectorAll('time[data-local-time]').forEach((el) => this.renderTime(el));
    root.querySelectorAll('time[data-local-day]').forEach((el) => this.renderDay(el));
  },

  renderTime(el) {
    const date = this.parse(el);
    if (!date) return

    el.textContent = date.toLocaleTimeString(this.locale(), {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    });
  },

  renderDay(el) {
    const date = this.parse(el);
    if (!date) return

    el.textContent = this.relativeDay(date, el.dataset.today, el.dataset.yesterday);
  },

  relativeDay(date, today, yesterday) {
    const now = new Date();
    const diff = this.dayDiff(date, now);

    if (diff === 0) return today
    if (diff === 1) return yesterday
    if (diff <= 6) return this.capitalize(date.toLocaleDateString(this.locale(), { weekday: 'long' }))
    if (date.getFullYear() === now.getFullYear()) {
      return this.capitalize(date.toLocaleDateString(this.locale(), { weekday: 'short', day: 'numeric', month: 'short' }));
    }

    return this.capitalize(date.toLocaleDateString(this.locale(), { day: 'numeric', month: 'short', year: 'numeric' }));
  },

  parse(el) {
    const iso = el.getAttribute('datetime');
    return iso ? new Date(iso) : null
  },

  dayDiff(date, now) {
    const startOfDay = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate());
    return Math.round((startOfDay(now) - startOfDay(date)) / 86_400_000);
  },

  locale() {
    return document.documentElement.lang || 'fr'
  },

  capitalize(value) {
    return value.charAt(0).toUpperCase() + value.slice(1);
  },
}
