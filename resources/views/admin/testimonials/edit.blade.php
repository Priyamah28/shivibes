@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit Testimonial'])
    <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST">@csrf @method('PUT') @include('admin.testimonials._form')</form>
@endsection
