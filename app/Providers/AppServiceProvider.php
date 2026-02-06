<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedMail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Notifikasi Keamanan saat Password Reset (Forgot Password)
        Event::listen(PasswordReset::class, function ($event) {
            try {
                Mail::to($event->user->email)->send(new PasswordChangedMail($event->user->name));
            } catch (\Exception $e) {
                // Ignore error
            }
        });
    }
}
