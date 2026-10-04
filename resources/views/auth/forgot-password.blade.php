<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi — Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .char-panel {
            background: linear-gradient(160deg, #312e81 0%, #4338ca 40%, #6d28d9 80%, #7c3aed 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .float { animation: float 3.5s ease-in-out infinite; }
    </style>
</head>
<body class="antialiased font-sans min-h-screen flex bg-gray-100">

    <!-- Left: Character Panel -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 char-panel relative overflow-hidden flex-col items-center justify-between py-12 px-8">
        <!-- Decorative circles -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
        <div class="absolute top-1/2 right-0 w-32 h-32 bg-violet-400/10 rounded-full translate-x-1/2 pointer-events-none"></div>

        <!-- Branding Top -->
        <div class="relative z-10 flex items-center gap-3">
            <img src="/storage/logo.png" alt="Dataraga" class="h-10 w-10 object-contain brightness-0 invert">
            <div>
                <p class="text-white font-black text-xl tracking-tight">Dataraga</p>
                <p class="text-violet-200 text-[10px] font-medium tracking-widest uppercase">Kamu Gerak, Indonesia Tahu</p>
            </div>
        </div>

        <!-- Character -->
        <div class="relative z-10 flex flex-col items-center gap-4">
            <!-- Speech bubble -->
            <div class="bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-white text-center max-w-[220px] relative">
                <p class="font-bold text-base leading-snug">"Lupa sandi itu wajar,<br>tenang saja, kita bantu<br>atur ulang!"</p>
                <p class="text-violet-200 text-xs mt-1">— Tim Dataraga</p>
                <!-- tail -->
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[8px] border-r-[8px] border-t-[12px] border-l-transparent border-r-transparent border-t-white/15"></div>
            </div>

            <!-- Character -->
            <div class="float">
                <img src="/storage/karakter/3.png" alt="Karakter Relawan"
                     class="h-72 xl:h-80 w-auto object-contain drop-shadow-2xl select-none">
            </div>
        </div>

        <!-- Bottom info -->
        <div class="relative z-10 flex items-center gap-6">
            <div class="text-center">
                <p class="text-white font-black text-2xl">1</p>
                <p class="text-violet-200 text-[10px] font-medium">Masukkan Email</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">2</p>
                <p class="text-violet-200 text-[10px] font-medium">Buka Tautan</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">3</p>
                <p class="text-violet-200 text-[10px] font-medium">Sandi Baru</p>
            </div>
        </div>
    </div>

    <!-- Right: Form Panel -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">

            <!-- Mobile brand -->
            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <img src="/storage/logo.png" alt="Dataraga" class="h-9 w-9 object-contain">
                <span class="font-black text-xl text-gray-900">Dataraga</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-black text-gray-900 mb-1">Lupa Kata Sandi?</h2>
                <p class="text-gray-500 text-sm">Masukkan email akunmu, kami kirim tautan untuk atur ulang sandi.</p>
            </div>

            @if (session('status'))
                <div class="mb-5 flex items-center gap-2.5 p-4 bg-green-50 text-green-700 rounded-xl text-sm border border-green-100">
                    <i class="fas fa-circle-check text-green-500"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 flex items-center gap-2.5 p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="email@kamu.com"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all text-sm">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-violet-600 text-white font-black rounded-2xl hover:bg-violet-700 shadow-lg hover:shadow-violet-500/30 transition-all active:scale-[0.98] text-base tracking-wide">
                    Kirim Tautan Atur Ulang
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Sudah ingat sandinya?
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Masuk di sini</a>
                </p>
                <p class="text-sm text-gray-400 mt-2">
                    Atau kembali ke
                    <a href="{{ url('/') }}" class="text-gray-600 font-semibold hover:underline">Halaman Utama</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
