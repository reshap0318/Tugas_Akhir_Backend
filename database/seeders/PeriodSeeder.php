<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\siaWeb;
use App\Models\{Period};

class PeriodSeeder extends Seeder
{

    public function run()
    {
        $data = siaWeb::get("v1/list-semester");
        if($data){
            $datas = $data->data;
            foreach ($datas as $data) {
                $siaName = $data->periode." ".$data->tahun;
                Period::create([
                    'id' => $data->id,
                    'name' => $siaName
                ]);
            }
        }
    }
}
