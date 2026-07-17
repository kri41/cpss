<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .char-panel {
            background: linear-gradient(160deg, #1e3a8a 0%, #1d4ed8 40%, #0369a1 80%, #0891b2 100%);
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
        <div class="absolute top-1/2 right-0 w-32 h-32 bg-teal-400/10 rounded-full translate-x-1/2 pointer-events-none"></div>

        <!-- Branding Top -->
        <div class="relative z-10 flex items-center gap-3">
            <img src="/storage/logo.png" alt="Dataraga" class="h-10 w-10 object-contain brightness-0 invert">
            <div>
                <p class="text-white font-black text-xl tracking-tight">Dataraga</p>
                <p class="text-blue-200 text-[10px] font-medium tracking-widest uppercase">Mencatat Gerak</p>
            </div>
        </div>

        <!-- Character -->
        <div class="relative z-10 flex flex-col items-center gap-4">
            <!-- Speech bubble -->
            <div class="bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-white text-center max-w-[220px] relative">
                <p class="font-bold text-base leading-snug">"Halo! Ayo login<br>dan mulai berkontribusi!"</p>
                <p class="text-blue-200 text-xs mt-1">— Tim Dataraga</p>
                <!-- tail -->
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[8px] border-r-[8px] border-t-[12px] border-l-transparent border-r-transparent border-t-white/15"></div>
            </div>

            <!-- Boy character -->
            <div class="float">
                <img src="/storage/karakter/3.png" alt="Karakter Relawan"
                     class="h-72 xl:h-80 w-auto object-contain drop-shadow-2xl select-none">
            </div>
        </div>

        <!-- Bottom stats -->
        <div class="relative z-10 flex items-center gap-6">
            <div class="text-center">
                <p class="text-white font-black text-2xl">100%</p>
                <p class="text-blue-200 text-[10px] font-medium">Gratis</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">Desa</p>
                <p class="text-blue-200 text-[10px] font-medium">Hingga Provinsi</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">Real</p>
                <p class="text-blue-200 text-[10px] font-medium">Data Nyata</p>
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
                <h2 class="text-3xl font-black text-gray-900 mb-1">Masuk Sistem</h2>
                <p class="text-gray-500 text-sm">Selamat datang kembali, relawan!</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 flex items-center gap-2.5 p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                    Email atau kata sandi tidak sesuai.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="email@kamu.com"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-semibold hover:underline">Lupa sandi?</a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-lg hover:shadow-blue-500/30 transition-all active:scale-[0.98] text-base tracking-wide">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Kembali ke
                    <a href="{{ url('/') }}" class="text-blue-600 font-bold hover:underline">Halaman Utama</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
