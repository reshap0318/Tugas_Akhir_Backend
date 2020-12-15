<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{siaWeb};

class kelasController extends Controller
{
    public function getListKelas()
    {
        try {
            $nim = app('auth')->user()->username;
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/kelas");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function getDetailKelas($nim=null, $klsId)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/kelas/$klsId");
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
