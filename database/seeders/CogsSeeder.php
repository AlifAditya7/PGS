<?php

namespace Database\Seeders;

use App\Models\CogsItem;
use App\Models\Facilitator;
use Illuminate\Database\Seeder;

class CogsSeeder extends Seeder
{
    public function run(): void
    {
        // General Items
        CogsItem::create(['name' => 'Catering (per pax)', 'price' => 50000]);
        CogsItem::create(['name' => 'ATK & Modul', 'price' => 75000]);
        CogsItem::create(['name' => 'Sewa Gedung (per hari)', 'price' => 2500000]);
        CogsItem::create(['name' => 'Sewa Zoom Pro (bulan)', 'price' => 300000]);
        CogsItem::create(['name' => 'Transportasi Lokal', 'price' => 500000]);

        // Facilitators
        Facilitator::create(['name' => 'Budi Santoso', 'specialization' => 'ISO Auditor', 'price' => 2000000]);
        Facilitator::create(['name' => 'Siti Aminah', 'specialization' => 'HR Trainer', 'price' => 1500000]);
        Facilitator::create(['name' => 'Andi Wijaya', 'specialization' => 'Finance Consultant', 'price' => 3000000]);
    }
}