<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Auth\Notifications\VerifyEmail;
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
