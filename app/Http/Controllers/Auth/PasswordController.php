<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedMail;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Kirim Notifikasi Keamanan
        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user->name));
        } catch (\Exception $e) {
            // Abaikan jika mail server bermasalah agar user tetap bisa update
        }

        return back()->with('status', 'password-updated');
    }
}
