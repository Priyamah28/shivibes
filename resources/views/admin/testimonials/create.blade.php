@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add Testimonial'])
    <form action="{{ route('admin.testimonials.store') }}" method="POST">@csrf @include('admin.testimonials._form')</form>
@endsection
