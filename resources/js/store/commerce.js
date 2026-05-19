import { api, postJson } from '../lib/http';

/**
 * Cart & wishlist actions — updates Alpine stores and shows toasts.
 */
export function registerCommerce(Alpine) {
    Alpine.store('nav', {
        cartCount: window.__SHIVIBES?.cartCount ?? 0,
        wishlistCount: window.__SHIVIBES?.wishlistCount ?? 0,
        wishlistSlugs: window.__SHIVIBES?.wishlistSlugs ?? [],

        setCart(count) {
            this.cartCount = count;
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
                Alpine.store('nav').setCart(res.data.count);
                Alpine.store('toast').show(res.message, 'success');
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: res.data }));
                window.dispatchEvent(new CustomEvent('toggle-cart'));
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
                this.items = res.data?.items ?? res.items ?? [];
                this.total = res.data?.total ?? res.total ?? 0;
                this.count = res.data?.count ?? res.count ?? 0;
                Alpine.store('nav').setCart(this.count);
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
                Alpine.store('nav').setCart(this.count);
                Alpine.store('toast').show(res.message, 'success');
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: res.data }));
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
    Alpine.store('nav').setCart(data.count);
    return data;
}
