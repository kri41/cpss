@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leaderboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Peringkat kontributor data keolahragaan</p>
        </div>

        <!-- Personal Rank Card -->
        @if($personalRank)
        <div class="mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Peringkat Anda saat ini</p>
                    <p class="text-3xl font-bold mt-1">#{{ $personalRank }}</p>
                </div>
                <div class="text-right">
                    <p class="text-indigo-100 text-sm">Total Poin</p>
                    <p class="text-3xl font-bold mt-1">{{ auth()->user()->total_poin ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('leaderboard.my-points') }}" class="inline-flex items-center text-sm font-medium text-white hover:text-indigo-100">
                    Lihat detail transaksi poin &rarr;
                </a>
            </div>
        </div>
        @endif

        <!-- Period Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('leaderboard.index', ['periode' => 'mingguan']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'mingguan' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('leaderboard.index', ['periode' => 'bulanan']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'bulanan' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('leaderboard.index', ['periode' => 'total']) }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'total' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Total Program
                </a>
            </nav>
        </div>

        <!-- Leaderboard Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Peringkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Relawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Wilayah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Poin</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($leaderboard as $index => $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $rank = $leaderboard->firstItem() + $index; @endphp
                            @if($rank <= 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                    {{ $rank === 1 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $rank === 2 ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $rank === 3 ? 'bg-orange-100 text-orange-800' : '' }}">
                                    {{ $rank }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $rank }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $user->desa ?? '-' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->kecamatan ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">
                                {{ match($periode) {
                                    'mingguan' => $user->poin_mingguan ?? 0,
                                    'bulanan' => $user->poin_bulanan ?? 0,
                                    default => $user->poin_total ?? 0,
                                } }} poin
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data kontributor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $leaderboard->appends(['periode' => $periode])->links() }}
        </div>
    </div>
</div>
@endsection
