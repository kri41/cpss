<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check-in Berhasil — {{ $fasil->nama_fasilitas }}</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(160deg, #eff6ff 0%, #f0f9ff 45%, #ffffff 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;
        }
        @keyframes pop { 0% { transform: scale(0.5); opacity: 0; } 70% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .animate-pop { animation: pop 0.5s ease forwards; }
    </style>
</head>
<body>
<div class="max-w-sm w-full mx-auto text-center">

    <div class="bg-white rounded-3xl shadow-xl border border-blue-50 p-8">

        <div class="animate-pop mb-5 inline-flex items-center justify-center w-20 h-20 rounded-full" style="background: linear-gradient(145deg, #1e3a8a 0%, #2563eb 60%, #0ea5e9 100%);">
            <i class="fas fa-check text-white text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-1">Check-in Berhasil!</h1>
        <p class="text-sm text-gray-500 mb-5">Data aktivitas olahraga Anda berhasil dicatat di <strong class="text-gray-700">{{ $fasil->nama_fasilitas }}</strong>, Kampung Olahraga {{ $kampung->nama_kampung }}.</p>

        <div class="bg-blue-50 rounded-2xl p-4 mb-6 text-left text-sm text-blue-900 flex gap-3">
            <i class="fas fa-medal text-blue-500 text-lg mt-0.5"></i>
            <div>
                <p class="font-semibold mb-1">Terima kasih sudah berolahraga!</p>
                <p class="text-blue-600 text-xs">Aktivitas Anda berkontribusi pada program Kampung Olahraga Kemenpora RI.</p>
            </div>
        </div>

        <a href="{{ route('kampung.checkin.form', $fasil->qr_token) }}"
           class="block w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition text-sm mb-3 shadow-lg hover:shadow-blue-500/30">
            Check-in Lagi
        </a>
        <a href="{{ url('/') }}" class="block text-xs text-gray-400 hover:text-gray-600">Ke Beranda Dataraga</a>
    </div>

    <p class="text-xs text-gray-400 mt-4">Dataraga &mdash; Sistem Informasi Olahraga Daerah</p>
</div>
</body>
</html>
