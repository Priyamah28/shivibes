@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Product', 'subtitle' => 'Create a new product for the storefront.'])

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
    </form>
@endsection
