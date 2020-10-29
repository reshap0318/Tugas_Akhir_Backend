<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $CodeSuccess = 200;
    public $CodeFailed = 500;

    
    public function MessageSuccess($success, $kode=null)
    {
        $kode = $kode ? $kode : $this->CodeSuccess;
        return response()->json([
            'success' => true,
            'data'    => $success,
        ], $kode);
    }

    public function MessageError($error, $kode=null)
    {
        $kode = $kode ? $kode : $this->CodeFailed;
        return response()->json([
            'success' => false,
            'errors'    => $error,
        ], $kode);
    }
}
