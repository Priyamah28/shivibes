@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="font-serif text-3xl font-bold text-brand-900">My Account</h1>
        <p class="mt-1 text-sm text-slate-600">Manage delivery addresses for faster checkout.</p>
    </div>

    <x-account.nav active="addresses" />

    <div class="mt-6 flex justify-end">
        <a href="{{ route('account.addresses.create') }}" class="btn-primary">+ Add Address</a>
    </div>

    @if ($addresses->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-brand-200 bg-white p-12 text-center">
            <p class="text-slate-600">No saved addresses yet.</p>
            <a href="{{ route('account.addresses.create') }}" class="btn-primary mt-4 inline-flex">Add your first address</a>
        </div>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ($addresses as $address)
                <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm {{ $address->is_default ? 'ring-2 ring-gold-300' : '' }}">
                    <div>
                        <p class="font-semibold text-brand-900">{{ $address->full_name }}</p>
                        <span class="badge mt-1 bg-brand-100 text-brand-800">{{ $address->typeLabel() }}</span>
                        @if ($address->is_default)
                            <span class="badge mt-1 bg-gold-100 text-gold-900">Default</span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-slate-600 whitespace-pre-line">{{ $address->formattedLines() }}</p>
                    <p class="mt-2 text-sm text-slate-500">Phone: {{ $address->phone }}@if($address->phone_alt) · Alt: {{ $address->phone_alt }}@endif</p>
                    <div class="mt-4 flex flex-wrap gap-3 text-sm">
                        <a href="{{ route('account.addresses.edit', $address) }}" class="text-brand-700 hover:underline">Edit</a>
                        @unless ($address->is_default)
                            <form action="{{ route('account.addresses.default', $address) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-brand-700 hover:underline">Make default</button>
                            </form>
                        @endunless
                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Delete this address?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
