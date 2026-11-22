<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bid;
use App\Models\Project;
use App\Models\User;

class GarudaHubSeeder extends Seeder
{
public function run(): void
    {
        // password di-hash otomatis oleh cast 'hashed' di model User
        User::create([
            'name' => 'Admin Garuda', 'email' => 'admin@example.com',
            'password' => 'password', 'role' => 'admin',
        ]);

        $clients = collect([
            ['Budi', 'budi@example.com'],
            ['Siti', 'siti@example.com'],
        ])->map(fn ($c) => User::create([
            'name' => $c[0], 'email' => $c[1],
            'password' => 'password', 'role' => 'client',
        ]));

        $developers = collect([
            ['Dewi', 'dewi@example.com'],
            ['Eko', 'eko@example.com'],
            ['Fitri', 'fitri@example.com'],
        ])->map(fn ($d) => User::create([
            'name' => $d[0], 'email' => $d[1],
            'password' => 'password', 'role' => 'developer',
        ]));

        $projectData = [
            ['Website Toko Online', 'Web', 5000000],
            ['Aplikasi Absensi Mobile', 'Mobile', 8000000],
            ['Dashboard Analitik', 'Data', 6500000],
            ['Sistem Inventaris', 'Web', 4000000],
        ];

        foreach ($projectData as $i => [$title, $category, $budget]) {
            $project = $clients[$i % $clients->count()]->projects()->create([
                'title' => $title,
                'description' => "Kebutuhan pengembangan: $title.",
                'category' => $category,
                'budget' => $budget,
                'deadline' => now()->addDays(14 + $i),
                'status' => 'open',
            ]);

            // satu developer hanya boleh menawar sekali per proyek
            foreach ($developers->take(2 + ($i % 2)) as $dev) {
                $project->bids()->create([
                    'developer_id' => $dev->id,
                    'price' => $budget - rand(100, 900) * 1000,
                    'duration_days' => rand(7, 30),
                    'proposal' => "Penawaran dari {$dev->name} untuk $title.",
                ]);
            }
        }
    }
}
