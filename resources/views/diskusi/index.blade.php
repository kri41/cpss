@extends('layouts.app')

@section('title', 'Forum - Dataraga')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-4 sm:px-6">

    <div class="mb-4 flex items-center justify-between gap-3 flex-wrap">
        <h1 class="text-base font-bold text-gray-800">Forum Relawan</h1>
        <form method="GET" action="{{ route('diskusi.index') }}">
            <select name="provinsi" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                @foreach($provinsiList as $prov)
                    <option value="{{ $prov->kode }}" {{ $provinsi == $prov->kode ? 'selected' : '' }}>{{ $prov->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <p class="text-xs text-gray-400 mb-4">Ruang diskusi untuk relawan &amp; admin di provinsi <strong>{{ $provinsiNama }}</strong>. Halaman ini tidak auto-refresh — muat ulang untuk melihat pesan terbaru.</p>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[7fr_3fr] gap-5">
        {{-- Forum (70%) --}}
        <div>
            {{-- Form kirim pesan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
                <form method="POST" action="{{ route('diskusi.store') }}">
                    @csrf
                    <input type="hidden" name="provinsi" value="{{ $provinsi }}">
                    <textarea name="pesan" rows="2" required maxlength="1000" placeholder="Tulis pesan untuk relawan lain di provinsi ini..."
                              class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 resize-none">{{ old('pesan') }}</textarea>
                    @error('pesan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Kirim</button>
                    </div>
                </form>
            </div>

            {{-- Daftar pesan --}}
            @if($posts->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400 text-sm">
                Belum ada pesan di provinsi ini. Jadilah yang pertama menulis!
            </div>
            @else
            <div class="space-y-3">
                @foreach($posts as $post)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($post->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $post->user?->name ?? 'Pengguna Dihapus' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('diskusi.destroy', $post) }}" onsubmit="return confirm('Hapus pesan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-500 transition" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $post->pesan }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $posts->links() }}</div>
            @endif
        </div>

        {{-- Peserta (30%) --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-20">
                <div class="px-4 py-3 border-b border-gray-50">
                    <h2 class="text-sm font-bold text-gray-800">Relawan di {{ $provinsiNama }}</h2>
                    <p class="text-xs text-gray-400">{{ $peserta->count() }} terdaftar</p>
                </div>
                @if($peserta->isEmpty())
                <div class="p-6 text-center text-xs text-gray-400">Belum ada relawan terdaftar di provinsi ini.</div>
                @else
                <div class="max-h-[32rem] overflow-y-auto divide-y divide-gray-50">
                    @foreach($peserta as $orang)
                    <div class="flex items-center gap-2.5 px-4 py-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr($orang->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $orang->name }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $wilayahNama[$orang->kabupaten] ?? $orang->kabupaten ?? '-' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
