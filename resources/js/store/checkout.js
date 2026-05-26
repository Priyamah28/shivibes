/**
 * AJAX checkout + Razorpay modal (no full page reload).
 */
export function registerCheckout(Alpine) {
    Alpine.data('checkoutPayment', (config) => ({
        mode: config.defaultMode ?? 'saved',
        paymentMethod: 'razorpay',
        processing: false,
        payLabel: 'Pay securely with Razorpay',
        subtotal: config.subtotal ?? 0,
        shipping: 0,

        get total() {
            return this.subtotal + this.shipping;
        },

        updateShipping(select) {
            this.shipping = select?.value === 'express' ? 60 : 0;
        },

        init() {
            this.$watch('paymentMethod', (value) => {
                this.payLabel =
                    value === 'cod'
                        ? 'Place order (Cash on delivery)'
                        : 'Pay securely with Razorpay';
            });
        },

        async submit(event) {
            event.preventDefault();

            if (this.processing) {
                return;
            }

            if (!config.razorpayConfigured && this.paymentMethod === 'razorpay') {
                Alpine.store('toast').show(
                    'Online payment is temporarily unavailable. Please try again later.',
                    'error'
                );
                return;
            }

            this.processing = true;

            try {
                const form = event.target;
                const body = new FormData(form);
                body.set('payment_method', this.paymentMethod);

                const res = await fetch(config.storeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body,
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (data.errors) {
                        const first = Object.values(data.errors).flat()[0];
                        throw new Error(first || data.message || 'Please check your details.');
                    }
                    throw new Error(data.message || 'Could not start checkout.');
                }

                if (this.paymentMethod === 'cod') {
                    Alpine.store('toast').show(data.message || 'Order placed!', 'success');
                    window.location.href = data.redirect;
                    return;
                }

                await this.openRazorpay(data, config);
            } catch (e) {
                Alpine.store('toast').show(e.message || 'Checkout failed. Please try again.', 'error');
            } finally {
                this.processing = false;
            }
        },

        openRazorpay(data, config) {
            return new Promise((resolve, reject) => {
                if (typeof window.Razorpay === 'undefined') {
                    reject(new Error('Payment gateway failed to load. Refresh and try again.'));
                    return;
                }

                const options = {
                    key: data.razorpay_key,
                    amount: data.amount,
                    currency: data.currency || 'INR',
                    name: config.businessName || 'Shivibes',
                    description: `Order ${data.order_number}`,
                    order_id: data.razorpay_order_id,
                    prefill: data.prefill || {},
                    notes: data.notes || {},
                    theme: { color: '#3a735c' },
                    handler: async (response) => {
                        this.processing = true;
                        try {
                            await this.verifyPayment(data.order_id, response, config);
                            resolve();
                        } catch (err) {
                            reject(err);
                        } finally {
                            this.processing = false;
                        }
                    },
                    modal: {
                        ondismiss: () => {
                            Alpine.store('toast').show('Payment cancelled.', 'info');
                            reject(new Error('Payment cancelled'));
                        },
                    },
                };

                const rzp = new window.Razorpay(options);
                rzp.on('payment.failed', (response) => {
                    const msg =
                        response.error?.description ||
                        response.error?.reason ||
                        'Payment failed. Please try again.';
                    Alpine.store('toast').show(msg, 'error');
                    reject(new Error(msg));
                });
                rzp.open();
            });
        },

        async verifyPayment(orderId, response, config) {
            const res = await fetch(config.verifyUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({
                    order_id: orderId,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature,
                }),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Payment verification failed.');
            }

            Alpine.store('toast').show(data.message || 'Payment successful!', 'success');
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: 0, items: [], total: 0 } }));
            window.location.href = data.redirect;
        },
    }));
}
