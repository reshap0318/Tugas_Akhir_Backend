<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helper\getFormUrl;
use App\Models\unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $data = getFormUrl::get("http://127.0.0.1:1234/api/v1/units");
        if($data->success){
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
