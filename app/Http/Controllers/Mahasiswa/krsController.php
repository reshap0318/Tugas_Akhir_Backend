<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\{siaWeb, firebase};
use App\Models\{User};

class krsController extends Controller
{
    public function getListData($nim=null)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/krs");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function getListDataSemester($semester, $nim=null)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/krs/$semester");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function isCanEntry($nim=null)
    {
        try {
            if($nim==null){
                $nim = app('auth')->user()->username;
            }
            $dataSia = siaWeb::get("v1/mahasiswa/$nim/krs/isCanEntry");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");   
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function entry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'klsId'   => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }


        try {
            $nim = app('auth')->user()->username;
            $dataSia = siaWeb::post("v1/mahasiswa/$nim/krs/entry",["klsId"=>$request->klsId]);
            if($dataSia){
                $data = $dataSia->data;
                $this->sendNotifKRSModif($nim,"Menambahkan Sebuah KRS");
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");  
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function isCanChange()
    {
        try {
            $dataSia = siaWeb::get("v1/mahasiswa/1611522012/krs/isCanChange");
            if($dataSia){
                $data = $dataSia->data;
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");   
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function changeStatus($nim, Request $request, $status)
    {
        $validator = Validator::make($request->all(), [
            'krsdtId'   => 'required|array|min:1',
            'krsdtId.*' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $num = 0;
            foreach($request->krsdtId as $krsdtId){
                $dataSia = siaWeb::post("v1/mahasiswa/$nim/krs/$krsdtId/chage-status/$status", [""]);
                if($dataSia){
                    $num += 1;
                }
            }
            if($num > 0){
                $dataMahasiswa = User::where('username',$nim)->where('role',3)->first();
                if($dataMahasiswa){
                    firebase::sendNotificationToUID($dataMahasiswa->fcm_token,[
                        'title' => $status==1 ? "Persetujuan KRS" : "Menolak KRS",
                        'body' => $status==1 ? app('auth')->user()->name. " Menyetujui $num KRS" : app('auth')->user()->name. " Menolak $num KRS",
                        'type' => 'persetujuanKRS',
                        'tanggal' => date("Y-m-d"),
                        'waktu' => date("H:i")
                    ]); 
                }
                return $this->MessageSuccess("Berhasil Merubah $num Data");
            }
            return $this->MessageError("Web SIA Not Active");  
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function deleteKrs($krsdtId)
    {
        try {
            $nim = app('auth')->user()->username;
            $dataSia = siaWeb::mDelete("v1/mahasiswa/$nim/krs/delete/$krsdtId",[""]);
            if($dataSia){
                $data = $dataSia->data;
                $this->sendNotifKRSModif($nim,"Menghapus Sebuah KRS");
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");  
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function sendNotifKRSModif($nim, $pesan)
    {
        $dataSiaDosPem = siaWeb::get("/v1/mahasiswa/$nim/pembimbing");
        if($dataSiaDosPem){
            $dosenNip = $dataSiaDosPem->data->nip;
            $dataDosen = User::where('username',$dosenNip)->where('role',2)->first();
            if($dataDosen){
                firebase::sendNotificationToUID($dataDosen->fcm_token,[
                    'title' => app('auth')->user()->name,
                    'body' => $pesan,
                    'type' => 'chat',
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date("H:i")
                ]); 
            }
        }
        return true;
    }
}
