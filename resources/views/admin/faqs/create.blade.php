@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add FAQ'])
    <form action="{{ route('admin.faqs.store') }}" method="POST">@csrf @include('admin.faqs._form')</form>
@endsection
