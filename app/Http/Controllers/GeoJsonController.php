<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class GeoJsonController extends Controller
{
    private const CACHE_KEY  = 'geojson/indonesia-provinces.json';
    private const CACHE_DISK = 'local';

    // Sumber yang dicoba berurutan sampai ada yang berhasil
    private const SOURCES = [
        'https://raw.githubusercontent.com/superpikar/indonesia-geojson/master/indonesia.geojson',
        'https://raw.githubusercontent.com/ans-4175/peta-indonesia-geojson/master/indonesia.geojson',
        'https://raw.githubusercontent.com/Vizzuality/growasia_calculator/master/public/indonesia.geojson',
    ];

    public function provinces()
    {
        // Sajikan dari cache lokal kalau sudah ada
        if (Storage::disk(self::CACHE_DISK)->exists(self::CACHE_KEY)) {
            $json = Storage::disk(self::CACHE_DISK)->get(self::CACHE_KEY);
            return response($json, 200)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'public, max-age=86400');
        }

        // Download dari sumber eksternal
        $json = $this->downloadFromSources();

        if ($json === null) {
            return response()->json([
                'error' => 'GeoJSON tidak tersedia. Jalankan: php artisan geojson:download'
            ], 503);
        }

        // Simpan cache
        Storage::disk(self::CACHE_DISK)->put(self::CACHE_KEY, $json);

        return response($json, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    private function downloadFromSources(): ?string
    {
        foreach (self::SOURCES as $url) {
            try {
                $response = Http::timeout(15)->get($url);
                if ($response->successful()) {
                    $body = $response->body();
                    // Validasi minimal: harus JSON valid dengan FeatureCollection
                    $data = json_decode($body, true);
                    if (isset($data['type']) && isset($data['features'])) {
                        return $body;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return null;
    }
}
