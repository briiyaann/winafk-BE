<?php

use App\Models\Core\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $userTypes = [
            [
                'name' => 'Silver Account',
                'commission_percentage' => 0.01,
            ],
            [
                'name' => 'Gold Account',
                'commission_percentage' => 0.03,
            ]
        ];

        foreach ($userTypes as $userType) {
            UserType::create([
                'name' => $userType['name'],
                'commission_percentage' => $userType['commission_percentage'],
            ]);
        }
    }
}
