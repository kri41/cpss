<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Daftar Relawan</h2>
                <p class="text-sm text-gray-500 mt-1">Temukan penggerak olahraga di daerah Anda dan mulai berkolaborasi.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form method="GET" action="{{ route('relawan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Provinsi</label>
                        <select name="provinsi" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Provinsi</option>
                            @foreach($filterProvinsi as $p)
                                <option value="{{ $p }}" {{ request('provinsi') == $p ? 'selected' : '' }}>{{ $wilayahNama[$p] ?? $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kab/Kota</label>
                        <select name="kabupaten" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kab/Kota</option>
                            @foreach($filterKabupaten as $k)
                                <option value="{{ $k }}" {{ request('kabupaten') == $k ? 'selected' : '' }}>{{ $wilayahNama[$k] ?? $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kecamatan</label>
                        <select name="kecamatan" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kecamatan</option>
                            @foreach($filterKecamatan as $k)
                                <option value="{{ $k }}" {{ request('kecamatan') == $k ? 'selected' : '' }}>{{ $wilayahNama[$k] ?? $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Grid Relawan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($relawan as $r)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-100 transition-all duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-xl shadow-md">
                                {{ substr($r->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-gray-900 truncate">{{ $r->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $r->email }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            @if($r->provinsi)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $wilayahNama[$r->provinsi] ?? $r->provinsi }}</span>
                                </div>
                            @endif
                            @if($r->kabupaten)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span class="truncate">{{ $wilayahNama[$r->kabupaten] ?? $r->kabupaten }}</span>
                                </div>
                            @endif
                            @if($r->kecamatan)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-.553-.894L15 7m0 13V7"/></svg>
                                    <span class="truncate">{{ $wilayahNama[$r->kecamatan] ?? $r->kecamatan }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="https://wa.me/?text=Halo%20{{ urlencode($r->name) }}%2C%20saya%20ingin%20berdiskusi%20mengenai%20keolahragaan%20daerah." target="_blank"
                                class="flex-1 text-center px-3 py-2 bg-green-50 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-100 transition border border-green-200">
                                <i class="fab fa-whatsapp mr-1"></i> Hubungi
                            </a>
                            <a href="mailto:{{ $r->email }}"
                                class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-100 transition border border-blue-200">
                                <i class="fas fa-envelope mr-1"></i> Email
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-lg font-medium text-gray-600">Belum ada relawan terdaftar.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $relawan->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
