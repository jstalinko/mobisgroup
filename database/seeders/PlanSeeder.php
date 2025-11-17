<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'DramaBx',
                'price' => 30000,
                'description' => 'Akses penuh ke DramaBx saja dalam satu bulan unlock semua tayangan',
                'feature_dramabox' => true,
                'feature_netshort' => false,
                'duration' => 'month',
                'active' => true,
            ],
            [
                'name' => 'NetShrt',
                'price' => 28000,
                'description' => 'Akses penuh ke NetShrt saja dalam satu bulan unlock semua tayangan',
                'feature_dramabox' => false,
                'feature_netshort' => true,
                'duration' => 'month',
                'active' => true,
            ],
            [
                'name' => 'Marathon',
                'price' => 50000,
                'description' => 'Akses ke dramaBx + NetShrt satu bulan unlock semua tayangan',
                'feature_dramabox' => true,
                'feature_netshort' => true,
                'duration' => 'month',
                'active' => true
            ]
        ]);
    }
}
