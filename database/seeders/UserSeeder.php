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
        $idMahasiswa = 3;
        $idDosen = 2;
        $idAdmin = 1;

        $datas = [
            [
                'name' => 'Reinaldo Shandev P',
                'username' => '1611522012',
                'role' => $idMahasiswa,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Annisa Aulia Khaira',
                'username' => '1611521006',
                'role' => $idMahasiswa,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Miftahul Asraf',
                'username' => '1611523012',
                'role' => $idMahasiswa,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Muhammad Farel Aleski',
                'username' => '1611523006',
                'role' => $idMahasiswa,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Fajar Wirya Putra',
                'username' => '1611521021',
                'role' => $idMahasiswa,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'DIO HARVANDY',
                'username' => '1711522004',
                'role' => $idMahasiswa,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'AFIF MAULANA ISMAN',
                'username' => '1711522012',
                'role' => $idMahasiswa,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Husnil Kamil',
                'username' => '19820118200', //'19820118200'
                'role' => $idDosen,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Fajril Akbar',
                'username' => '198001102008121002', //198410062012121001
                'role' => $idDosen,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Ricky Akbar',
                'username' => '198410062012121001', //198410062012121001
                'role' => $idDosen,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Adi Arga Arifnur',
                'username' => '199208202019031005', //199208202019031005
                'role' => $idDosen,
                'unit_id' => 53,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Nindy Malisha',
                'username' => 'nindy',
                'role' => $idAdmin,
                'unit_id' => 52,
                'password' => Hash::make('root'),
                'api_token' => '3CjqjJplRxDZJbYPLPNseCF0BucoiSXv04aLw1fj',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Ade Priyanto',
                'username' => 'ade',
                'role' => $idAdmin,
                'unit_id' => 15,
                'password' => Hash::make('root'),
                'api_token' => '',
                'fcm_token' => Str::random(20)
            ],
            [
                'name' => 'Surya Afnarius',
                'username' => '132137882',
                'role' => $idDosen,
                'unit_id' => 52,
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
