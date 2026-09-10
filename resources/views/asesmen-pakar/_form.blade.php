@php $a = $asesmen ?? null; @endphp

@if($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

{{-- ===== IDENTITAS ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
    <h2 class="text-base font-bold text-gray-900">Identitas &amp; Waktu Kerja Asesor</h2>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prasarana yang dinilai <span class="text-red-500">*</span></label>
        <select name="prasarana_id" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">— pilih prasarana —</option>
            @foreach($prasaranaList->groupBy('kabupaten') as $kab => $grup)
                <optgroup label="{{ $kab ?: 'Tanpa wilayah' }}">
                    @foreach($grup as $p)
                        <option value="{{ $p->id }}" @selected((string) old('prasarana_id', $a?->prasarana_id) === (string) $p->id)>
                            #{{ $p->id }} — {{ $p->nama_fasilitas }} ({{ $p->kecamatan ?: '-' }})
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">Cocokkan dengan nomor ID pada Daftar Lokasi Sasaran.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Asesor <span class="text-red-500">*</span></label>
            <input type="text" name="nama_asesor" value="{{ old('nama_asesor', $a?->nama_asesor) }}" required
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Asesmen</label>
            <input type="date" name="tanggal_asesmen" value="{{ old('tanggal_asesmen', $a?->tanggal_asesmen?->format('Y-m-d')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Waktu Mulai Menilai <span class="text-xs font-normal text-gray-400">(T1)</span></label>
            <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', $a?->waktu_mulai?->format('Y-m-d\TH:i')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Waktu Selesai Menilai <span class="text-xs font-normal text-gray-400">(T1)</span></label>
            <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai', $a?->waktu_selesai?->format('Y-m-d\TH:i')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lama Menyusun Laporan <span class="text-xs font-normal text-gray-400">(T2, menit)</span></label>
            <input type="number" step="0.5" min="0" name="durasi_laporan_menit"
                   value="{{ old('durasi_laporan_menit', $a?->durasi_laporan_detik ? round($a->durasi_laporan_detik / 60, 1) : '') }}"
                   class="w-full sm:w-40 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
</div>

{{-- ===== KONDISI ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-base font-bold text-gray-900 mb-1">Penilaian Kondisi (skala 1&ndash;5)</h2>
    <p class="text-xs text-gray-400 mb-4">Kosongkan komponen yang tidak relevan / tidak dinilai.</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(\App\Models\AsesmenPakar::KONDISI as $key => $label)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <select name="{{ $key }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">—</option>
                    @foreach($ratingLabels as $val => $rl)
                        <option value="{{ $val }}" @selected((string) old($key, $a?->{$key}) === (string) $val)>{{ $val }} — {{ $rl }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
    </div>
</div>

{{-- ===== AKSESIBILITAS & KELENGKAPAN ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">Aksesibilitas &amp; Kelengkapan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach(\App\Models\AsesmenPakar::AKSES as $key => $label)
            @php $cur = old($key, $a ? ($a->{$key} === null ? '' : ($a->{$key} ? '1' : '0')) : ''); @endphp
            <div>
                <p class="text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</p>
                <div class="flex gap-3 text-sm">
                    <label class="flex items-center gap-1.5"><input type="radio" name="{{ $key }}" value="1" @checked((string) $cur === '1') class="text-blue-600"> Ya</label>
                    <label class="flex items-center gap-1.5"><input type="radio" name="{{ $key }}" value="0" @checked((string) $cur === '0') class="text-blue-600"> Tidak</label>
                    <label class="flex items-center gap-1.5"><input type="radio" name="{{ $key }}" value="" @checked((string) $cur === '') class="text-gray-400"> —</label>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ===== KATEGORI OLAHRAGA ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-base font-bold text-gray-900 mb-4">Kategori Olahraga (menurut asesor)</h2>
    @php $catPilih = collect(old('kategori_olahraga_ids', $a?->kategori_olahraga_ids ?? []))->map(fn ($v) => (string) $v); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
        @foreach($jenisOlahragaList as $j)
            <label class="flex items-center gap-2 text-sm p-2 rounded-lg border border-gray-100 hover:bg-gray-50">
                <input type="checkbox" name="kategori_olahraga_ids[]" value="{{ $j->id }}" @checked($catPilih->contains((string) $j->id)) class="rounded border-gray-300 text-blue-600">
                {{ $j->nama }}
            </label>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan</label>
    <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('catatan', $a?->catatan) }}</textarea>
</div>

<div class="flex gap-2">
    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan</button>
    <a href="{{ route('asesmen-pakar.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
</div>
