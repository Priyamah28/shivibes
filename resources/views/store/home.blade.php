@extends('layouts.app')

@section('fullWidth', '1')

@section('content')
    @include('store.home.sections.hero')
    @include('store.home.sections.trust-bar')
    @include('store.home.sections.categories')
    @include('store.home.sections.product-row', ['title' => 'New Arrivals', 'subtitle' => 'Fresh herbal essentials just added to our collection', 'products' => $newArrivals, 'bg' => 'bg-brand-50/70'])
    @include('store.home.sections.product-row', ['title' => 'Trending Now', 'subtitle' => 'Most loved by our community this season', 'products' => $trending, 'bg' => 'bg-white'])
    @include('store.home.sections.product-row', ['title' => 'Bestsellers', 'subtitle' => 'Tried, tested and adored across India', 'products' => $bestSellers, 'bg' => 'bg-brand-50/70'])
    @include('store.home.sections.gifting-hub')
    @include('store.home.sections.shop-by')
    @include('store.home.sections.about')
    @include('store.home.sections.why-choose')
    @include('store.home.sections.testimonials')
    @include('store.home.sections.faq')
    @include('store.home.sections.instagram')
@endsection
