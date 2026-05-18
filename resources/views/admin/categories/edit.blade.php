@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Category', 'subtitle' => $category->name])

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
@endsection
