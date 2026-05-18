@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Edit Product',
        'subtitle' => $product->name,
    ])

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>
@endsection
