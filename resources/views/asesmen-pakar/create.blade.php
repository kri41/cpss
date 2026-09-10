@extends('layouts.app')

@section('title', 'Input Lembar Asesor - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('asesmen-pakar.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Transkripsi Lembar Audit Asesor</h1>
        <p class="text-sm text-gray-500 mb-6">Salin hasil dari lembar kertas asesor ke sistem.</p>

        <form method="POST" action="{{ route('asesmen-pakar.store') }}" class="space-y-5">
            @csrf
            @include('asesmen-pakar._form', ['asesmen' => null])
        </form>
    </div>
</div>
@endsection
