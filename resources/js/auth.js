/**
 * Auth pages: OTP boxes, password visibility, password strength validation.
 */

const PASSWORD_RULES = [
    { key: 'minLength', label: 'At least 8 characters', test: (p) => p.length >= 8 },
    { key: 'uppercase', label: 'One uppercase letter (A–Z)', test: (p) => /[A-Z]/.test(p) },
    { key: 'lowercase', label: 'One lowercase letter (a–z)', test: (p) => /[a-z]/.test(p) },
    { key: 'number', label: 'One number (0–9)', test: (p) => /\d/.test(p) },
    { key: 'special', label: 'One special character (!@#$…)', test: (p) => /[^A-Za-z0-9]/.test(p) },
];

export function registerAuth(Alpine) {
    Alpine.data('authForm', () => ({
        loading: false,
        submit() {
            this.loading = true;
        },
    }));

    Alpine.data('passwordField', () => ({
        visible: false,
        toggle() {
            this.visible = !this.visible;
        },
    }));

    Alpine.data('passwordForm', () => ({
        loading: false,
        submitted: false,
        password: '',
        confirmation: '',
        showPassword: false,
        showConfirmation: false,
        touched: {
            password: false,
            confirmation: false,
        },

        get checks() {
            return PASSWORD_RULES.map((rule) => ({
                ...rule,
                pass: rule.test(this.password),
            }));
        },

        get allRulesPass() {
            return this.checks.every((check) => check.pass);
        },

        get passwordsMatch() {
            return this.password.length > 0 && this.password === this.confirmation;
        },

        get canSubmit() {
            return this.allRulesPass && this.passwordsMatch;
        },

        get strengthPercent() {
            const passed = this.checks.filter((c) => c.pass).length;
            return Math.round((passed / this.checks.length) * 100);
        },

        get strengthLabel() {
            if (!this.password) {
                return 'Enter a password';
            }
            if (this.strengthPercent < 40) {
                return 'Weak';
            }
            if (this.strengthPercent < 80) {
                return 'Fair';
            }
            if (this.strengthPercent < 100) {
                return 'Good';
            }
            return 'Strong';
        },

        get strengthBarClass() {
            if (this.strengthPercent < 40) {
                return 'bg-rose-500';
            }
            if (this.strengthPercent < 80) {
                return 'bg-amber-500';
            }
            return 'bg-emerald-500';
        },

        showPasswordErrors() {
            return (this.submitted || this.touched.password) && this.password.length > 0 && !this.allRulesPass;
        },

        showMismatch() {
            return (this.submitted || this.touched.confirmation) && this.confirmation.length > 0 && !this.passwordsMatch;
        },

        showSummaryError() {
            return this.submitted && !this.canSubmit;
        },

        handleSubmit(event) {
            this.submitted = true;
            this.touched.password = true;
            this.touched.confirmation = true;

            if (!this.canSubmit) {
                event.preventDefault();
                this.$nextTick(() => {
                    this.$refs.summaryError?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
                return;
            }

            this.loading = true;
        },
    }));

    Alpine.data('otpForm', (length = 6) => ({
        length,
        digits: [],
        loading: false,

        init() {
            this.digits = Array.from({ length: this.length }, () => '');
            this.$nextTick(() => this.$refs.digit0?.focus());
        },

        get code() {
            return this.digits.join('');
        },

        setHiddenInput() {
            const input = this.$refs.otpHidden;
            if (input) {
                input.value = this.code;
            }
        },

        onInput(index, event) {
            const value = event.target.value.replace(/\D/g, '');
            this.digits[index] = value.slice(-1);
            event.target.value = this.digits[index];
            this.setHiddenInput();

            if (this.digits[index] && index < this.length - 1) {
                this.$refs[`digit${index + 1}`]?.focus();
            }

            if (this.code.length === this.length) {
                this.$refs.verifyForm?.requestSubmit();
            }
        },

        onKeydown(index, event) {
            if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                this.$refs[`digit${index - 1}`]?.focus();
            }
        },

        onPaste(event) {
            event.preventDefault();
            const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, this.length);
            pasted.split('').forEach((char, i) => {
                this.digits[i] = char;
                const el = this.$refs[`digit${i}`];
                if (el) {
                    el.value = char;
                }
            });
            this.setHiddenInput();
            const next = Math.min(pasted.length, this.length - 1);
            this.$refs[`digit${next}`]?.focus();
        },

        submit() {
            this.loading = true;
            this.setHiddenInput();
        },
    }));
}
