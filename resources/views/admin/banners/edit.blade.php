@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Banner', 'subtitle' => $banner->title])

    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.banners._form')
    </form>
@endsection
