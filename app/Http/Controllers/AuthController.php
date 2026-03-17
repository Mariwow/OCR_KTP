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
        ]);

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

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', 
        ], [
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $user = User::findOrFail($id);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan salah!');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
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
