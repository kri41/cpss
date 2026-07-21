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

        $changeRequests = ChangeRequest::with(['user', 'changeable'])
            ->pending()
            ->latest()
            ->paginate(15);

        return view('change-requests.index', compact('changeRequests'));
    }

    public function approve(ChangeRequest $changeRequest): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_unless($changeRequest->status === 'pending', 404);

        $model = $changeRequest->changeable;
        abort_if(!$model, 404, 'Data terkait sudah tidak ada.');

        $model->update($changeRequest->perubahan);

        $changeRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $changeRequest->user_id,
            'type' => 'perubahan',
            'title' => 'Usulan Perubahan Diterima',
            'message' => 'Usulan perubahan Anda untuk "' . $this->itemName($model) . '" telah disetujui admin.',
            'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
        ]);

        if ($model->user_id && $model->user_id !== $changeRequest->user_id) {
            UserNotification::create([
                'user_id' => $model->user_id,
                'type' => 'perubahan',
                'title' => 'Data Anda Diperbarui',
                'message' => '"' . $this->itemName($model) . '" milik Anda diperbarui berdasarkan usulan perubahan yang disetujui admin.',
                'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
            ]);
        }

        return back()->with('success', 'Usulan perubahan disetujui dan data telah diperbarui.');
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
            'title' => 'Usulan Perubahan Ditolak',
            'message' => 'Usulan perubahan Anda untuk "' . $this->itemName($changeRequest->changeable) . '" ditolak admin. Alasan: ' . $request->catatan_admin,
            'data' => ['related_type' => $changeRequest->changeable_type, 'related_id' => $changeRequest->changeable_id],
        ]);

        return back()->with('success', 'Usulan perubahan ditolak.');
    }

    private function itemName($model): string
    {
        if (!$model) {
            return '-';
        }

        return $model->nama_fasilitas ?? $model->nama_club ?? $model->nama_event ?? ('#' . $model->id);
    }
}
