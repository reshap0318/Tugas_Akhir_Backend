<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{siaWeb};

class semesterController extends Controller
{
    public function getListData($nim=null)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/list-semester");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

}
