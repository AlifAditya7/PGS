<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Kustomisasi Email Verifikasi
     */
    public function sendEmailVerificationNotification()
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email - PGS Consulting')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di PGS Consulting.')
                ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda agar dapat mulai berlangganan layanan kami.')
                ->action('Verifikasi Email Sekarang', $url)
                ->line('Jika Anda tidak merasa mendaftar akun, abaikan saja email ini.')
                ->salutation("Salam hangat,\nTeam PGS Consulting");
        });

        $this->notify(new VerifyEmail);
    }

    /**
     * Kustomisasi Email Reset Password (Forgot Password)
     */
    public function sendPasswordResetNotification($token)
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Permintaan Reset Kata Sandi - PGS Consulting')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun PGS Consulting Anda.')
                ->line('Silakan klik tombol di bawah ini untuk melanjutkan proses reset kata sandi.')
                ->action('Reset Kata Sandi', $url)
                ->line('Tautan reset kata sandi ini akan kedaluwarsa dalam 60 menit.')
                ->line('Jika Anda tidak meminta reset kata sandi, abaikan saja email ini.')
                ->salutation("Salam hangat,\nTeam PGS Consulting");
        });

        $this->notify(new ResetPassword($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
