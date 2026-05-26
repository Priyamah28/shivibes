/**
 * Admin image upload — client-side dimension check before submit.
 */
export function registerAdminImageUpload(Alpine) {
    Alpine.data('adminImageUpload', (config) => ({
        spec: config.spec,
        bannerSpecs: config.bannerSpecs ?? null,
        placementField: config.placementField,
        previewUrl: null,
        previewDimensions: '',
        clientMessage: '',
        clientOk: true,

        init() {
            if (this.bannerSpecs && this.placementField) {
                const select = document.querySelector(`[name="${this.placementField}"]`);
                if (select) {
                    select.addEventListener('change', () => this.updateBannerSpec(select.value));
                    this.updateBannerSpec(select.value);
                }
            }
        },

        updateBannerSpec(placement) {
            const key = placement === 'promo_strip' ? 'promo_strip' : 'home_hero';
            if (this.bannerSpecs?.[key]) {
                this.spec = this.bannerSpecs[key];
                this.clientMessage = '';
                this.previewUrl = null;
            }
        },

        async onFileSelect(event) {
            const file = event.target.files?.[0];
            this.clientMessage = '';
            this.clientOk = true;
            this.previewUrl = null;
            this.previewDimensions = '';

            if (!file) {
                return;
            }

            const maxBytes = (this.spec.max_kb || 2048) * 1024;
            if (file.size > maxBytes) {
                this.clientOk = false;
                this.clientMessage = `File is too large (${(file.size / 1024 / 1024).toFixed(2)} MB). Max ${this.spec.max_mb} MB.`;
                event.target.value = '';
                return;
            }

            const result = await this.checkDimensions(file);

            if (!result.ok) {
                this.clientOk = false;
                this.clientMessage = result.message;
                event.target.value = '';
                return;
            }

            this.clientOk = true;
            this.clientMessage = result.message;
            this.previewUrl = URL.createObjectURL(file);
            this.previewDimensions = `${result.width} × ${result.height} px · ratio ${result.ratioLabel}`;
        },

        checkDimensions(file) {
            return new Promise((resolve) => {
                const img = new Image();
                const url = URL.createObjectURL(file);

                img.onload = () => {
                    URL.revokeObjectURL(url);
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const ratio = width / height;
                    const expected = this.spec.ratio ?? this.spec.recommended.width / this.spec.recommended.height;
                    const tolerance = 0.06;

                    const minW = this.spec.min.width;
                    const minH = this.spec.min.height;
                    const maxW = this.spec.max?.width ?? 99999;
                    const maxH = this.spec.max?.height ?? 99999;

                    if (width < minW || height < minH) {
                        resolve({
                            ok: false,
                            message: `Too small (${width}×${height}). Minimum ${minW}×${minH} px.`,
                        });
                        return;
                    }

                    if (width > maxW || height > maxH) {
                        resolve({
                            ok: false,
                            message: `Too large (${width}×${height}). Maximum ${maxW}×${maxH} px.`,
                        });
                        return;
                    }

                    if (Math.abs(ratio - expected) > expected * tolerance) {
                        resolve({
                            ok: false,
                            message: `Wrong aspect ratio for ${this.spec.ratio_label}. Use ${this.spec.recommended.width}×${this.spec.recommended.height} px.`,
                        });
                        return;
                    }

                    resolve({
                        ok: true,
                        width,
                        height,
                        ratioLabel: (width / height).toFixed(2) + ':1',
                        message: `Looks good for ${this.spec.label}. Server will verify on save.`,
                    });
                };

                img.onerror = () => {
                    URL.revokeObjectURL(url);
                    resolve({ ok: false, message: 'Could not read image file.' });
                };

                img.src = url;
            });
        },
    }));
}
