<?php

namespace App\Http\Controllers;

use App\Models\CheckinKampung;
use App\Models\JenisOlahraga;
use App\Models\KampungOlahraga;
use App\Models\KomponenSyarat;
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

        $kampung->load(['user', 'checkins.jenisOlahraga']);

        $komponenList  = KomponenSyarat::where('aktif', true)->orderBy('urutan')->get();
        $totalCheckin  = $kampung->checkins()->count();
        $recentCheckins = $kampung->checkins()->with('jenisOlahraga')->latest()->limit(20)->get();

        $qrSvg  = null;
        $qrUrl  = null;
        if ($kampung->status_validasi === 'validated' && $kampung->qr_token) {
            $qrUrl = route('kampung.checkin.form', $kampung->qr_token);
            $qrSvg = (string) QrCode::size(220)->margin(1)->generate($qrUrl);
        }

        return view('kampung.show', compact(
            'kampung', 'komponenList', 'totalCheckin', 'recentCheckins', 'qrSvg', 'qrUrl'
        ));
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
            'qr_token'        => $kampung->qr_token ?? KampungOlahraga::generateQrToken(),
            'catatan_admin'   => null,
        ]);

        return back()->with('success', 'Kampung Olahraga berhasil diverifikasi. QR Code sudah aktif.');
    }

    public function reject(Request $request, KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $kampung->update([
            'status_validasi' => 'rejected',
            'catatan_admin'   => $request->catatan_admin,
        ]);

        return back()->with('success', 'Kampung Olahraga telah ditolak.');
    }

    public function cancelValidate(KampungOlahraga $kampung): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $kampung->update(['status_validasi' => 'pending']);
        return back()->with('success', 'Verifikasi dibatalkan, status kembali ke pending.');
    }

    // ── PUBLIC CHECK-IN (via QR) ──────────────────────────────

    public function checkinForm(string $token): View
    {
        $kampung = KampungOlahraga::where('qr_token', $token)
            ->where('status_validasi', 'validated')
            ->firstOrFail();

        $jenisOlahraga = JenisOlahraga::where('aktif', true)->orderBy('nama')->get();

        return view('kampung.checkin', compact('kampung', 'jenisOlahraga'));
    }

    public function checkinStore(Request $request, string $token): RedirectResponse
    {
        $kampung = KampungOlahraga::where('qr_token', $token)
            ->where('status_validasi', 'validated')
            ->firstOrFail();

        $request->validate([
            'nama_peserta'       => 'required|string|max:255',
            'umur'               => 'required|integer|min:1|max:120',
            'jenis_olahraga_id'  => 'nullable|integer|exists:jenis_olahraga,id',
            'jenis_olahraga_baru'=> 'nullable|string|max:100',
            'foto'               => 'nullable|image|max:10240',
        ]);

        // Resolve jenis olahraga
        $jenisId   = $request->jenis_olahraga_id;
        $jenisnama = null;

        if (!$jenisId && filled($request->jenis_olahraga_baru)) {
            $nama    = trim($request->jenis_olahraga_baru);
            $jenis   = JenisOlahraga::firstOrCreate(['nama' => $nama], ['aktif' => true]);
            $jenisId = $jenis->id;
            $jenisnama = $jenis->nama;
        } elseif ($jenisId) {
            $jenisnama = JenisOlahraga::find($jenisId)?->nama;
        }

        // Handle photo
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $this->compressAndStore($request->file('foto'));
        }

        CheckinKampung::create([
            'kampung_olahraga_id' => $kampung->id,
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
        $kampung = KampungOlahraga::where('qr_token', $token)
            ->where('status_validasi', 'validated')
            ->firstOrFail();

        return view('kampung.checkin-sukses', compact('kampung'));
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
