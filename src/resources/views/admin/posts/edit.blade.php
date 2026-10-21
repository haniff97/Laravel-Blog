@extends('layouts.app')
@section('title', 'Edit Post')
@section('content')
    @include('admin.posts.form', ['post' => $post])
@endsection
