<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ServiceSeeder::class);

        // Create Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);

        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'Admin PGS',
            'email' => 'admin@pgs.com',
        ]);
        $admin->assignRole('admin');

        // Create Customer User
        $customer = User::factory()->create([
            'name' => 'Customer Test',
            'email' => 'customer@gmail.com',
        ]);
        $customer->assignRole('customer');
    }
}