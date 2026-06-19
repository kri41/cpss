<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Log audit untuk operasi CREATE, UPDATE, DELETE
     */
    public static function log(
        string $action,
        string $targetTable,
        string $targetId,
        ?array $oldValue = null,
        ?array $newValue = null
    ): void {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'target_table' => $targetTable,
                'target_id' => $targetId,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan ganggu flow aplikasi
            Log::error('Failed to create audit log: ' . $e->getMessage());
        }
    }

    /**
     * Log create operation
     */
    public static function logCreate(string $table, string $id, array $data): void
    {
        self::log('CREATE', $table, $id, null, $data);
    }

    /**
     * Log update operation
     */
    public static function logUpdate(string $table, string $id, array $oldData, array $newData): void
    {
        self::log('UPDATE', $table, $id, $oldData, $newData);
    }

    /**
     * Log delete operation
     */
    public static function logDelete(string $table, string $id, array $data): void
    {
        self::log('DELETE', $table, $id, $data, null);
    }
}
