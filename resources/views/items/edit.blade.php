@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
    <h1 class="h3 mb-4">Edit Item</h1>

    <form action="{{ route('items.update', $item) }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        @method('PUT')
        @include('items._form')
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('items.index') }}" class="btn btn-link">Cancel</a>
    </form>
@endsection
