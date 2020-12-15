<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['user', 'admin', 'matchmanager'];

        foreach ($roles as $role) {
            DB::table('user_role')->insert([
                'name' => $role,
            ]);
        }
    }
}
