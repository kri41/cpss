@props([
    'namePrefix' => '',
    'selectedProvinsi' => null,
    'selectedKabupaten' => null,
    'selectedKecamatan' => null,
    'selectedDesa' => null,
    'required' => false,
])

@php
    $prefix = $namePrefix ? $namePrefix . '_' : '';
    $requiredAttr = $required ? 'required' : '';
    $uniqueId = uniqid('wilayah_');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="{{ $uniqueId }}">
    <!-- Provinsi -->
    <div>
        <x-input-label :for="$uniqueId . '_provinsi'" :value="__('Provinsi')" />
        <select id="{{ $uniqueId }}_provinsi" name="{{ $prefix }}provinsi"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                {{ $requiredAttr }}
                data-selected="{{ $selectedProvinsi }}">
            <option value="">Pilih Provinsi</option>
        </select>
        <x-input-error :messages="$errors->get($prefix . 'provinsi')" class="mt-2" />
    </div>

    <!-- Kabupaten -->
    <div>
        <x-input-label :for="$uniqueId . '_kabupaten'" :value="__('Kabupaten')" />
        <select id="{{ $uniqueId }}_kabupaten" name="{{ $prefix }}kabupaten"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                {{ $requiredAttr }}
                disabled
                data-selected="{{ $selectedKabupaten }}">
            <option value="">Pilih Kabupaten</option>
        </select>
        <x-input-error :messages="$errors->get($prefix . 'kabupaten')" class="mt-2" />
    </div>

    <!-- Kecamatan -->
    <div>
        <x-input-label :for="$uniqueId . '_kecamatan'" :value="__('Kecamatan')" />
        <select id="{{ $uniqueId }}_kecamatan" name="{{ $prefix }}kecamatan"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                {{ $requiredAttr }}
                disabled
                data-selected="{{ $selectedKecamatan }}">
            <option value="">Pilih Kecamatan</option>
        </select>
        <x-input-error :messages="$errors->get($prefix . 'kecamatan')" class="mt-2" />
    </div>

    <!-- Desa -->
    <div>
        <x-input-label :for="$uniqueId . '_desa'" :value="__('Desa')" />
        <select id="{{ $uniqueId }}_desa" name="{{ $prefix }}desa"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                {{ $requiredAttr }}
                disabled
                data-selected="{{ $selectedDesa }}">
            <option value="">Pilih Desa</option>
        </select>
        <x-input-error :messages="$errors->get($prefix . 'desa')" class="mt-2" />
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('{{ $uniqueId }}');
        const provinsiSelect = document.getElementById('{{ $uniqueId }}_provinsi');
        const kabupatenSelect = document.getElementById('{{ $uniqueId }}_kabupaten');
        const kecamatanSelect = document.getElementById('{{ $uniqueId }}_kecamatan');
        const desaSelect = document.getElementById('{{ $uniqueId }}_desa');

        function resetSelect(select, placeholder) {
            select.innerHTML = '<option value="">' + placeholder + '</option>';
            select.disabled = true;
        }

        // useKode=true  → option.value = kode (provinsi, untuk peta)
        // useKode=false → option.value = nama (kabupaten/kecamatan/desa, untuk display)
        // data-kode selalu berisi kode BPS untuk keperluan cascade API
        function populateSelect(select, data, placeholder, selectedValue, useKode) {
            select.innerHTML = '<option value="">' + placeholder + '</option>';
            data.forEach(function(item) {
                const option = document.createElement('option');
                option.value = useKode ? item.kode : item.nama;
                option.dataset.kode = item.kode;
                option.textContent = item.nama;
                // Cocokkan terhadap kode DAN nama agar data lama (kode) & baru (nama) sama-sama bisa pre-select
                const matches = selectedValue && (
                    String(item.kode) === String(selectedValue) ||
                    String(item.nama) === String(selectedValue)
                );
                if (matches) option.selected = true;
                select.appendChild(option);
            });
            select.disabled = false;
        }

        // Ambil kode dari option terpilih (disimpan di data-kode)
        function selectedKode(select) {
            const opt = select.options[select.selectedIndex];
            return opt ? (opt.dataset.kode || '') : '';
        }

        function loadProvinces() {
            fetch('/api/provinces')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    // Provinsi: simpan kode sebagai value (dipakai peta)
                    populateSelect(provinsiSelect, data, 'Pilih Provinsi', provinsiSelect.dataset.selected, true);
                    if (provinsiSelect.dataset.selected) {
                        provinsiSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(function(err) { console.error('Gagal load provinsi:', err); });
        }

        provinsiSelect.addEventListener('change', function() {
            const kode = selectedKode(this) || this.value; // provinsi: value IS kode
            resetSelect(kabupatenSelect, 'Pilih Kabupaten');
            resetSelect(kecamatanSelect, 'Pilih Kecamatan');
            resetSelect(desaSelect, 'Pilih Desa');

            if (kode) {
                fetch('/api/kabupaten/' + kode)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        populateSelect(kabupatenSelect, data, 'Pilih Kabupaten', kabupatenSelect.dataset.selected, false);
                        if (kabupatenSelect.dataset.selected) {
                            kabupatenSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(function(err) { console.error('Gagal load kabupaten:', err); });
            }
        });

        kabupatenSelect.addEventListener('change', function() {
            const kode = selectedKode(this);
            resetSelect(kecamatanSelect, 'Pilih Kecamatan');
            resetSelect(desaSelect, 'Pilih Desa');

            if (kode) {
                fetch('/api/kecamatan/' + kode)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        populateSelect(kecamatanSelect, data, 'Pilih Kecamatan', kecamatanSelect.dataset.selected, false);
                        if (kecamatanSelect.dataset.selected) {
                            kecamatanSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(function(err) { console.error('Gagal load kecamatan:', err); });
            }
        });

        kecamatanSelect.addEventListener('change', function() {
            const kode = selectedKode(this);
            resetSelect(desaSelect, 'Pilih Desa');

            if (kode) {
                fetch('/api/desa/' + kode)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        populateSelect(desaSelect, data, 'Pilih Desa', desaSelect.dataset.selected, false);
                    })
                    .catch(function(err) { console.error('Gagal load desa:', err); });
            }
        });

        loadProvinces();
    })();
</script>
