<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,relawan',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,relawan',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Form import bulk user.
     */
    public function importForm(): View
    {
        return view('users.import');
    }

    /**
     * Download template CSV untuk import bulk.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        return response()->stream(function () {
            $output = fopen('php://output', 'w');
            // BOM agar Excel baca UTF-8 dengan benar
            fputs($output, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($output, ['name', 'email', 'password', 'role', 'provinsi', 'kabupaten', 'kecamatan', 'desa']);

            // Contoh baris
            fputcsv($output, ['Budi Santoso',  'budi@example.com',  'Password123', 'relawan', '35', 'Kabupaten Malang', 'Kecamatan Kepanjen', 'Desa Ardirejo']);
            fputcsv($output, ['Siti Rahayu',   'siti@example.com',  'Password123', 'admin',   '35', 'Kabupaten Malang', '', '']);
            fputcsv($output, ['Ahmad Fauzi',   'ahmad@example.com', 'Password123', 'relawan', '',   '',                '', '']);

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Proses import bulk user dari file CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.required' => 'File CSV wajib diunggah.',
            'file.mimes'    => 'File harus berformat CSV.',
            'file.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $path   = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        // Deteksi dan skip BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Skip header row
        $header = fgetcsv($handle);
        if (!$header) {
            return back()->with('error', 'File CSV kosong atau tidak valid.');
        }

        // Normalisasi header (lowercase, trim)
        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $expected = ['name', 'email', 'password', 'role'];
        foreach ($expected as $col) {
            if (!in_array($col, $header)) {
                return back()->with('error', "Kolom wajib \"{$col}\" tidak ditemukan di header CSV.");
            }
        }

        $berhasil = 0;
        $gagal    = [];
        $baris    = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $baris++;
            if (count($row) < 4 || empty(array_filter($row))) continue;

            $data = array_combine($header, array_pad($row, count($header), ''));

            $name      = trim($data['name']     ?? '');
            $email     = trim($data['email']    ?? '');
            $password  = trim($data['password'] ?? '');
            $role      = trim($data['role']     ?? 'relawan');
            $provinsi  = trim($data['provinsi'] ?? '');
            $kabupaten = trim($data['kabupaten'] ?? '');
            $kecamatan = trim($data['kecamatan'] ?? '');
            $desa      = trim($data['desa']     ?? '');

            // Validasi per baris
            $errors = [];
            if (empty($name))                                         $errors[] = 'name kosong';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email tidak valid';
            if (strlen($password) < 8)                                $errors[] = 'password < 8 karakter';
            if (!in_array($role, ['super_admin', 'admin', 'relawan'])) $errors[] = "role \"{$role}\" tidak dikenal";
            if (User::where('email', $email)->exists())               $errors[] = 'email sudah terdaftar';

            if (!empty($errors)) {
                $gagal[] = "Baris {$baris} ({$email}): " . implode(', ', $errors);
                continue;
            }

            User::create([
                'name'      => $name,
                'email'     => $email,
                'password'  => Hash::make($password),
                'role'      => $role,
                'provinsi'  => $provinsi  ?: null,
                'kabupaten' => $kabupaten ?: null,
                'kecamatan' => $kecamatan ?: null,
                'desa'      => $desa      ?: null,
            ]);

            $berhasil++;
        }

        fclose($handle);

        $msg = "{$berhasil} pengguna berhasil diimpor.";
        if (!empty($gagal)) {
            $msg .= ' ' . count($gagal) . ' baris dilewati: ' . implode(' | ', array_slice($gagal, 0, 5));
            if (count($gagal) > 5) $msg .= ' ... dan ' . (count($gagal) - 5) . ' lainnya.';
        }

        return redirect()->route('users.index')
            ->with($berhasil > 0 ? 'success' : 'error', $msg);
    }
}
