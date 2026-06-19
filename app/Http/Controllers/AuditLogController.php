<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        // Filter berdasarkan aksi
        if ($request->has('action') && $request->action) {
            $query->action($request->action);
        }

        // Filter berdasarkan tabel
        if ($request->has('table') && $request->table) {
            $query->targetTable($request->table);
        }

        // Filter berdasarkan user
        if ($request->has('user_id') && $request->user_id) {
            $query->byUser($request->user_id);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        return view('audit-logs.index', compact('auditLogs'));
    }
}
