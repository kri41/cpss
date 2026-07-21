<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ChangeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HandlesChangeRequests
{
    /**
     * Buat permintaan akses edit (bukan usulan nilai field) untuk data yang
     * sudah tervalidasi/bukan milik user ini. Kalau disetujui admin nanti,
     * yang mendapat akses edit kembali adalah PEMILIK ASLI data, bukan pengaju.
     * Pemilik baru diberi tahu SETELAH admin menyetujui, bukan saat pengajuan masuk.
     */
    protected function requestEditAccess(Model $model, string $type, Request $request): ChangeRequest
    {
        $request->validate(['alasan' => 'required|string|min:10']);

        return ChangeRequest::create([
            'user_id' => auth()->id(),
            'changeable_type' => $type,
            'changeable_id' => $model->id,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);
    }
}
