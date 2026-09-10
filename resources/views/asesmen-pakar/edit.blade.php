<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Asesmen Pakar</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $asesmen->prasarana?->nama_fasilitas }}</p>
            </div>
            <a href="{{ route('asesmen-pakar.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium shrink-0">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('asesmen-pakar.update', $asesmen) }}" class="space-y-5">
                @csrf @method('PUT')
                @include('asesmen-pakar._form', ['asesmen' => $asesmen])
            </form>
        </div>
    </div>
</x-app-layout>
