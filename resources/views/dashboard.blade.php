<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dasbor CPSS - Dispora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900 flex h-screen overflow-hidden">

    <aside class="w-64 bg-indigo-900 text-white flex flex-col hidden md:flex shadow-2xl relative z-20">
        <div class="h-20 flex items-center px-6 border-b border-indigo-800/50">
            <div
                class="w-10 h-10 bg-gradient-to-tr from-teal-400 to-emerald-300 rounded-xl shadow-lg flex items-center justify-center text-indigo-900 font-bold text-xl mr-3">
                C</div>
            <span class="font-bold text-xl tracking-wider">CPSS DOD</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="#"
                class="flex items-center px-4 py-3 bg-indigo-800/50 text-white rounded-xl shadow-inner border border-indigo-700/50 transition">
                <i class="fas fa-chart-pie w-6"></i>
                <span class="font-semibold">Statistik Utama</span>
            </a>

            @if (in_array(Auth::user()->role, ['admin', 'super_admin']))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-bold text-indigo-400 uppercase tracking-wider">Menu Khusus Dinas</p>
                </div>
                <a href="#"
                    class="flex items-center px-4 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white rounded-xl transition">
                    <i class="fas fa-file-export w-6"></i>
                    <span class="font-semibold">Ekspor Laporan DOD</span>
                </a>
                <a href="#"
                    class="flex items-center px-4 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white rounded-xl transition">
                    <i class="fas fa-users-cog w-6"></i>
                    <span class="font-semibold">Kelola Pengguna</span>
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-indigo-800/50 bg-indigo-950/30">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold border-2 border-indigo-300">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-indigo-300 truncate uppercase font-semibold tracking-wider">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full text-left text-xs text-red-400 hover:text-red-300 flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50 relative">

        <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="font-extrabold text-2xl text-indigo-950">Command Center</h2>
            <div class="flex items-center gap-4">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-bold text-gray-500">Status: <span class="text-emerald-600">Terhubung ke
                        Cloud</span></span>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full space-y-8">

            <div>
                <h3 class="text-gray-500 font-medium">Ringkasan Data Masuk</h3>
                <p class="text-2xl font-black text-gray-800 mt-1 mb-4">Capaian Sains Warga Bulan Ini</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-2xl">
                            <i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Fasilitas Dipetakan</p>
                            <h4 class="text-3xl font-black text-gray-800">124 <span
                                    class="text-sm font-normal text-emerald-500"><i class="fas fa-arrow-up"></i>
                                    12%</span></h4>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-2xl">
                            <i class="fas fa-users"></i></div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Total Partisipasi Warga</p>
                            <h4 class="text-3xl font-black text-gray-800">5,430 <span
                                    class="text-sm font-normal text-purple-500"><i class="fas fa-arrow-up"></i>
                                    8%</span></h4>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl">
                            <i class="fas fa-hands-helping"></i></div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Relawan Aktif</p>
                            <h4 class="text-3xl font-black text-gray-800">42 <span
                                    class="text-sm font-normal text-gray-400">Orang</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4">Kondisi Prasarana Olahraga Daerah (Pilar 4)</h4>
                    <div class="relative h-64 w-full">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4">Tren Partisipasi Mingguan (Pilar 1)</h4>
                    <div class="relative h-64 w-full">
                        <canvas id="partisipasiChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-gray-500 font-medium">Panel Kontribusi Data</h3>
                <p class="text-2xl font-black text-gray-800 mt-1 mb-6">Pilih Sektor Input DOD</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-fr">
                    <div
                        class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 hover:border-purple-200 transition-all duration-300 group flex flex-col justify-between relative overflow-hidden">
                        <div class="relative z-10">
                            <div
                                class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl mb-4">
                                <i class="fas fa-running"></i></div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Pilar 1: Partisipasi</h4>
                            <p class="text-gray-500 text-sm mb-6">Pelaporan cek-in masyarakat dan pelajar aktif
                                berolahraga.</p>
                        </div>
                        <button
                            class="w-full py-2.5 bg-purple-50 text-purple-700 font-bold rounded-lg group-hover:bg-purple-600 group-hover:text-white transition relative z-10 border border-purple-100">Input
                            Partisipasi</button>
                    </div>

                    @if (in_array(Auth::user()->role, ['admin', 'super_admin']))
                        <div
                            class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-[1.5rem] p-6 shadow-sm border border-indigo-100 hover:shadow-xl hover:-translate-y-1 hover:border-indigo-300 transition-all duration-300 group flex flex-col justify-between relative overflow-hidden">
                            <div class="relative z-10">
                                <div
                                    class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-trophy"></i></div>
                                <div class="flex justify-between items-start">
                                    <h4 class="text-lg font-bold text-indigo-900 mb-1">Pilar 2: Talenta Muda</h4>
                                    <span
                                        class="text-[10px] font-bold bg-indigo-200 text-indigo-800 px-2 py-1 rounded uppercase">Internal</span>
                                </div>
                                <p class="text-indigo-700/70 text-sm mb-6">Pusat data kompetisi, kelas olahraga, dan
                                    beasiswa.</p>
                            </div>
                            <button
                                class="w-full py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition relative z-10">Kelola
                                Database Talenta</button>
                        </div>
                    @endif

                    @if (in_array(Auth::user()->role, ['admin', 'super_admin']))
                        <div
                            class="bg-gradient-to-br from-slate-50 to-gray-100 rounded-[1.5rem] p-6 shadow-sm border border-slate-200 hover:shadow-xl hover:-translate-y-1 hover:border-slate-400 transition-all duration-300 group flex flex-col justify-between relative overflow-hidden">
                            <div class="relative z-10">
                                <div
                                    class="w-12 h-12 bg-slate-800 text-white rounded-xl flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-chalkboard-teacher"></i></div>
                                <div class="flex justify-between items-start">
                                    <h4 class="text-lg font-bold text-slate-900 mb-1">Pilar 3: Tenaga Ahli</h4>
                                    <span
                                        class="text-[10px] font-bold bg-slate-300 text-slate-800 px-2 py-1 rounded uppercase">Internal</span>
                                </div>
                                <p class="text-slate-600 text-sm mb-6">Data fasilitator, instruktur, dan guru PJOK.</p>
                            </div>
                            <button
                                class="w-full py-2.5 bg-slate-800 text-white font-bold rounded-lg shadow-md hover:bg-slate-900 transition relative z-10">Monitor
                                Sertifikasi</button>
                        </div>
                    @endif

                    <div
                        class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300 group flex flex-col justify-between relative overflow-hidden">
                        <div class="relative z-10">
                            <div
                                class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl mb-4">
                                <i class="fas fa-futbol"></i></div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Pilar 4: Prasarana</h4>
                            <p class="text-gray-500 text-sm mb-6">Pemetaan kelayakan fasilitas, lantai, dan
                                aksesibilitas.</p>
                        </div>
                        <button
                            class="w-full py-2.5 bg-emerald-50 text-emerald-700 font-bold rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition relative z-10 border border-emerald-100">Input
                            Lapangan Baru</button>
                    </div>

                    <div
                        class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 hover:border-orange-200 transition-all duration-300 group flex flex-col justify-between relative overflow-hidden">
                        <div class="relative z-10">
                            <div
                                class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-xl mb-4">
                                <i class="fas fa-bullhorn"></i></div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Pilar 5: Wisata & Event</h4>
                            <p class="text-gray-500 text-sm mb-6">Laporan event Tarkam, festival tradisional, wisata
                                olahraga.</p>
                        </div>
                        <button
                            class="w-full py-2.5 bg-orange-50 text-orange-700 font-bold rounded-lg group-hover:bg-orange-600 group-hover:text-white transition relative z-10 border border-orange-100">Lapor
                            Event Lokal</button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Grafik Doughnut (Kondisi Lapangan)
            const ctxKondisi = document.getElementById('kondisiChart').getContext('2d');
            new Chart(ctxKondisi, {
                type: 'doughnut',
                data: {
                    labels: ['Baik', 'Sedang', 'Rusak Berat'],
                    datasets: [{
                        data: [65, 35, 24],
                        backgroundColor: ['#10B981', '#FBBF24', '#EF4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Grafik Garis (Tren Partisipasi)
            const ctxPartisipasi = document.getElementById('partisipasiChart').getContext('2d');
            new Chart(ctxPartisipasi, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Jumlah Orang Berolahraga',
                        data: [120, 190, 150, 220, 310, 540, 680],
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.2)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung halus
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
