@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="font-serif text-3xl font-bold text-brand-900">Add Address</h1>
        <a href="{{ route('account.addresses.index') }}" class="mt-2 inline-block text-sm text-brand-700 hover:underline">← Back to addresses</a>
    </div>

    <form action="{{ route('account.addresses.store') }}" method="POST" class="mt-6 max-w-2xl rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
        @csrf
        <x-account.address-form />
        <button type="submit" class="btn-primary mt-6">Save Address</button>
    </form>
@endsection
