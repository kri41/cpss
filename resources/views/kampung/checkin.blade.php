<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in Olahraga — {{ $fasil->nama_fasilitas }}</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        html, body { height: auto; overflow: auto; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(160deg, #eff6ff 0%, #f0f9ff 45%, #ffffff 100%);
            min-height: 100vh;
        }
        @media (min-width: 1024px) {
            html, body { height: 100%; overflow: hidden; }
        }
        .form-input {
            display: block; width: 100%; padding: 0.75rem 1rem;
            border: 1.5px solid #dbeafe; border-radius: 0.9rem;
            font-size: 1rem; outline: none; background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
        }
        .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37,99,235,0.12); }
        .dropdown-item { padding: 0.6rem 1rem; cursor: pointer; }
        .dropdown-item:hover { background: #eff6ff; }
        .select-wrap { position: relative; }
        .select-wrap::after {
            content: ''; position: absolute; right: 1rem; top: 50%; width: 0.55rem; height: 0.55rem;
            border-right: 2px solid #93c5fd; border-bottom: 2px solid #93c5fd;
            transform: translateY(-70%) rotate(45deg); pointer-events: none;
        }
        .select-wrap select { appearance: none; padding-right: 2.5rem; }
        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        .floaty { animation: floaty 3s ease-in-out infinite; }
    </style>
</head>
<body>

<div x-data="checkinApp()" class="min-h-screen lg:h-screen flex flex-col">

    {{-- Header: logo + Dataraga saja --}}
    <div class="shrink-0" style="background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 45%, #0369a1 100%);">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-2.5">
            <img src="/storage/logo.png" alt="Dataraga" class="h-7 w-7 object-contain brightness-0 invert opacity-90">
            <span class="text-white font-bold text-sm">Dataraga</span>
        </div>
    </div>

    {{-- 2 Kolom: Info Fasil (kiri) + Form Check-in (kanan) --}}
    <div class="max-w-5xl mx-auto px-4 py-4 w-full lg:flex-1 lg:min-h-0 lg:flex lg:items-center">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start lg:max-h-full w-full">

            {{-- KOLOM 1: Foto & Informasi Fasil --}}
            <div class="lg:col-span-2 lg:max-h-full lg:overflow-y-auto">
                <div class="bg-white rounded-3xl shadow-xl border border-blue-50 overflow-hidden">
                    @if($fasil->foto_path)
                    <img src="{{ asset('storage/' . $fasil->foto_path) }}" alt="{{ $fasil->nama_fasilitas }}"
                         class="w-full h-48 sm:h-56 lg:h-44 object-cover">
                    @else
                    <div class="w-full h-40 flex items-center justify-center" style="background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);">
                        <div class="floaty w-16 h-16 rounded-2xl bg-white/70 flex items-center justify-center">
                            <i class="fas fa-person-running text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                    @endif

                    <div class="p-5">
                        <h1 class="text-lg font-extrabold text-gray-900 leading-tight">
                            {{ $fasil->nama_fasilitas }}
                            <i class="fas fa-circle-check text-emerald-500 text-sm align-middle ml-1" title="Kampung Olahraga Terverifikasi"></i>
                        </h1>
                        <p class="text-sm text-blue-600 font-semibold mt-0.5">{{ $fasil->kategori_olahraga }}</p>

                        <div class="mt-3 pt-3 border-t border-gray-50 space-y-2 text-sm text-gray-600">
                            <div class="flex gap-2">
                                <i class="fas fa-house text-blue-300 w-4 mt-0.5"></i>
                                <span>{{ $kampung->nama_kampung }}</span>
                            </div>
                            @if($fasil->alamat)
                            <div class="flex gap-2">
                                <i class="fas fa-location-dot text-blue-300 w-4 mt-0.5"></i>
                                <span>{{ $fasil->alamat }}</span>
                            </div>
                            @endif
                            <div class="flex gap-2">
                                <i class="fas fa-map text-blue-300 w-4 mt-0.5"></i>
                                <span>
                                    @if($kampung->rt_rw_label){{ $kampung->rt_rw_label }} &middot; @endif
                                    {{ collect([$kampung->desa, $kampung->kecamatan, $kampung->kabupaten])->filter()->implode(', ') }}
                                </span>
                            </div>
                            @if($fasil->getAverageKondisiAttribute() > 0)
                            <div class="flex gap-2 items-center">
                                <i class="fas fa-star text-amber-400 w-4"></i>
                                <span>Kondisi rata-rata: <strong class="text-gray-800">{{ $fasil->getAverageKondisiAttribute() }}/5</strong></span>
                            </div>
                            @endif
                        </div>

                        @php
                            $aksesBadges = collect([
                                $fasil->akses_disabilitas ? 'Akses Disabilitas' : null,
                                $fasil->akses_parkir ? 'Parkir' : null,
                                $fasil->akses_transportasi ? 'Akses Transportasi' : null,
                                $fasil->fasilitas_ruang_ganti ? 'Ruang Ganti' : null,
                                $fasil->fasilitas_tribun ? 'Tribun' : null,
                            ])->filter();
                        @endphp
                        @if($aksesBadges->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-gray-50 flex flex-wrap gap-1.5">
                            @foreach($aksesBadges as $badge)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-full">
                                <i class="fas fa-check text-[9px]"></i> {{ $badge }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <p class="hidden lg:block text-center text-xs text-gray-400 mt-4">Data ini akan digunakan untuk program Kampung Olahraga Kemenpora RI</p>
            </div>

            {{-- KOLOM 2: Form Check-in --}}
            <div class="lg:col-span-3 lg:max-h-full lg:overflow-y-auto">
                <div class="bg-white rounded-3xl shadow-xl border border-blue-50 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 text-base">Daftar Aktivitas Olahraga</h2>
                            <p class="text-xs text-gray-500">Isi data di bawah untuk tercatat sebagai peserta aktif.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('kampung.checkin.store', $fasil->qr_token) }}"
                          enctype="multipart/form-data" @submit.prevent="submitForm($event)" class="p-5 space-y-4">
                        @csrf

                        @if($errors->any())
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Nama --}}
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-user text-blue-400 mr-1"></i> Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_peserta" value="{{ old('nama_peserta') }}" placeholder="Masukkan nama lengkap Anda" required
                                       autocomplete="name" class="form-input @error('nama_peserta') border-red-400 @enderror">
                            </div>

                            {{-- Umur --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-cake-candles text-blue-400 mr-1"></i> Umur <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="umur" value="{{ old('umur') }}" placeholder="Umur (tahun)" required
                                       min="1" max="120" inputmode="numeric" class="form-input @error('umur') border-red-400 @enderror">
                            </div>

                            {{-- Klub/Komunitas --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-people-group text-blue-400 mr-1"></i> Klub/Komunitas
                                </label>
                                <div class="select-wrap">
                                    <select name="club_id" x-model="clubId" class="form-input @error('club_id') border-red-400 @enderror">
                                        <option value="">Belum bergabung</option>
                                        @foreach($klubList as $k)
                                        <option value="{{ $k->id }}" {{ old('club_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_club }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <template x-if="clubId && clubSport(clubId)">
                            <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <p class="text-xs text-emerald-700">Olahraga otomatis terisi: <span class="font-bold" x-text="clubSport(clubId)"></span></p>
                            </div>
                        </template>

                        {{-- Jenis Olahraga: searchable dropdown (hanya jika belum bergabung) --}}
                        <div x-data="olahragaDropdown()" x-show="!clubId" x-cloak class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-volleyball text-blue-400 mr-1"></i> Jenis Olahraga
                            </label>

                            <input type="hidden" name="jenis_olahraga_id" x-model="selectedId">
                            <input type="hidden" name="jenis_olahraga_baru" x-model="newName">

                            <div @click.away="open = false">
                                <input type="text" x-model="search" @focus="open = true" @input="filterList()"
                                       placeholder="Cari jenis olahraga..." autocomplete="off"
                                       class="form-input cursor-pointer"
                                       :value="selectedLabel || search">

                                {{-- Dropdown --}}
                                <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full bg-white rounded-xl shadow-xl border border-blue-100 overflow-hidden max-h-52 overflow-y-auto">

                                    {{-- Existing options --}}
                                    <template x-for="opt in filtered" :key="opt.id">
                                        <div class="dropdown-item text-sm text-gray-700" @click="select(opt)">
                                            <span x-text="opt.nama"></span>
                                        </div>
                                    </template>

                                    {{-- Add new if no match --}}
                                    <template x-if="search.trim() && !filtered.length">
                                        <div class="dropdown-item text-sm text-blue-700 font-semibold" @click="addNew()">
                                            <span class="mr-1">+</span> Tambah "<span x-text="search.trim()"></span>"
                                        </div>
                                    </template>

                                    <template x-if="!search.trim() && !filtered.length">
                                        <div class="px-4 py-3 text-sm text-gray-400">Tidak ada pilihan</div>
                                    </template>
                                </div>
                            </div>

                            <p x-show="newName" x-cloak class="mt-1 text-xs text-blue-600">
                                Akan ditambahkan: "<span x-text="newName" class="font-semibold"></span>" (olahraga baru)
                            </p>
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-camera text-blue-400 mr-1"></i> Foto Aktivitas
                                <span class="text-xs font-normal text-gray-400">(opsional, maks 200KB)</span>
                            </label>

                            <div x-data="photoUpload()" class="space-y-2">
                                <input type="file" name="foto" accept="image/*" capture="environment"
                                       id="foto-input" class="hidden" @change="onFile($event)">

                                <label x-show="!preview" x-cloak for="foto-input"
                                       class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-blue-200 rounded-2xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                    <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center mb-2">
                                        <i class="fas fa-camera-retro text-blue-400"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-blue-600">Ambil / Unggah Foto</span>
                                    <span class="text-xs text-gray-400 mt-0.5">Otomatis dikompres agar ringan</span>
                                </label>

                                <div x-show="preview" x-cloak class="relative">
                                    <img :src="preview" class="w-full h-44 object-cover rounded-2xl border border-blue-100">
                                    <button type="button" @click="clearPhoto()" class="absolute top-2 right-2 bg-white rounded-full p-1.5 shadow text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <span x-show="sizeText" class="absolute bottom-2 left-2 text-[10px] bg-black/50 text-white px-2 py-0.5 rounded-full" x-text="sizeText"></span>
                                </div>

                                {{-- Hidden canvas for compression --}}
                                <canvas id="compressCanvas" class="hidden"></canvas>
                            </div>
                        </div>

                        <button type="submit" :disabled="submitting"
                                class="w-full py-3.5 text-sm font-bold text-white rounded-2xl transition shadow-lg"
                                :class="submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 hover:shadow-blue-500/30 active:scale-[0.98]'">
                            <span x-show="!submitting" class="inline-flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Kirim Check-in
                            </span>
                            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                                <i class="fas fa-circle-notch fa-spin"></i> Mengirim...
                            </span>
                        </button>
                    </form>
                </div>

                <p class="lg:hidden text-center text-xs text-gray-400 mt-4">Data ini akan digunakan untuk program Kampung Olahraga Kemenpora RI</p>
            </div>

        </div>
    </div>
</div>

<script>
const allJenis = @json($jenisOlahraga);
const allKlub = @json($klubList->map(fn($k) => ['id' => $k->id, 'sport' => $k->jenisOlahraga?->nama]));

function checkinApp() {
    return {
        submitting: false,
        clubId: '{{ old('club_id') }}',
        clubSport(id) {
            const k = allKlub.find(x => String(x.id) === String(id));
            return k ? k.sport : null;
        },
        submitForm(e) { this.submitting = true; e.target.submit(); }
    };
}

function olahragaDropdown() {
    return {
        open: false,
        search: '',
        selectedId: null,
        selectedLabel: '',
        newName: '',
        all: allJenis,
        filtered: allJenis,
        filterList() {
            this.selectedId = null;
            this.selectedLabel = '';
            this.newName = '';
            const q = this.search.trim().toLowerCase();
            this.filtered = q ? this.all.filter(o => o.nama.toLowerCase().includes(q)) : this.all;
        },
        select(opt) {
            this.selectedId = opt.id;
            this.selectedLabel = opt.nama;
            this.search = opt.nama;
            this.newName = '';
            this.open = false;
        },
        addNew() {
            this.newName = this.search.trim();
            this.selectedId = null;
            this.selectedLabel = this.newName;
            this.open = false;
        },
    };
}

function photoUpload() {
    return {
        preview: null,
        sizeText: '',
        compressedBlob: null,
        onFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.compressImage(file);
        },
        compressImage(file) {
            const self = this;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.getElementById('compressCanvas');
                    let w = img.width, h = img.height;
                    const maxDim = 1024;
                    if (w > maxDim || h > maxDim) {
                        const r = Math.min(maxDim / w, maxDim / h);
                        w = Math.round(w * r); h = Math.round(h * r);
                    }
                    canvas.width = w; canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    const maxKB = 200 * 1024;
                    let quality = 0.82;
                    const tryCompress = () => {
                        canvas.toBlob(blob => {
                            if (blob && (blob.size > maxKB) && quality > 0.2) {
                                quality = Math.max(0.2, quality - 0.1);
                                tryCompress();
                            } else {
                                self.compressedBlob = blob;
                                const url = URL.createObjectURL(blob);
                                self.preview = url;
                                self.sizeText = blob ? Math.round(blob.size / 1024) + ' KB' : '';
                                // Replace file input value by injecting compressed file
                                const dt = new DataTransfer();
                                const compressed = new File([blob], 'foto.jpg', { type: 'image/jpeg' });
                                dt.items.add(compressed);
                                document.getElementById('foto-input').files = dt.files;
                            }
                        }, 'image/jpeg', quality);
                    };
                    tryCompress();
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        },
        clearPhoto() {
            this.preview = null;
            this.sizeText = '';
            this.compressedBlob = null;
            document.getElementById('foto-input').value = '';
        },
    };
}
</script>
</body>
</html>
