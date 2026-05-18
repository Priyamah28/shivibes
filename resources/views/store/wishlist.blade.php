@extends('layouts.app')

@section('content')
    <x-store.breadcrumb :items="[['label' => 'Wishlist']]" />

    <h1 class="font-serif text-3xl font-bold">My Wishlist</h1>

    @if ($items->isEmpty())
        <div class="mt-12 rounded-2xl border border-dashed border-brand-200 bg-white p-16 text-center">
            <p class="text-lg font-semibold">Your wishlist is empty</p>
            <p class="mt-2 text-sm text-slate-500">Save products you love and shop them later.</p>
            <a href="{{ route('products.index') }}" class="btn-primary mt-6 inline-flex">Browse Products</a>
        </div>
    @else
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($items as $item)
                <x-store.product-card :product="$item->product" />
            @endforeach
        </div>
    @endif
@endsection
