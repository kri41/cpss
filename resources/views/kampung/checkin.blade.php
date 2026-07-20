<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in Olahraga — {{ $kampung->nama_kampung }}</title>
    <link rel="icon" href="/storage/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background: #f0fdf4; min-height: 100vh; }
        .form-input {
            display: block; width: 100%; padding: 0.625rem 0.875rem;
            border: 1.5px solid #d1d5db; border-radius: 0.75rem;
            font-size: 1rem; outline: none; background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
        }
        .form-input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.15); }
        .dropdown-item { padding: 0.5rem 0.875rem; cursor: pointer; }
        .dropdown-item:hover { background: #f0fdf4; }
    </style>
</head>
<body>

<div x-data="checkinApp()" class="min-h-screen">

    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); padding: 1.5rem 1rem 3rem;">
        <div class="max-w-md mx-auto">
            <div class="flex items-center gap-3 mb-2">
                <img src="/storage/logo.png" alt="Dataraga" class="h-8 w-8 object-contain brightness-0 invert opacity-90">
                <span class="text-white/80 text-sm font-medium">Dataraga</span>
            </div>
            <h1 class="text-white text-xl font-bold leading-tight">{{ $kampung->nama_kampung }}</h1>
            <p class="text-green-100 text-sm mt-1">
                {{ collect([$kampung->desa, $kampung->kecamatan, $kampung->kabupaten])->filter()->implode(', ') }}
            </p>
            <div class="mt-3 inline-flex items-center gap-1.5 bg-white/20 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kampung Olahraga Terverifikasi
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="max-w-md mx-auto px-4" style="margin-top: -1.5rem;">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900 text-base">Daftar Aktivitas Olahraga</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data di bawah untuk tercatat sebagai peserta aktif.</p>
            </div>

            <form method="POST" action="{{ route('kampung.checkin.store', $kampung->qr_token) }}"
                  enctype="multipart/form-data" @submit.prevent="submitForm($event)" class="p-5 space-y-4">
                @csrf

                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_peserta" value="{{ old('nama_peserta') }}" placeholder="Masukkan nama lengkap Anda" required
                           autocomplete="name" class="form-input @error('nama_peserta') border-red-400 @enderror">
                </div>

                {{-- Umur --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Umur <span class="text-red-500">*</span></label>
                    <input type="number" name="umur" value="{{ old('umur') }}" placeholder="Umur Anda (tahun)" required
                           min="1" max="120" inputmode="numeric" class="form-input @error('umur') border-red-400 @enderror">
                </div>

                {{-- Jenis Olahraga: searchable dropdown --}}
                <div x-data="olahragaDropdown()" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Olahraga <span class="text-red-500">*</span></label>

                    <input type="hidden" name="jenis_olahraga_id" x-model="selectedId">
                    <input type="hidden" name="jenis_olahraga_baru" x-model="newName">

                    <div @click.away="open = false">
                        <input type="text" x-model="search" @focus="open = true" @input="filterList()"
                               placeholder="Cari jenis olahraga..." autocomplete="off"
                               class="form-input cursor-pointer"
                               :value="selectedLabel || search">

                        {{-- Dropdown --}}
                        <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden max-h-52 overflow-y-auto">

                            {{-- Existing options --}}
                            <template x-for="opt in filtered" :key="opt.id">
                                <div class="dropdown-item text-sm text-gray-700" @click="select(opt)">
                                    <span x-text="opt.nama"></span>
                                </div>
                            </template>

                            {{-- Add new if no match --}}
                            <template x-if="search.trim() && !filtered.length">
                                <div class="dropdown-item text-sm text-green-700 font-semibold" @click="addNew()">
                                    <span class="mr-1">+</span> Tambah "<span x-text="search.trim()"></span>"
                                </div>
                            </template>

                            <template x-if="!search.trim() && !filtered.length">
                                <div class="px-4 py-3 text-sm text-gray-400">Tidak ada pilihan</div>
                            </template>
                        </div>
                    </div>

                    <p x-show="newName" class="mt-1 text-xs text-green-600">
                        Akan ditambahkan: "<span x-text="newName" class="font-semibold"></span>" (olahraga baru)
                    </p>
                </div>

                {{-- Foto --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Aktivitas <span class="text-xs font-normal text-gray-400">(opsional, maks 200KB)</span></label>

                    <div x-data="photoUpload()" class="space-y-2">
                        <input type="file" name="foto" accept="image/*" capture="environment"
                               id="foto-input" class="hidden" @change="onFile($event)">

                        <template x-if="!preview">
                            <label for="foto-input" class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-xs text-gray-400">Klik untuk ambil foto</span>
                            </label>
                        </template>

                        <template x-if="preview">
                            <div class="relative">
                                <img :src="preview" class="w-full h-40 object-cover rounded-xl border border-gray-200">
                                <button type="button" @click="clearPhoto()" class="absolute top-2 right-2 bg-white rounded-full p-1 shadow text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <span x-show="sizeText" class="absolute bottom-2 left-2 text-[10px] bg-black/50 text-white px-2 py-0.5 rounded-full" x-text="sizeText"></span>
                            </div>
                        </template>

                        {{-- Hidden canvas for compression --}}
                        <canvas id="compressCanvas" class="hidden"></canvas>
                        {{-- Compressed file will be set via JS --}}
                        <input type="hidden" name="_compressed" value="1">
                    </div>
                </div>

                <button type="submit" :disabled="submitting"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition"
                        :class="submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 active:scale-[0.98]'">
                    <span x-show="!submitting">Kirim Check-in</span>
                    <span x-show="submitting" x-cloak>Mengirim...</span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4 pb-6">Data ini akan digunakan untuk program Kampung Olahraga Kemenpora RI</p>
    </div>
</div>

<script>
const allJenis = @json($jenisOlahraga);

function checkinApp() {
    return { submitting: false,
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
