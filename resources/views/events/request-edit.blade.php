<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan Akses Edit</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $event->nama_event }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-amber-800">Data ini sudah tervalidasi / bukan milik Anda, jadi tidak bisa diedit langsung. Jelaskan apa yang perlu diperbaiki — kalau admin menyetujui, <strong>pemilik asli data</strong> akan mendapat kembali akses edit untuk memperbaikinya sendiri.</p>
                </div>

                @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('events.request-edit', $event) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="alasan" :value="__('Alasan / Apa yang perlu diperbaiki')" />
                        <textarea id="alasan" name="alasan" rows="4" required minlength="10"
                            placeholder="Contoh: tanggal event ternyata mundur, mohon diperbarui..."
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alasan') }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('events.show', $event) }}" class="underline text-sm text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        <x-primary-button>{{ __('Kirim Permintaan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
