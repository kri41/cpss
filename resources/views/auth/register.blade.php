<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — Dataraga</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .char-panel {
            background: linear-gradient(160deg, #0f766e 0%, #0d9488 40%, #059669 80%, #047857 100%);
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
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
        <div class="absolute top-1/2 left-0 w-32 h-32 bg-emerald-400/10 rounded-full -translate-x-1/2 pointer-events-none"></div>

        <!-- Branding Top -->
        <div class="relative z-10 flex items-center gap-3">
            <img src="/storage/logo.png" alt="Dataraga" class="h-10 w-10 object-contain brightness-0 invert">
            <div>
                <p class="text-white font-black text-xl tracking-tight">Dataraga</p>
                <p class="text-teal-200 text-[10px] font-medium tracking-widest uppercase">Membangun Bangsa</p>
            </div>
        </div>

        <!-- Character -->
        <div class="relative z-10 flex flex-col items-center gap-4">
            <!-- Speech bubble -->
            <div class="bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-5 py-3 text-white text-center max-w-[220px] relative">
                <p class="font-bold text-base leading-snug">"Daftar sekarang dan<br>jadilah bagian dari<br>gerakan olahraga!"</p>
                <p class="text-teal-200 text-xs mt-1">— Tim Dataraga</p>
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[8px] border-r-[8px] border-t-[12px] border-l-transparent border-r-transparent border-t-white/15"></div>
            </div>

            <!-- Girl character -->
            <div class="float">
                <img src="/storage/karakter/2.png" alt="Karakter Relawan"
                     class="h-72 xl:h-80 w-auto object-contain drop-shadow-2xl select-none">
            </div>
        </div>

        <!-- Bottom info -->
        <div class="relative z-10 flex items-center gap-6">
            <div class="text-center">
                <p class="text-white font-black text-2xl">Data</p>
                <p class="text-teal-200 text-[10px] font-medium">Keolahragaan</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">Desa</p>
                <p class="text-teal-200 text-[10px] font-medium">Hingga Provinsi</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div class="text-center">
                <p class="text-white font-black text-2xl">Kamu</p>
                <p class="text-teal-200 text-[10px] font-medium">Bisa Berperan!</p>
            </div>
        </div>
    </div>

    <!-- Right: Form Panel -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 overflow-y-auto">
        <div class="w-full max-w-md py-4">

            <!-- Mobile brand -->
            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <img src="/storage/logo.png" alt="Dataraga" class="h-9 w-9 object-contain">
                <span class="font-black text-xl text-gray-900">Dataraga</span>
            </div>

            <div class="mb-7">
                <h2 class="text-3xl font-black text-gray-900 mb-1">Daftar Relawan</h2>
                <p class="text-gray-500 text-sm">Bergabunglah menjadi penggerak olahraga daerah.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 flex items-center gap-2.5 p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                    Mohon periksa kembali isian Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="Nama lengkap kamu"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Aktif</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="email@kamu.com"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required placeholder="Min. 8 karakter"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-double text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi"
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="w-full py-3.5 bg-teal-600 text-white font-black rounded-2xl hover:bg-teal-700 shadow-lg hover:shadow-teal-500/30 transition-all active:scale-[0.98] text-base tracking-wide">
                        Daftar &amp; Langsung Masuk
                    </button>
                </div>
            </form>

            <div class="mt-7 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-teal-600 font-bold hover:underline">Masuk di sini</a>
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
