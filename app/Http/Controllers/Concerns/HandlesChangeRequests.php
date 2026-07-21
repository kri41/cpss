<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ChangeRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HandlesChangeRequests
{
    /**
     * Bandingkan data tervalidasi dengan nilai model saat ini, simpan hanya
     * field yang benar-benar berubah sebagai usulan (pending), untuk ditinjau admin.
     * Return null kalau tidak ada perubahan sama sekali.
     */
    protected function proposeChange(Model $model, string $type, array $validated, Request $request): ?ChangeRequest
    {
        $request->validate(['alasan' => 'required|string|min:10']);

        $changed = [];
        foreach ($validated as $key => $value) {
            $current = $model->{$key};

            if ($current instanceof \DateTimeInterface) {
                $current = Carbon::parse($current)->format('Y-m-d');
                $value = $value ? Carbon::parse($value)->format('Y-m-d') : null;
            } elseif (is_bool($current) || is_bool($value)) {
                $current = (bool) $current;
                $value = (bool) $value;
            }

            if ((string) $current !== (string) $value) {
                $changed[$key] = $value;
            }
        }

        if (empty($changed)) {
            return null;
        }

        return ChangeRequest::create([
            'user_id' => auth()->id(),
            'changeable_type' => $type,
            'changeable_id' => $model->id,
            'perubahan' => $changed,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);
    }
}
