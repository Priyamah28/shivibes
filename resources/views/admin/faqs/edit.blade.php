@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit FAQ'])
    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">@csrf @method('PUT') @include('admin.faqs._form')</form>
@endsection
