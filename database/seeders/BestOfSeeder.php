<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BestOfSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i <= 7; $i++) {
            DB::table('best_ofs')->insert([
                'round' => $i,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }
    }
}
