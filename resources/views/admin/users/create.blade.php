@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Add User'])

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        @include('admin.users._form')
    </form>
@endsection
