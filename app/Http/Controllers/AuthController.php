<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,fo', // Hanya boleh admin atau fo
        ], [
        // Ubah pesan error ke bahasa yang mudah dimengerti
        'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain!',
        'password.confirmed' => 'Konfirmasi password tidak cocok!',
        'password.min' => 'Password minimal harus 8 karakter!'
         ]);

         $validator->validate();

        // 2. Buat User Baru (Password otomatis Bcrypt via Hash::make)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
            'role'     => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun berhasil dibuat!');
    }

    public function login(Request $request)
{
    // 1. Validasi input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 2. Coba Login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // Keamanan: cegah session fixation

        $user = Auth::user();

        // 3. Cek Role & Redirect
        if ($user->role === 'admin') {
            return redirect()->intended('/confirmation'); 
        }

        // Kalau FO arahkan ke scan
        return redirect()->intended('/scan'); 
    }

    // 4. Kalau gagal balik lagi dengan pesan error
    return back()->withErrors([
        'email' => 'Email atau password salah, coba cek lagi.',
    ])->onlyInput('email');
}

    public function index()
    {
        return view('login'); 
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
    
    public function viewIndex()
    {
        $users = User::latest()->get();
        return view('accountControl', compact('users'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            // Validasi email harus mengabaikan ID user ini agar tidak dianggap duplikat oleh dirinya sendiri
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id, 
            'role'     => 'required|in:admin,fo',
            // Password dibuat nullable (boleh kosong)
            'password' => 'nullable|string|min:8|confirmed', 
        ], [
            'email.unique'       => 'Email ini sudah terdaftar!',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok!',
            'password.min'       => 'Password baru minimal harus 8 karakter!'
        ]);

        // 2. Ambil data user
        $user = User::findOrFail($id);

        // 3. Update informasi dasar
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        // 4. Update password HANYA jika form password diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 5. Simpan ke database
        $user->save();

        return redirect()->back()->with('success', 'Data akun berhasil diperbarui!');
    }

    public function deleteAccount($id)
    {
        $user = User::findOrFail($id);
        
        // Opsional: Cek kalau user mencoba menghapus akunnya sendiri yang sedang login
        if (auth()->id() == $id) {
        return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang digunakan!');
        }

        // Hapus akun
        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus permanen.');
    }
}
