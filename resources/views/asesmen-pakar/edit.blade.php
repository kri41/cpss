@extends('layouts.app')

@section('title', 'Edit Asesmen Pakar - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('asesmen-pakar.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Asesmen &mdash; {{ $asesmen->prasarana?->nama_fasilitas }}</h1>

        <form method="POST" action="{{ route('asesmen-pakar.update', $asesmen) }}" class="space-y-5">
            @csrf @method('PUT')
            @include('asesmen-pakar._form', ['asesmen' => $asesmen])
        </form>
    </div>
</div>
@endsection
