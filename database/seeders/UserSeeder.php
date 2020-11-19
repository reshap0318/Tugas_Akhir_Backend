<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run()
    {
        $datas = [
            [
                'name' => 'Reinaldo Shandev P',
                'username' => '1611522012',
                'role' => 3,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Annisa Aulia Khaira',
                'username' => '1611521006',
                'role' => 3,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Miftahul Asraf',
                'username' => '1611523012',
                'role' => 3,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Muhammad Farel Aleski',
                'username' => '1611523006',
                'role' => 3,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Husnil Kamil',
                'username' => 'husnilk', //'198201182008121002'
                'role' => 2,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Ricky Akbar',
                'username' => 'rickya', //198410062012121001
                'role' => 2,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Adi Arga Arifnur',
                'username' => 'adia', //199208202019031005
                'role' => 2,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Nindy Malisha',
                'username' => 'nindy',
                'role' => 1,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '3CjqjJplRxDZJbYPLPNseCF0BucoiSXv04aLw1fj',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Ade Priyanto',
                'username' => 'ade',
                'role' => 1,
                'unit_id' => 15,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            
        ];

        foreach ($datas as $data) {
            User::create($data);
        }
    }
}
