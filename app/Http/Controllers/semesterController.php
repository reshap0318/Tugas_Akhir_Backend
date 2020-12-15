<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\siaWeb;
use App\Models\Period;

class semesterController extends Controller
{
    public function active()
    {
        try {
            $lastPeriod = Period::latest('id')->first();
            $data = json_decode(json_encode([
                'id' => $lastPeriod->id,
                'tahun' => explode(" ",$lastPeriod->name)[2],
                'periode' => explode(" ",$lastPeriod->name)[1]
            ]));
            $dataSia = siaWeb::get('v1/semester-aktif');
            if($dataSia){
                $data =  $dataSia->data;
            }
            return $this->MessageSuccess($data);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
