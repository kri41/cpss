<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Ajukan Akses Edit</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $club->nama_club }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-amber-800">Data ini sudah tervalidasi / bukan milik Anda, jadi tidak bisa diedit langsung. Jelaskan apa yang perlu diperbaiki — kalau admin menyetujui, <strong>pemilik asli data</strong> akan mendapat kembali akses edit untuk memperbaikinya sendiri.</p>
                </div>

                @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('clubs.request-edit', $club) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan / Apa yang perlu diperbaiki <span class="text-red-500">*</span></label>
                        <textarea name="alasan" rows="4" required minlength="10"
                            placeholder="Contoh: nomor telepon narahubung sudah berganti, mohon diperbarui..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('alasan') }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('clubs.show', $club) }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Kirim Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
