import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        visible: false,
        message: '',
        type: 'success',
        show(message, type = 'success') {
            this.message = message;
            this.type = type;
            this.visible = true;
            setTimeout(() => { this.visible = false; }, 4000);
        },
    });

    Alpine.data('searchBox', () => ({
        query: '',
        results: [],
        open: false,
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            const res = await fetch(`/search/suggest?q=${encodeURIComponent(this.query)}`);
            this.results = await res.json();
            this.open = this.results.length > 0;
        },
    }));

    Alpine.data('cartDrawer', () => ({
        open: false,
        items: [],
        count: 0,
        total: 0,
        loading: false,
        async toggle() {
            this.open = !this.open;
            if (this.open) await this.refresh();
        },
        async refresh() {
            this.loading = true;
            try {
                const res = await fetch('/cart/summary');
                if (res.ok) {
                    const data = await res.json();
                    this.items = data.items;
                    this.count = data.count;
                    this.total = data.total;
                }
            } finally {
                this.loading = false;
            }
        },
    }));
});

Alpine.start();
