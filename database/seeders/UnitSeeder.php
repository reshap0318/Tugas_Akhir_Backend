<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\siaWeb;
use App\Models\unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $data = siaWeb::get("v1/units");
        if($data){
            $datas = $data->data->units;
            foreach ($datas as $data) {
                unit::create([
                    'id' => $data->id,
                    'name' => $data->name,
                    'unit_id' => $data->unit_id
                ]);
            }
        }
    }
}
