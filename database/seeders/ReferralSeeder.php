<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referrals = [
            3,5,6,8,7,8
        ];
            DB::table('referrals')->truncate();

        foreach ($referrals as $ref) {
            DB::table('referrals')->insert([
                'user_id' => 2,
                'referred_user_id' => $ref,
                'bonus_amount' => rand(5000, 20000),
                'plan_id' => 1,
                'status' => fake()->randomElement([ 'completed','withdrawn']),
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
