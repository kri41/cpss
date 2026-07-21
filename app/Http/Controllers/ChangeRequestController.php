<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $changeRequests = ChangeRequest::with(['user', 'changeable.user'])
            ->pending()
            ->latest()
            ->paginate(15);

        return view('change-requests.index', compact('changeRequests'));
    }

    /**
     * Setujui permintaan — buka kembali akses edit untuk PEMILIK ASLI data
     * (bukan pengaju) dengan mengembalikan status_validasi ke pending, supaya
     * aturan canEdit() yang sudah ada otomatis mengizinkan pemilik edit lagi.
     * Admin perlu memvalidasi ulang setelah pemilik selesai memperbaiki.
     */
    public function approve(ChangeRequest $changeRequest): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_unless($changeRequest->status === 'pending', 404);

        $model = $changeRequest->changeable;
        abort_if(!$model, 404, 'Data terkait sudah tidak ada.');

        $model->update(['status_validasi' => 'pending']);

        $changeRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        if ($model->user_id) {
            UserNotification::create([
                'user_id' => $model->user_id,
                'type' => 'perubahan',
                'title' => 'Akses Edit Dibuka Kembali',
                'message' => 'Admin membuka kembali akses edit untuk "' . $this->itemName($model) . '" karena ada laporan: "' . $changeRequest->alasan . '". Data perlu divalidasi ulang setelah Anda selesai mengedit.',
                'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
            ]);
        }

        if ($changeRequest->user_id !== $model->user_id) {
            UserNotification::create([
                'user_id' => $changeRequest->user_id,
                'type' => 'perubahan',
                'title' => 'Permintaan Diterima',
                'message' => 'Permintaan Anda tentang "' . $this->itemName($model) . '" disetujui. Pemilik data akan memperbaikinya.',
                'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
            ]);
        }

        return back()->with('success', 'Permintaan disetujui — akses edit pemilik data dibuka kembali (status kembali pending, menunggu validasi ulang).');
    }

    public function reject(Request $request, ChangeRequest $changeRequest): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_unless($changeRequest->status === 'pending', 404);

        $request->validate(['catatan_admin' => 'required|string|min:5']);

        $changeRequest->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $changeRequest->user_id,
            'type' => 'perubahan',
            'title' => 'Permintaan Ditolak',
            'message' => 'Permintaan akses edit Anda untuk "' . $this->itemName($changeRequest->changeable) . '" ditolak admin. Alasan: ' . $request->catatan_admin,
            'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
        ]);

        return back()->with('success', 'Permintaan ditolak.');
    }

    private function itemName($model): string
    {
        if (!$model) {
            return '-';
        }

        return $model->nama_fasilitas ?? $model->nama_club ?? $model->nama_event ?? ('#' . $model->id);
    }
}
