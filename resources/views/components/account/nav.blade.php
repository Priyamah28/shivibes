@props(['active' => ''])

<nav class="flex flex-wrap gap-2 border-b border-brand-100 pb-4 text-sm font-medium">
    <a href="{{ route('account.orders.index') }}"
       class="rounded-full px-4 py-2 {{ $active === 'orders' ? 'bg-brand-800 text-white' : 'bg-brand-50 text-brand-800 hover:bg-brand-100' }}">
        My Orders
    </a>
    <a href="{{ route('account.addresses.index') }}"
       class="rounded-full px-4 py-2 {{ $active === 'addresses' ? 'bg-brand-800 text-white' : 'bg-brand-50 text-brand-800 hover:bg-brand-100' }}">
        Addresses
    </a>
    <a href="{{ route('profile.edit') }}"
       class="rounded-full px-4 py-2 {{ $active === 'profile' ? 'bg-brand-800 text-white' : 'bg-brand-50 text-brand-800 hover:bg-brand-100' }}">
        Profile
    </a>
</nav>
