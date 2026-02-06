<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeletedMail;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Menggunakan Bcrypt
            'email_verified_at' => now(), // Otomatis verifikasi jika dibuat oleh admin
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Kirim Email Notifikasi
        try {
            Mail::to($userEmail)->send(new AccountDeletedMail($userName));
        } catch (\Exception $e) {
            // Lanjutkan penghapusan meskipun email gagal terkirim
        }

        $user->delete();

        return redirect()->back()->with('success', "Akun $userName ($userEmail) telah dihapus. Email pemberitahuan telah dikirim.");
    }
}