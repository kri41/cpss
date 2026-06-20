<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AuditLogger;
use App\Models\Event;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $events = Event::with('user')
            ->latest()
            ->paginate(10);
        
        return view('events.index', compact('events'));
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
        ]);

        $validated['user_id'] = auth()->id();

        $event = Event::create($validated);

        // Audit Log
        AuditLogger::logCreate('events', $event->id, $validated);

        // Gamification: Event Baru
        GamificationService::awardPoints(
            auth()->id(),
            'event_baru',
            'event',
            $event->id
        );

        return redirect()->route('events.index')
            ->with('success', 'Data event berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): View
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
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
        ]);

        // Simpan data lama untuk audit log
        $oldData = $event->toArray();

        $event->update($validated);

        // Audit Log
        AuditLogger::logUpdate('events', $event->id, $oldData, $event->fresh()->toArray());

        return redirect()->route('events.index')
            ->with('success', 'Data event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        // Simpan data untuk audit log
        $oldData = $event->toArray();

        $event->delete();

        // Audit Log
        AuditLogger::logDelete('events', $event->id, $oldData);

        return redirect()->route('events.index')
            ->with('success', 'Data event berhasil dihapus.');
    }
}
