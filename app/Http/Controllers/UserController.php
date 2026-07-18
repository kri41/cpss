<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function importForm(): View
    {
        return view('users.import');
    }

    public function importPreview(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ], [
            'file.required' => 'File CSV wajib diunggah.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return back()->withErrors(['file' => 'File harus berformat CSV (.csv atau .txt).']);
        }

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        // Auto-detect delimiter (comma vs semicolon vs tab — Excel Indonesia pakai semicolon)
        $firstLine = fgets($handle);
        if (!$firstLine) return back()->with('error', 'File CSV kosong atau tidak valid.');
        $delimiter = ',';
        $counts = [
            ','  => substr_count($firstLine, ','),
            ';'  => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);
        // Kembali ke awal baris header (setelah BOM sudah di-skip)
        fseek($handle, -strlen($firstLine), SEEK_CUR);

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) return back()->with('error', 'File CSV kosong atau tidak valid.');

        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        foreach (['name', 'email', 'password', 'role'] as $col) {
            if (!in_array($col, $header))
                return back()->with('error', "Kolom wajib \"{$col}\" tidak ditemukan di header CSV.");
        }

        $valid      = [];
        $invalid    = [];
        $baris      = 1;
        $emailsSeen = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $baris++;
            if (empty(array_filter($row))) continue;

            $data = array_combine($header, array_pad($row, count($header), ''));

            $name      = trim($data['name']      ?? '');
            $email     = strtolower(trim($data['email']     ?? ''));
            $password  = trim($data['password']  ?? '');
            $role      = trim($data['role']      ?? 'relawan');
            $provinsi  = trim($data['provinsi']  ?? '');
            $kabupaten = trim($data['kabupaten'] ?? '');
            $kecamatan = trim($data['kecamatan'] ?? '');
            $desa      = trim($data['desa']      ?? '');

            $errors = [];
            if (empty($name))                                              $errors[] = 'Nama kosong';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid';
            if (strlen($password) < 8)                                     $errors[] = 'Password minimal 8 karakter';
            if (!in_array($role, ['super_admin', 'admin', 'relawan']))     $errors[] = "Role \"{$role}\" tidak dikenal";
            if (!empty($email) && isset($emailsSeen[$email]))              $errors[] = 'Email duplikat dalam file';
            elseif (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && User::where('email', $email)->exists())
                                                                            $errors[] = 'Email sudah terdaftar';

            if ($errors) {
                $invalid[] = ['baris' => $baris, 'name' => $name ?: '-', 'email' => $email ?: '-', 'role' => $role, 'errors' => $errors];
            } else {
                $emailsSeen[$email] = true;
                $valid[] = compact('name', 'email', 'password', 'role', 'provinsi', 'kabupaten', 'kecamatan', 'desa');
            }
        }

        fclose($handle);

        session(['import_valid' => $valid, 'import_invalid' => $invalid]);

        return redirect()->route('users.import.confirm');
    }

    public function importConfirm(): View|RedirectResponse
    {
        $valid   = session('import_valid',   []);
        $invalid = session('import_invalid', []);

        if (empty($valid) && empty($invalid)) {
            return redirect()->route('users.import.form')->with('error', 'Sesi habis. Silakan upload file lagi.');
        }

        return view('users.import-preview', compact('valid', 'invalid'));
    }

    public function importConfirmStore(): RedirectResponse
    {
        $valid = session('import_valid', []);

        if (empty($valid)) {
            return redirect()->route('users.import.form')->with('error', 'Tidak ada data valid untuk diimport.');
        }

        $berhasil = 0;
        foreach ($valid as $data) {
            if (User::where('email', $data['email'])->doesntExist()) {
                User::create([
                    'name'      => $data['name'],
                    'email'     => $data['email'],
                    'password'  => Hash::make($data['password']),
                    'role'      => $data['role'],
                    'provinsi'  => $data['provinsi'] ?: null,
                    'kabupaten' => $data['kabupaten'] ?: null,
                    'kecamatan' => $data['kecamatan'] ?: null,
                    'desa'      => $data['desa'] ?: null,
                ]);
                $berhasil++;
            }
        }

        session()->forget(['import_valid', 'import_invalid']);

        return redirect()->route('users.index')
            ->with('success', "{$berhasil} pengguna berhasil diimport.");
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


}
