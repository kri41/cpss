<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">QR Code Partisipasi</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Scan untuk Mendaftar Kehadiran</h3>
                <p class="text-sm text-gray-500 mb-6">Partisipan dapat memindai QR code ini untuk mengisi identitas dan mencatat kehadiran secara mandiri.</p>
                <div class="flex justify-center mb-6">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($qrUrl) }}" alt="QR Code" class="rounded-xl shadow-md border border-gray-100">
                </div>
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">URL Pendaftaran</p>
                    <p class="text-sm text-blue-600 font-medium break-all">{{ $qrUrl }}</p>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('partisipasi.show', $partisipasi) }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Kembali ke Detail</a>
                    <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Cetak QR</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>