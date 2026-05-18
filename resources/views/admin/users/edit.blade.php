@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', ['title' => 'Edit User', 'subtitle' => $user->email])

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user])
    </form>
@endsection
