<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Banner -->
            <div class="mb-8 bg-gradient-to-r from-blue-600 to-sky-500 rounded-2xl p-6 sm:p-8 text-white shadow-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-bold mb-2">Pemetaan Olahraga Masyarakat</h3>
                        <p class="text-blue-100 max-w-2xl">Platform kolaborasi untuk memetakan fasilitas, aktivitas, komunitas, dan event olahraga daerah.</p>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <a href="{{ route('prasarana.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">{{ $stats['total_prasarana'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Prasarana</p>
                </a>

                <a href="{{ route('clubs.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-sky-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-sky-100 rounded-lg group-hover:bg-sky-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-sky-600 bg-sky-50 px-2 py-1 rounded-full">{{ $stats['total_clubs'] ?? 0 }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Clubs</p>
                </a>

                <div class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">{{ number_format($stats['total_partisipasi']) }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Partisipasi</p>
                </div>

                <a href="{{ route('events.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">{{ $stats['total_events'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Events</p>
                </a>

                <a href="{{ route('talenta.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">{{ $stats['total_talenta'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Talenta</p>
                </a>

                <a href="{{ route('tenaga-ahli.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-red-200 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">{{ $stats['total_tenaga_ahli'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Tenaga Ahli</p>
                </a>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Tren Partisipasi</h3>
                            <p class="text-sm text-gray-500">6 bulan terakhir</p>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg></div>
                    </div>
                    <div class="h-64"><canvas id="participationChart"></canvas></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Kondisi Prasarana</h3>
                            <p class="text-sm text-gray-500">Berdasarkan kondisi lantai</p>
                        </div>
                        <div class="p-2 bg-green-50 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg></div>
                    </div>
                    <div class="h-64"><canvas id="facilityChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Event Akan Datang</h3>
                                <p class="text-sm text-gray-500">Jadwal event terdekat</p>
                            </div>
                            <a href="{{ route('events.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($upcomingEvents->count() > 0)
                            <div class="space-y-4">
                                @foreach($upcomingEvents as $event)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex-shrink-0 w-14 h-14 bg-blue-100 rounded-xl flex flex-col items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600 uppercase">{{ $event->tanggal_mulai->format('M') }}</span>
                                            <span class="text-lg font-bold text-blue-800">{{ $event->tanggal_mulai->format('d') }}</span>
                                        </div>
                                        <div class="ml-4 flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 truncate">{{ $event->nama_event }}</h4>
                                            <p class="text-sm text-gray-500">{{ $event->tingkat }} &bull; {{ $event->lokasi ?? 'Lokasi belum ditentukan' }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full whitespace-nowrap
                                            {{ $event->status === 'Akan Datang' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $event->status === 'Sedang Berlangsung' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $event->status === 'Selesai' ? 'bg-gray-100 text-gray-800' : '' }}">{{ $event->status }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="p-4 bg-gray-100 rounded-full inline-block mb-3"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                                <p class="text-gray-500">Tidak ada event yang akan datang.</p>
                                <a href="{{ route('events.create') }}" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Buat event baru</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Partisipasi by Usia</h3>
                    <p class="text-sm text-gray-500 mb-6">Distribusi berdasarkan kelompok usia</p>
                    <div class="h-48"><canvas id="usiaChart"></canvas></div>
                </div>
            </div>

            @if(auth()->user()->isAdmin() && $recentAuditLogs->count() > 0)
                <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h3>
                            <p class="text-sm text-gray-500">Log aktivitas sistem</p>
                        </div>
                        <a href="{{ route('audit-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tabel</th></tr></thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentAuditLogs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $log->action === 'CREATE' ? 'bg-green-100 text-green-800' : '' }} {{ $log->action === 'UPDATE' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->action === 'DELETE' ? 'bg-red-100 text-red-800' : '' }}">{{ $log->action }}</span></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->target_table }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const ctxPart = document.getElementById('participationChart').getContext('2d');
        new Chart(ctxPart, {
            type: 'line',
            data: {
                labels: {!! json_encode($partisipasiPerBulan->pluck('bulan')->map(function($b) { return \Carbon\Carbon::parse($b)->format('M Y'); })) !!},
                datasets: [{
                    label: 'Partisipasi',
                    data: {!! json_encode($partisipasiPerBulan->pluck('total')) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3, fill: true, tension: 0.4,
                    pointBackgroundColor: '#2563eb', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } }
        });

        const ctxFac = document.getElementById('facilityChart').getContext('2d');
        new Chart(ctxFac, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($prasaranaByKondisi->pluck('kondisi_lantai')) !!},
                datasets: [{ data: {!! json_encode($prasaranaByKondisi->pluck('total')) !!}, backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } } }
        });

        const ctxUsia = document.getElementById('usiaChart').getContext('2d');
        new Chart(ctxUsia, {
            type: 'bar',
            data: {
                labels: {!! json_encode($partisipasiByUsia->pluck('mayoritas_usia')) !!},
                datasets: [{ label: 'Jumlah', data: {!! json_encode($partisipasiByUsia->pluck('total')) !!}, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'], borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } }
        });
    </script>
    @endpush
</x-app-layout>
