<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class DownloadGeoJson extends Command
{
    protected $signature   = 'geojson:download';
    protected $description = 'Download dan simpan GeoJSON peta provinsi Indonesia ke storage lokal';

    private const CACHE_KEY = 'geojson/indonesia-provinces.json';

    private const SOURCES = [
        'https://raw.githubusercontent.com/superpikar/indonesia-geojson/master/indonesia.geojson',
        'https://raw.githubusercontent.com/ans-4175/peta-indonesia-geojson/master/indonesia.geojson',
        'https://raw.githubusercontent.com/Vizzuality/growasia_calculator/master/public/indonesia.geojson',
        'https://raw.githubusercontent.com/gadis-net/indonesia-geojson/main/indonesia.geojson',
    ];

    public function handle(): int
    {
        if (Storage::disk('local')->exists(self::CACHE_KEY)) {
            if (!$this->confirm('File GeoJSON sudah ada. Timpa?', false)) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        foreach (self::SOURCES as $url) {
            $this->line("Mencoba: <comment>$url</comment>");
            try {
                $response = Http::timeout(30)->get($url);
                if ($response->successful()) {
                    $body = $response->body();
                    $data = json_decode($body, true);
                    if (isset($data['type'], $data['features'])) {
                        Storage::disk('local')->put(self::CACHE_KEY, $body);
                        $kb = round(strlen($body) / 1024);
                        $this->info("✓ Berhasil ({$kb} KB, " . count($data['features']) . " features)");
                        return 0;
                    }
                    $this->warn("  → Respons tidak valid (bukan FeatureCollection)");
                } else {
                    $this->warn("  → HTTP " . $response->status());
                }
            } catch (\Throwable $e) {
                $this->warn("  → Error: " . $e->getMessage());
            }
        }

        $this->error('Semua sumber gagal. Coba download manual dari browser dan simpan ke:');
        $this->line('  ' . storage_path('app/' . self::CACHE_KEY));
        return 1;
    }
}
