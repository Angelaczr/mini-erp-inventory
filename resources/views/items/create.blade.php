@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
    <h1 class="h3 mb-4">Add Item</h1>

    <form action="{{ route('items.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        @include('items._form')
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('items.index') }}" class="btn btn-link">Cancel</a>
    </form>
@endsection
