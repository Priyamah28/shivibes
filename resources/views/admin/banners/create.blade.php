@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Banner'])

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.banners._form')
    </form>
@endsection
