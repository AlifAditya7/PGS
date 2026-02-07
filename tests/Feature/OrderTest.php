<?php

use App\Models\User;
use App\Models\Service;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles for testing environment
    Role::findOrCreate('admin');
    Role::findOrCreate('customer');
});

test('customer can access service catalog', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $user->email_verified_at = now();
    $user->save();

    $response = $this->actingAs($user)->get(route('orders.catalog'));

    $response->assertStatus(200);
    $response->assertSee('Katalog Layanan PGS');
});

test('customer can register for a service', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $user->email_verified_at = now();
    $user->save();

    $service = Service::factory()->create([
        'name' => 'Test Service',
        'price' => 100000,
        'benefits' => ['Benefit 1'],
        'activities' => ['Activity 1']
    ]);

    $response = $this->actingAs($user)->post(route('orders.store'), [
        'service_id' => $service->id,
    ]);

    $response->assertRedirect(route('orders.my-orders'));
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'service_id' => $service->id,
    ]);
});

test('regular customer cannot access admin management', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->get(route('admin.orders.index'));

    $response->assertStatus(403); // Forbidden
});
