<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{siaWeb};
use App\Models\User;

class myController extends Controller
{
    public function getData($nim=null)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim");
            if($dataSia){
                $nim = $dataSia->data->nim;
                $nama = $dataSia->data->nama;
                $data = [
                    'nim' => $nim,
                    'nama' => $nama,
                    'avatar' => User::where("username", $nim)->where("role",3)->first() ? User::where("username", $nim)->where("role",3)->first()->getAvatar() : "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode($nama)
                ];
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
