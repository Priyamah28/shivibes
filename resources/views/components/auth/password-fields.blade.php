@props([
    'passwordLabel' => 'Password',
    'confirmLabel' => 'Confirm password',
])

{{-- Requires parent <form x-data="passwordForm" @submit="handleSubmit" novalidate> --}}

<div
    x-show="showSummaryError()"
    x-cloak
    x-ref="summaryError"
    class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
    role="alert"
>
    <p class="font-semibold">Please fix your password before continuing</p>
    <ul class="mt-2 list-inside list-disc space-y-0.5 text-rose-700">
        <template x-if="!allRulesPass">
            <li>Password must meet all requirements below</li>
        </template>
        <template x-if="allRulesPass && !passwordsMatch">
            <li>Passwords do not match</li>
        </template>
    </ul>
</div>

<div>
    <x-input-label for="password" :value="$passwordLabel" />
    <div class="relative mt-1">
        <input
            id="password"
            name="password"
            x-model="password"
            @blur="touched.password = true"
            @input="touched.password = true"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="new-password"
            placeholder="Create a strong password"
            class="input-field block w-full pe-12"
            :class="showPasswordErrors() ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30' : ''"
        >
        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 end-0 flex items-center px-3 text-sm font-medium text-slate-500 hover:text-brand-700"
            tabindex="-1"
        >
            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
        </button>
    </div>

    <div class="mt-3 rounded-2xl border border-brand-100/80 bg-gradient-to-br from-brand-50/80 to-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-2 text-xs">
            <span class="font-semibold text-slate-700">Password strength</span>
            <span class="font-medium" :class="strengthPercent === 100 ? 'text-emerald-700' : 'text-slate-600'" x-text="strengthLabel"></span>
        </div>
        <div class="h-1.5 overflow-hidden rounded-full bg-brand-100">
            <div
                class="h-full rounded-full transition-all duration-300"
                :class="strengthBarClass"
                :style="`width: ${strengthPercent}%`"
            ></div>
        </div>
        <ul class="mt-4 space-y-2">
            <template x-for="check in checks" :key="check.key">
                <li class="flex items-start gap-2 text-sm transition-colors" :class="check.pass ? 'text-emerald-700' : 'text-slate-600'">
                    <span
                        class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                        :class="check.pass ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500'"
                        x-text="check.pass ? '✓' : '○'"
                    ></span>
                    <span x-text="check.label"></span>
                </li>
            </template>
        </ul>
    </div>

    <p x-show="showPasswordErrors()" x-cloak class="mt-2 text-sm text-rose-600" role="alert">
        Password does not meet all requirements.
    </p>
    @if ($errors->has('password'))
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    @endif
</div>

<div>
    <x-input-label for="password_confirmation" :value="$confirmLabel" />
    <div class="relative mt-1">
        <input
            id="password_confirmation"
            name="password_confirmation"
            x-model="confirmation"
            @blur="touched.confirmation = true"
            @input="touched.confirmation = true"
            :type="showConfirmation ? 'text' : 'password'"
            autocomplete="new-password"
            placeholder="Repeat your password"
            class="input-field block w-full pe-12"
            :class="showMismatch() ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30' : (passwordsMatch && confirmation.length > 0 ? 'border-emerald-400 focus:border-emerald-500 focus:ring-emerald-500/30' : '')"
        >
        <button
            type="button"
            @click="showConfirmation = !showConfirmation"
            class="absolute inset-y-0 end-0 flex items-center px-3 text-sm font-medium text-slate-500 hover:text-brand-700"
            tabindex="-1"
        >
            <span x-text="showConfirmation ? 'Hide' : 'Show'"></span>
        </button>
    </div>

    <p x-show="passwordsMatch && confirmation.length > 0" x-cloak class="mt-2 flex items-center gap-1.5 text-sm text-emerald-700">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold">✓</span>
        Passwords match
    </p>
    <p x-show="showMismatch()" x-cloak class="mt-2 text-sm text-rose-600" role="alert">
        Passwords do not match. Please type the same password in both fields.
    </p>
    @if ($errors->has('password_confirmation'))
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    @endif
</div>
