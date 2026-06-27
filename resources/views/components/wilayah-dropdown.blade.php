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

        function populateSelect(select, data, placeholder, selectedValue) {
            select.innerHTML = '<option value="">' + placeholder + '</option>';
            data.forEach(function(item) {
                const option = document.createElement('option');
                option.value = item.kode;
                option.textContent = item.nama;
                if (selectedValue && String(item.id) === String(selectedValue)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            select.disabled = false;
        }

        function loadProvinces() {
            fetch('/api/provinces')
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    populateSelect(provinsiSelect, data, 'Pilih Provinsi', provinsiSelect.dataset.selected);
                    if (provinsiSelect.dataset.selected) {
                        provinsiSelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(function(err) { console.error('Failed to load provinces:', err); });
        }

        provinsiSelect.addEventListener('change', function() {
            const provinceId = this.value;
            resetSelect(kabupatenSelect, 'Pilih Kabupaten');
            resetSelect(kecamatanSelect, 'Pilih Kecamatan');
            resetSelect(desaSelect, 'Pilih Desa');

            if (provinceId) {
                fetch('/api/kabupaten/' + provinceId)
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        populateSelect(kabupatenSelect, data, 'Pilih Kabupaten', kabupatenSelect.dataset.selected);
                        if (kabupatenSelect.dataset.selected) {
                            kabupatenSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(function(err) { console.error('Failed to load kabupaten:', err); });
            }
        });

        kabupatenSelect.addEventListener('change', function() {
            const regencyId = this.value;
            resetSelect(kecamatanSelect, 'Pilih Kecamatan');
            resetSelect(desaSelect, 'Pilih Desa');

            if (regencyId) {
                fetch('/api/kecamatan/' + regencyId)
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        populateSelect(kecamatanSelect, data, 'Pilih Kecamatan', kecamatanSelect.dataset.selected);
                        if (kecamatanSelect.dataset.selected) {
                            kecamatanSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(function(err) { console.error('Failed to load kecamatan:', err); });
            }
        });

        kecamatanSelect.addEventListener('change', function() {
            const districtId = this.value;
            resetSelect(desaSelect, 'Pilih Desa');

            if (districtId) {
                fetch('/api/desa/' + districtId)
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        populateSelect(desaSelect, data, 'Pilih Desa', desaSelect.dataset.selected);
                    })
                    .catch(function(err) { console.error('Failed to load desa:', err); });
            }
        });

        loadProvinces();
    })();
</script>
