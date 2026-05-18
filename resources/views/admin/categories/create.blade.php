@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Category'])

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.categories._form')
    </form>
@endsection
