<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Partisipasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Lokasi Observasi</h3>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ $partisipasi->lokasi_observasi }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Wilayah</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $partisipasi->desa ?? '-' }} / {{ $partisipasi->kecamatan ?? '-' }} / {{ $partisipasi->kabupaten ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tanggal Observasi</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $partisipasi->tanggal_observasi->format('d F Y') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Estimasi Jumlah Orang</h3>
                            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($partisipasi->estimasi_jumlah_orang) }} orang</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kehadiran Tercatat</h3>
                            <p class="mt-1 text-2xl font-bold text-green-600">{{ $partisipasi->kehadiran->count() }} orang</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Mayoritas Kelompok Usia</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $partisipasi->mayoritas_usia }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Dicatat Oleh</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $partisipasi->user->name ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Waktu Pencatatan</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $partisipasi->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Kehadiran Individu -->
                    <div class="mt-10">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Kehadiran Individu</h3>
                            @auth
                                @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                                <a href="{{ route('partisipasi.edit', $partisipasi) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    + Tambah Kehadiran
                                </a>
                                @endif
                            @endauth
                        </div>

                        @if($partisipasi->kehadiran->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usia</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelompok</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($partisipasi->kehadiran as $k)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $k->nama_peserta }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->jenis_kelamin ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->usia ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->kelompok_usia ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $k->status === 'Hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $k->status === 'Izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $k->status === 'Sakit' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $k->status === 'Alfa' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $k->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->catatan ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center text-gray-500">
                                Belum ada data kehadiran individu untuk observasi ini.
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('partisipasi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                            <a href="{{ route('partisipasi.edit', $partisipasi) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Edit') }}
                            </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
