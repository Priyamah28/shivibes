import './bootstrap';

import Alpine from 'alpinejs';
import { registerCommerce } from './store/commerce';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    registerCommerce(Alpine);

    Alpine.store('toast', {
        visible: false,
        message: '',
        type: 'success',
        show(message, type = 'success') {
            this.message = message;
            this.type = type;
            this.visible = true;
            clearTimeout(this._timer);
            this._timer = setTimeout(() => {
                this.visible = false;
            }, 4000);
        },
    });

    Alpine.data('searchBox', () => ({
        query: '',
        results: [],
        open: false,
        loading: false,
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            try {
                const res = await fetch(`/search/suggest?q=${encodeURIComponent(this.query)}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.results = await res.json();
                this.open = this.results.length > 0;
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('cartDrawer', () => ({
        open: false,
        items: [],
        count: 0,
        total: 0,
        loading: false,
        removing: null,

        init() {
            window.addEventListener('cart-updated', (e) => {
                if (e.detail) {
                    this.items = e.detail.items ?? this.items;
                    this.count = e.detail.count ?? this.count;
                    this.total = e.detail.total ?? this.total;
                    Alpine.store('nav').setFromPayload(e.detail);
                }
            });
        },

        async toggle() {
            this.open = !this.open;
            if (this.open) {
                await this.refresh();
            }
        },

        async refresh() {
            this.loading = true;
            try {
                const res = await fetch('/cart/summary', {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) {
                    const json = await res.json();
                    const data = json.data ?? json;
                    this.items = data.items;
                    this.count = data.count;
                    this.total = data.total;
                    Alpine.store('nav').setFromPayload(data);
                }
            } finally {
                this.loading = false;
            }
        },

        async removeItem(slug) {
            if (this.removing) {
                return;
            }
            this.removing = slug;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch(`/cart/remove/${slug}`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const json = await res.json();
                if (!res.ok) {
                    throw new Error(json.message || 'Remove failed');
                }
                const data = json.data;
                this.items = data.items;
                this.count = data.count;
                this.total = data.total;
                Alpine.store('nav').setFromPayload(data);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                Alpine.store('toast').show(json.message, 'success');
            } catch (e) {
                Alpine.store('toast').show(e.message || 'Could not remove item', 'error');
            } finally {
                this.removing = null;
            }
        },
    }));
});

Alpine.start();
