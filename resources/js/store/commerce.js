import { api, postJson } from '../lib/http';

function syncNavFromCartPayload(data) {
    Alpine.store('nav').setFromPayload(data);
    window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
}

/**
 * Cart & wishlist actions — updates Alpine stores and shows toasts.
 */
export function registerCommerce(Alpine) {
    Alpine.store('nav', {
        cartCount: window.__SHIVIBES?.cartCount ?? 0,
        cartQuantities: { ...(window.__SHIVIBES?.cartQuantities ?? {}) },
        wishlistCount: window.__SHIVIBES?.wishlistCount ?? 0,
        wishlistSlugs: window.__SHIVIBES?.wishlistSlugs ?? [],

        setCart(count) {
            this.cartCount = count;
        },

        cartQty(slug) {
            return this.cartQuantities[slug] ?? 0;
        },

        setFromPayload(data) {
            if (!data) {
                return;
            }

            this.setCart(data.count ?? 0);

            const quantities = {};
            for (const item of data.items ?? []) {
                quantities[item.slug] = item.quantity;
            }
            this.cartQuantities = quantities;
        },

        setWishlist(count, slugs = null) {
            this.wishlistCount = count;
            if (slugs !== null) {
                this.wishlistSlugs = slugs;
            }
        },

        isInWishlist(slug) {
            return this.wishlistSlugs.includes(slug);
        },

        toggleWishlistSlug(slug, inWishlist) {
            if (inWishlist && !this.wishlistSlugs.includes(slug)) {
                this.wishlistSlugs = [...this.wishlistSlugs, slug];
            }
            if (!inWishlist) {
                this.wishlistSlugs = this.wishlistSlugs.filter((s) => s !== slug);
            }
        },
    });

    Alpine.data('productActions', (slug, initiallyInWishlist = false) => ({
        slug,
        inWishlist: initiallyInWishlist,
        cartLoading: false,
        wishlistLoading: false,

        init() {
            if (Alpine.store('nav').isInWishlist(this.slug)) {
                this.inWishlist = true;
            }
        },

        async addToCart() {
            if (this.cartLoading) {
                return;
            }

            this.cartLoading = true;

            try {
                const res = await postJson(`/cart/add/${this.slug}`);
                syncNavFromCartPayload(res.data);
                Alpine.store('toast').show(res.message, 'success');
            } catch (e) {
                Alpine.store('toast').show(
                    e.payload?.message || e.message || 'Could not add to cart',
                    'error',
                );
                if (e.status === 401 || e.status === 419) {
                    window.location.href = '/login';
                }
            } finally {
                this.cartLoading = false;
            }
        },

        async decrementCart() {
            if (this.cartLoading || Alpine.store('nav').cartQty(this.slug) < 1) {
                return;
            }

            this.cartLoading = true;

            try {
                const res = await postJson(`/cart/decrement/${this.slug}`);
                syncNavFromCartPayload(res.data);
                Alpine.store('toast').show(res.message, 'success');
            } catch (e) {
                Alpine.store('toast').show(
                    e.payload?.message || e.message || 'Could not update cart',
                    'error',
                );
                if (e.status === 401 || e.status === 419) {
                    window.location.href = '/login';
                }
            } finally {
                this.cartLoading = false;
            }
        },

        async toggleWishlist() {
            if (this.wishlistLoading) {
                return;
            }

            this.wishlistLoading = true;
            const wasIn = this.inWishlist;
            this.inWishlist = !wasIn;
            Alpine.store('nav').toggleWishlistSlug(this.slug, this.inWishlist);

            try {
                const res = await postJson(`/wishlist/toggle/${this.slug}`);
                this.inWishlist = res.data.in_wishlist;
                Alpine.store('nav').setWishlist(res.data.count, res.data.slugs);
                Alpine.store('toast').show(res.message, 'success');
            } catch (e) {
                this.inWishlist = wasIn;
                Alpine.store('nav').toggleWishlistSlug(this.slug, wasIn);
                Alpine.store('toast').show(
                    e.payload?.message || e.message || 'Wishlist update failed',
                    'error',
                );
                if (e.status === 401 || e.status === 419) {
                    window.location.href = '/login';
                }
            } finally {
                this.wishlistLoading = false;
            }
        },
    }));

    Alpine.data('cartPage', () => ({
        items: [],
        total: 0,
        count: 0,
        loading: true,
        removing: null,

        async init() {
            await this.refresh();
        },

        async refresh() {
            this.loading = true;
            try {
                const res = await api('/cart/summary');
                const data = res.data ?? res;
                this.items = data.items ?? [];
                this.total = data.total ?? 0;
                this.count = data.count ?? 0;
                Alpine.store('nav').setFromPayload(data);
            } finally {
                this.loading = false;
            }
        },

        async removeItem(slug) {
            if (this.removing) {
                return;
            }

            this.removing = slug;

            try {
                const res = await postJson(`/cart/remove/${slug}`);
                this.items = res.data.items;
                this.total = res.data.total;
                this.count = res.data.count;
                syncNavFromCartPayload(res.data);
                Alpine.store('toast').show(res.message, 'success');
            } catch (e) {
                Alpine.store('toast').show(
                    e.payload?.message || 'Could not remove item',
                    'error',
                );
            } finally {
                this.removing = null;
            }
        },
    }));
}

export async function refreshCartSummary() {
    const res = await api('/cart/summary');
    const data = res.data ?? res;
    Alpine.store('nav').setFromPayload(data);
    return data;
}
