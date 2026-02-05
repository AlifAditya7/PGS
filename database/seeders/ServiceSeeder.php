<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'ISO 9001:2015 Consulting',
                'category' => 'consulting',
                'price' => 15000000,
                'description' => 'Konsultasi implementasi sistem manajemen mutu ISO 9001:2015.',
                'benefits' => ['Sertifikasi Internasional', 'Perbaikan Proses', 'Efisiensi Operasional'],
                'estimated_resources' => '2 Konsultan, 3 Bulan Estimasi'
            ],
            [
                'name' => 'Financial Audit Internal',
                'category' => 'auditing',
                'price' => 10000000,
                'description' => 'Audit keuangan internal untuk memastikan kepatuhan dan validitas data.',
                'benefits' => ['Laporan Temuan', 'Rekomendasi Perbaikan', 'Mitigasi Risiko'],
                'estimated_resources' => '1 Auditor senior, 2 Minggu'
            ],
            [
                'name' => 'Leadership Training Program',
                'category' => 'training',
                'price' => 5000000,
                'description' => 'Pelatihan kepemimpinan untuk level manajerial.',
                'benefits' => ['Sertifikat Pelatihan', 'Modul Eksklusif', 'Sesi Coaching'],
                'estimated_resources' => '1 Trainer, 2 Hari Intensif'
            ],
        ];

        foreach ($services as $service) {
            $service['slug'] = Str::slug($service['name']);
            Service::create($service);
        }
    }
}
