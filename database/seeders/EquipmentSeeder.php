<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            [
                'name' => 'Treadmill Pro 2000',
                'picture' => 'treadmill.jpg',
                'status' => 'available',
                'maintenance_schedule' => now()->addMonths(3),
                'last_serviced_at' => now()->subMonths(1),
                'notes' => 'Regular maintenance required every 4 months',
            ],
            [
                'name' => 'Elliptical Trainer',
                'picture' => 'elliptical.jpg',
                'status' => 'in-use',
                'maintenance_schedule' => now()->addMonths(2),
                'last_serviced_at' => now()->subMonths(2),
                'notes' => 'Check resistance levels',
            ],
            [
                'name' => 'Weight Bench',
                'picture' => 'weight_bench.jpg',
                'status' => 'available',
                'maintenance_schedule' => now()->addMonths(6),
                'last_serviced_at' => now()->subMonths(1),
                'notes' => 'In good condition',
            ],
            [
                'name' => 'Rowing Machine',
                'picture' => 'rowing_machine.jpg',
                'status' => 'under-maintenance',
                'maintenance_schedule' => now()->addMonth(),
                'last_serviced_at' => now()->subMonths(4),
                'notes' => 'Needs belt replacement',
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::create($item);
        }
    }
}
