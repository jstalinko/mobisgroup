<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  
    public function run(): void
    {
        $data = [
           [ 'type' => 'ewallet',
            'account_name' => 'Mobis Group',
            'number' => '081234567890',
            'bank_name' => 'Dana',
            'active' => true
        ],
        [
            'type' => 'bank',
            'account_name' => 'Mobis Group',
            'number' => '1234567890',
            'bank_name' => 'BANK CENTRAL ASIS (BCA)',
            'active' => true
        ],
        [
            'type' => 'bank',
            'account_name' => 'Mobis Group',
            'number' => '0987654321',
            'bank_name' => 'BANK RAKYAT INDONESIA (BRI)',
            'active' => true
        ],

    ];

        foreach ($data as $item) {
            \App\Models\Rekening::create($item);
        }
    }
}
