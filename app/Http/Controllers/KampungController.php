<?php

namespace App\Http\Controllers;

use App\Models\CheckinKampung;
use App\Models\Club;
use App\Models\JenisOlahraga;
use App\Models\KampungOlahraga;
use App\Models\KomponenSyarat;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\UserNotification;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KampungController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $query = KampungOlahraga::with('user')->latest();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $kampungList  = $query->paginate(15);
        $totalPending = KampungOlahraga::where('status_validasi', 'pending')->count();

        return view('kampung.index', compact('kampungList', 'totalPending'));
    }

    public function create(): View
    {
        return view('kampung.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kampung' => 'required|string|max:255',
            'alamat'       => 'nullable|string',
            'provinsi'     => 'nullable|string|max:255',
            'kabupaten'    => 'nullable|string|max:255',
            'kecamatan'    => 'nullable|string|max:255',
            'desa'         => 'nullable|string|max:255',
            'rt'           => 'nullable|string|max:5',
            'rw'           => 'nullable|string|max:5',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
        ]);

        $validated['user_id'] = auth()->id();
        KampungOlahraga::create($validated);

        return redirect()->route('kampung.index')
            ->with('success', 'Kampung Olahraga berhasil didaftarkan. Menunggu verifikasi admin.');
    }

    public function show(KampungOlahraga $kampung): View
    {
        $this->authorizeAccess($kampung);

        $kampung->load(['user', 'checkins.jenisOlahraga', 'fasil', 'klubKomunitas']);

        $komponenList  = KomponenSyarat::where('aktif', true)->orderBy('urutan')->get();
        $totalCheckin  = $kampung->checkins()->count();
        $recentCheckins = $kampung->checkins()->with('jenisOlahraga')->latest()->limit(20)->get();

        // QR per-fasil (bukan lagi per-kampung)
        $fasilQr = [];
        if ($kampung->status_validasi === 'validated') {
            foreach ($kampung->fasil as $fasil) {
                if ($fasil->qr_token) {
                    $url = route('kampung.checkin.form', $fasil->qr_token);
                    $fasilQr[$fasil->id] = [
                        'url' => $url,
                        'svg' => (string) QrCode::size(180)->margin(1)->generate($url),
                    ];
                }
            }
        }

        // Kandidat fasil & klub/komunitas yang bisa didaftarkan ke kampung ini (se-wilayah, belum terdaftar)
        $candidateFasil = collect();
        $candidateKlub  = collect();
        if ($this->canManageRegistrations($kampung)) {
            $candidateFasil = Prasarana::validated()
                ->sameWilayahAs($kampung)
                ->whereNull('kampung_olahraga_id')
                ->orderBy('nama_fasilitas')
                ->get();

            $attachedKlubIds = $kampung->klubKomunitas->pluck('id');
            $candidateKlub = Club::validated()->aktif()
                ->sameWilayahAs($kampung)
                ->whereNotIn('id', $attachedKlubIds)
                ->orderBy('nama_club')
                ->get();
        }

        return view('kampung.show', compact(
            'kampung', 'komponenList', 'totalCheckin', 'recentCheckins', 'fasilQr',
            'candidateFasil', 'candidateKlub'
        ));
    }

    // ── FASIL & KLUB/KOMUNITAS REGISTRATION ─────────────────────

    public function attachFasil(Request $request, KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless($this->canManageRegistrations($kampung), 403);

        $request->validate(['prasarana_id' => 'required|exists:prasarana,id']);

        $prasarana = Prasarana::validated()
            ->sameWilayahAs($kampung)
            ->whereNull('kampung_olahraga_id')
            ->findOrFail($request->prasarana_id);

        $prasarana->update([
            'kampung_olahraga_id' => $kampung->id,
            'qr_token' => $prasarana->qr_token ?? Prasarana::generateQrToken(),
        ]);

        return back()->with('success', 'Fasil "' . $prasarana->nama_fasilitas . '" berhasil didaftarkan ke kampung ini.');
    }

    public function detachFasil(KampungOlahraga $kampung, Prasarana $prasarana): RedirectResponse
    {
        abort_unless($this->canManageRegistrations($kampung), 403);
        abort_unless($prasarana->kampung_olahraga_id === $kampung->id, 404);

        $prasarana->update(['kampung_olahraga_id' => null, 'qr_token' => null]);

        return back()->with('success', 'Fasil "' . $prasarana->nama_fasilitas . '" dilepas dari kampung ini. QR-nya dinonaktifkan.');
    }

    public function attachKlub(Request $request, KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless($this->canManageRegistrations($kampung), 403);

        $request->validate(['club_id' => 'required|exists:clubs,id']);

        $club = Club::validated()->aktif()
            ->sameWilayahAs($kampung)
            ->findOrFail($request->club_id);

        $kampung->klubKomunitas()->syncWithoutDetaching([$club->id]);

        return back()->with('success', 'Klub/Komunitas "' . $club->nama_club . '" berhasil didaftarkan ke kampung ini.');
    }

    public function detachKlub(KampungOlahraga $kampung, Club $club): RedirectResponse
    {
        abort_unless($this->canManageRegistrations($kampung), 403);

        $kampung->klubKomunitas()->detach($club->id);

        return back()->with('success', 'Klub/Komunitas "' . $club->nama_club . '" dilepas dari kampung ini.');
    }

    public function edit(KampungOlahraga $kampung): View
    {
        $this->authorizeAccess($kampung);
        return view('kampung.edit', compact('kampung'));
    }

    public function update(Request $request, KampungOlahraga $kampung): RedirectResponse
    {
        $this->authorizeAccess($kampung);

        $validated = $request->validate([
            'nama_kampung' => 'required|string|max:255',
            'alamat'       => 'nullable|string',
            'provinsi'     => 'nullable|string|max:255',
            'kabupaten'    => 'nullable|string|max:255',
            'kecamatan'    => 'nullable|string|max:255',
            'desa'         => 'nullable|string|max:255',
            'rt'           => 'nullable|string|max:5',
            'rw'           => 'nullable|string|max:5',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
        ]);

        $kampung->update($validated);

        return redirect()->route('kampung.show', $kampung)
            ->with('success', 'Data Kampung Olahraga berhasil diperbarui.');
    }

    public function destroy(KampungOlahraga $kampung): RedirectResponse
    {
        $this->authorizeAccess($kampung);
        $kampung->delete();
        return redirect()->route('kampung.index')
            ->with('success', 'Kampung Olahraga berhasil dihapus.');
    }

    public function validate(KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $kampung->update([
            'status_validasi' => 'validated',
            'catatan_admin'   => null,
        ]);

        $kode = GamificationService::resolveKodeAktivitas('kampung_baru', $kampung->user_id, 'kampung_olahraga', $kampung->id);
        $tx = GamificationService::awardPoints($kampung->user_id, $kode, 'kampung_olahraga', $kampung->id);

        $msg = 'Kampung Olahraga berhasil diverifikasi. QR fasil yang terdaftar kini aktif.';
        if ($tx) {
            $msg .= ' +' . $tx->poin . ' poin diberikan ke relawan.';
        }

        return back()->with('success', $msg);
    }

    public function reject(Request $request, KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate(['catatan_admin' => 'required|string|min:5']);

        $kampung->update([
            'status_validasi' => 'rejected',
            'catatan_admin'   => $request->catatan_admin,
        ]);

        if ($kampung->user_id) {
            UserNotification::create([
                'user_id' => $kampung->user_id,
                'type' => 'validasi',
                'title' => 'Kampung Olahraga Butuh Perbaikan',
                'message' => 'Kampung Olahraga "' . $kampung->nama_kampung . '" perlu diperbaiki. Catatan admin: ' . $request->catatan_admin,
                'data' => ['related_type' => 'kampung_olahraga', 'related_id' => $kampung->id],
            ]);
        }

        return back()->with('success', 'Kampung Olahraga ditandai butuh perbaikan. Relawan pelapor telah diberi tahu.');
    }

    public function cancelValidate(KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $kampung->update(['status_validasi' => 'pending']);

        $tx = PointTransaction::where('related_type', 'kampung_olahraga')
            ->where('related_id', $kampung->id)
            ->where('status', 'valid')
            ->first();

        if ($tx) {
            GamificationService::batalkanPoin($tx->id, auth()->id(), 'Verifikasi Kampung Olahraga dibatalkan');
        }

        return back()->with('success', 'Verifikasi dibatalkan, status kembali ke pending. Poin relawan telah ditarik.');
    }

    // ── PUBLIC CHECK-IN (via QR) ──────────────────────────────

    public function checkinForm(string $token): View
    {
        $fasil = $this->resolveFasilByToken($token);
        $kampung = $fasil->kampungOlahraga;

        $klubList = $kampung->klubKomunitas()->where('aktif', true)->with('jenisOlahraga')->orderBy('nama_club')->get();
        $jenisOlahraga = JenisOlahraga::where('aktif', true)->orderBy('nama')->get();

        return view('kampung.checkin', compact('fasil', 'kampung', 'klubList', 'jenisOlahraga'));
    }

    public function checkinStore(Request $request, string $token): RedirectResponse
    {
        $fasil = $this->resolveFasilByToken($token);
        $kampung = $fasil->kampungOlahraga;

        $request->validate([
            'nama_peserta'        => 'required|string|max:255',
            'umur'                => 'required|integer|min:1|max:120',
            'club_id'             => 'nullable|integer|exists:clubs,id',
            'jenis_olahraga_id'   => 'nullable|integer|exists:jenis_olahraga,id',
            'jenis_olahraga_baru' => 'nullable|string|max:100',
            'foto'                => 'nullable|image|max:10240',
        ]);

        $club = null;
        if ($request->filled('club_id')) {
            $club = $kampung->klubKomunitas()->where('clubs.id', $request->club_id)->first();
            abort_unless($club, 422, 'Klub/komunitas tidak terdaftar di kampung ini.');
        }

        // Resolve jenis olahraga: auto dari klub jika dipilih, manual jika "Belum bergabung"
        $jenisId   = null;
        $jenisnama = null;

        if ($club) {
            $jenisId   = $club->jenis_olahraga_id;
            $jenisnama = $club->jenisOlahraga?->nama;
        } elseif (filled($request->jenis_olahraga_id)) {
            $jenisId   = $request->jenis_olahraga_id;
            $jenisnama = JenisOlahraga::find($jenisId)?->nama;
        } elseif (filled($request->jenis_olahraga_baru)) {
            $nama      = trim($request->jenis_olahraga_baru);
            $jenis     = JenisOlahraga::firstOrCreate(['nama' => $nama], ['aktif' => true]);
            $jenisId   = $jenis->id;
            $jenisnama = $jenis->nama;
        }

        // Handle photo
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $this->compressAndStore($request->file('foto'));
        }

        CheckinKampung::create([
            'kampung_olahraga_id' => $kampung->id,
            'prasarana_id'        => $fasil->id,
            'club_id'             => $club?->id,
            'nama_peserta'        => $request->nama_peserta,
            'umur'                => $request->umur,
            'jenis_olahraga_id'   => $jenisId,
            'jenis_olahraga_nama' => $jenisnama,
            'foto'                => $fotoPath,
        ]);

        return redirect()->route('kampung.checkin.sukses', $token);
    }

    public function checkinSukses(string $token): View
    {
        $fasil = $this->resolveFasilByToken($token);
        $kampung = $fasil->kampungOlahraga;

        return view('kampung.checkin-sukses', compact('fasil', 'kampung'));
    }

    private function resolveFasilByToken(string $token): Prasarana
    {
        $fasil = Prasarana::where('qr_token', $token)
            ->whereNotNull('kampung_olahraga_id')
            ->firstOrFail();

        abort_unless($fasil->kampungOlahraga?->status_validasi === 'validated', 404);

        return $fasil;
    }

    public function apiJenisOlahraga(Request $request): JsonResponse
    {
        $results = JenisOlahraga::where('aktif', true)
            ->when($request->q, fn($q) => $q->where('nama', 'like', '%' . $request->q . '%'))
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama']);

        return response()->json($results);
    }

    // ── HELPERS ────────────────────────────────────────────────

    private function authorizeAccess(KampungOlahraga $kampung): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $kampung->user_id !== $user->id) {
            abort(403);
        }
    }

    private function canManageRegistrations(KampungOlahraga $kampung): bool
    {
        $user = auth()->user();
        return $user->isAdmin() || $kampung->user_id === $user->id;
    }

    private function compressAndStore($file): string
    {
        $dir = storage_path('app/public/checkin_kampung/');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = 'checkin_' . uniqid() . '.jpg';
        $dest     = $dir . $filename;

        $src = match ($ext) {
            'png'  => @imagecreatefrompng($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            'gif'  => @imagecreatefromgif($file->getRealPath()),
            default=> @imagecreatefromjpeg($file->getRealPath()),
        };

        if (!$src) {
            $file->storeAs('public/checkin_kampung', $filename);
            return 'checkin_kampung/' . $filename;
        }

        // Resize if too large (max 1024px on any side)
        [$origW, $origH] = [imagesx($src), imagesy($src)];
        $maxDim = 1024;
        if ($origW > $maxDim || $origH > $maxDim) {
            $ratio  = min($maxDim / $origW, $maxDim / $origH);
            $newW   = (int) ($origW * $ratio);
            $newH   = (int) ($origH * $ratio);
            $canvas = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $canvas;
        }

        // Binary-search quality to get under 200 KB
        $maxBytes = 200 * 1024;
        $quality  = 75;
        for ($q = 85; $q >= 20; $q -= 5) {
            ob_start();
            imagejpeg($src, null, $q);
            $data = ob_get_clean();
            if (strlen($data) <= $maxBytes) {
                $quality = $q;
                break;
            }
        }

        imagejpeg($src, $dest, $quality);
        imagedestroy($src);

        return 'checkin_kampung/' . $filename;
    }
}
