<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    
    // Pastikan role customer diberikan
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    expect($user->hasRole('customer'))->toBeTrue();

    $response->assertRedirect(route('dashboard', absolute: false));
});
