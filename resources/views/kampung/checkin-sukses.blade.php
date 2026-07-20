<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check-in Berhasil — {{ $kampung->nama_kampung }}</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0fdf4; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        @keyframes pop { 0% { transform: scale(0.5); opacity: 0; } 70% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .animate-pop { animation: pop 0.5s ease forwards; }
    </style>
</head>
<body>
<div class="max-w-sm w-full mx-auto text-center">

    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">

        <div class="animate-pop mb-5 inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-1">Check-in Berhasil!</h1>
        <p class="text-sm text-gray-500 mb-5">Data aktivitas olahraga Anda berhasil dicatat di <strong class="text-gray-700">{{ $kampung->nama_kampung }}</strong>.</p>

        <div class="bg-green-50 rounded-2xl p-4 mb-6 text-left text-sm text-green-800">
            <p class="font-semibold mb-1">Terima kasih sudah berolahraga!</p>
            <p class="text-green-600 text-xs">Aktivitas Anda berkontribusi pada program Kampung Olahraga Kemenpora RI.</p>
        </div>

        <a href="{{ route('kampung.checkin.form', $kampung->qr_token) }}"
           class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl transition text-sm mb-3">
            Check-in Lagi
        </a>
        <a href="{{ url('/') }}" class="block text-xs text-gray-400 hover:text-gray-600">Ke Beranda Dataraga</a>
    </div>

    <p class="text-xs text-gray-400 mt-4">Dataraga &mdash; Sistem Informasi Olahraga Daerah</p>
</div>
</body>
</html>
