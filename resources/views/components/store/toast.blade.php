<div
    x-data
    x-show="$store.toast.visible"
    x-transition
    class="fixed bottom-6 right-6 z-50 max-w-sm rounded-xl border px-5 py-4 shadow-lg"
    :class="{
        'border-green-200 bg-green-50 text-green-800': $store.toast.type === 'success',
        'border-rose-200 bg-rose-50 text-rose-800': $store.toast.type === 'error',
        'border-brand-200 bg-brand-50 text-brand-900': $store.toast.type === 'info',
    }"
    style="display: none;"
>
    <p class="text-sm font-medium" x-text="$store.toast.message"></p>
</div>

@if (session('success'))
    <div x-data x-init="$store.toast.show(@js(session('success')), 'success')"></div>
@endif
@if (session('error'))
    <div x-data x-init="$store.toast.show(@js(session('error')), 'error')"></div>
@endif
