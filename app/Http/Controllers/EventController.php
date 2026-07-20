<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Event;
use App\Models\PointTransaction;
use App\Models\UserNotification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $isDashboard = request()->is('dashboard/*');
        $user = auth()->user();

        $query = Event::with('user')->latest();

        // Guest (publik) hanya lihat yang sudah divalidasi
        if (!auth()->check()) {
            $query->validated();
        }

        // Relawan di dashboard hanya lihat data di wilayahnya sendiri
        if ($isDashboard && $user?->isRelawan()) {
            $user->scopeToOwnWilayah($query);
        }

        // Filter: search nama
        if ($request->filled('search')) {
            $query->where('nama_event', 'like', '%' . $request->search . '%');
        }

        // Filter: kabupaten
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        // Filter: kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Filter: tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $events = $query->paginate(10)->withQueryString();

        // Data untuk dropdown filter
        $filterQuery = auth()->check() ? Event::query() : Event::validated();
        if ($isDashboard && $user?->isRelawan()) {
            $user->scopeToOwnWilayah($filterQuery);
        }
        $kabupatenList = (clone $filterQuery)->distinct()->orderBy('kabupaten')->pluck('kabupaten')->filter();
        $kecamatanList = (clone $filterQuery)->when($request->filled('kabupaten'), fn($q) => $q->where('kabupaten', $request->kabupaten))->distinct()->orderBy('kecamatan')->pluck('kecamatan')->filter();
        $tingkatList = ['Desa/Kelurahan', 'Kecamatan', 'Kabupaten/Kota'];

        $view = $isDashboard ? 'events.index-dashboard' : 'events.index';
        return view($view, compact('events', 'kabupatenList', 'kecamatanList', 'tingkatList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'tingkat' => 'required|in:Desa/Kelurahan,Kecamatan,Kabupaten/Kota',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi_kegiatan' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:20',
        ]);

        $validated['user_id'] = auth()->id();

        $event = Event::create($validated);

        // Audit Log
        AuditLogger::logCreate('events', $event->id, $validated);

        return redirect()->route('dashboard.events')
            ->with('success', 'Data event berhasil ditambahkan. Menunggu validasi admin untuk kredit poin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): View
    {
        // Resolve kode BPS → nama wilayah (satu query untuk semua level)
        $kodes = array_filter([
            $event->provinsi,
            $event->kabupaten,
            $event->kecamatan,
            $event->desa,
        ]);

        $wilayahNama = $kodes
            ? DB::table('wilayah')->whereIn('kode', $kodes)->pluck('nama', 'kode')
            : collect();

        return view('events.show', compact('event', 'wilayahNama'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event): View
    {
        if (!auth()->user()->canEdit($event)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit event ini.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        if (!auth()->user()->canEdit($event)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit event ini.');
        }

        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'tingkat' => 'required|in:Desa/Kelurahan,Kecamatan,Kabupaten/Kota',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi_kegiatan' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:20',
        ]);

        // Simpan data lama untuk audit log
        $oldData = $event->toArray();

        $event->update($validated);

        // Audit Log
        AuditLogger::logUpdate('events', $event->id, $oldData, $event->fresh()->toArray());

        return redirect()->route('dashboard.events')
            ->with('success', 'Data event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        if (!auth()->user()->canEdit($event)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus event ini.');
        }

        // Simpan data untuk audit log
        $oldData = $event->toArray();

        $event->delete();

        // Audit Log
        AuditLogger::logDelete('events', $event->id, $oldData);

        return redirect()->route('dashboard.events')
            ->with('success', 'Data event berhasil dihapus.');
    }

    /**
     * Validate the specified event.
     */
    public function validateEvent(Request $request, Event $event): RedirectResponse
    {
        if (!auth()->user()->canValidate($event)) {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi event ini.');
        }

        $event->update([
            'status_validasi' => 'validated',
            'komentar_validasi' => $request->input('komentar_validasi'),
        ]);

        // Gamification: berikan poin saat validasi (event tidak dibatasi)
        $tx = GamificationService::awardPoints(
            $event->user_id,
            'event_baru',
            'event',
            $event->id
        );

        $msg = 'Data event berhasil divalidasi.';
        if ($tx) {
            $msg .= ' +' . $tx->poin . ' poin diberikan ke relawan.';

            UserNotification::create([
                'user_id' => $event->user_id,
                'type' => 'poin',
                'title' => '+' . $tx->poin . ' Poin Diterima',
                'message' => 'Event "' . $event->nama_event . '" telah divalidasi. Anda mendapatkan ' . $tx->poin . ' poin.',
                'data' => ['related_type' => 'event', 'related_id' => $event->id, 'poin' => $tx->poin],
            ]);
        }

        $redirect = redirect()->route('dashboard.events')->with('success', $msg);
        if ($tx) {
            $redirect->with('poin_diperoleh', ['poin' => $tx->poin, 'label' => 'Event "' . $event->nama_event . '" divalidasi']);
        }
        return $redirect;
    }

    /**
     * Cancel validation (super admin only).
     */
    public function cancelValidateEvent(Event $event): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat membatalkan validasi.');
        }

        $event->update([
            'status_validasi' => 'pending',
            'komentar_validasi' => null,
        ]);

        $tx = PointTransaction::where('related_type', 'event')
            ->where('related_id', $event->id)
            ->where('status', 'valid')
            ->first();

        if ($tx) {
            GamificationService::batalkanPoin($tx->id, auth()->id(), 'Validasi dibatalkan oleh Super Admin');
        }

        return redirect()->route('dashboard.events')
            ->with('success', 'Validasi event dibatalkan. Poin relawan telah ditarik.');
    }

    /**
     * Peta choropleth distribusi event per provinsi.
     */
    public function peta(Request $request): \Illuminate\View\View
    {
        // Jumlah event tervalidasi per kode provinsi
        $provinsiCounts = Event::validated()
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '')
            ->groupBy('provinsi')
            ->selectRaw('provinsi as kode, count(*) as total')
            ->pluck('total', 'kode');   // ['35' => 5, '33' => 2]

        $maxCount = $provinsiCounts->max() ?: 1;

        // Bila provinsi dipilih, ambil event-nya
        $selectedKode     = $request->get('provinsi');
        $selectedNama     = $request->get('nama', '');
        $selectedEvents   = collect();

        if ($selectedKode) {
            $selectedEvents = Event::validated()
                ->where('provinsi', $selectedKode)
                ->latest('tanggal_mulai')
                ->get();
        }

        $isDashboard = request()->is('dashboard/*');
        $layout      = $isDashboard ? 'layouts.app' : 'layouts.public';

        return view('events.peta', compact(
            'provinsiCounts', 'maxCount',
            'selectedKode', 'selectedNama', 'selectedEvents',
            'isDashboard', 'layout'
        ));
    }
}
