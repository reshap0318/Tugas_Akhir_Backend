<?php

namespace App\Http\Controllers\SIA;

use App\Http\Controllers\Controller;
use Illuminate\Support\{Str, Facades\Validator};
use Illuminate\Http\Request;
use App\Helpers\siaWeb;
use App\Models\{Period, PeriodTopic};


class periodController extends Controller
{
    public function getlist()
    {
        try {
            $data = Period::all();
            return $this->MessageSuccess($data);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function syn()
    {
        try {
            $dataSia = siaWeb::get("v1/list-semester");
            $newData = [];
            if($dataSia){
                $data = Period::get()->pluck('name')->toArray();
                foreach ($dataSia->data as $value) {
                    $siaName = $value->periode." ".$value->tahun;
                    $temp = ['id'=>$value->id,'name'=> $siaName];
                    if(!in_array($siaName, $data)){
                        $newData []= $temp;
                    }
                    
                }
                $newData = array_map("unserialize", array_unique(array_map("serialize", $newData)));
                if($newData){
                    foreach ($newData as $val) {
                        $data = Period::create($val);
                    }
                    return $this->MessageSuccess(count($newData)." Data Syncron into Database");
                }
            }
            return $this->MessageSuccess("No Data Syn");
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }

    }

    public function addTopic($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'topics'   => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            foreach ($request->topics as $key => $value) { 
                $data = new PeriodTopic();
                $data->period_id = $id;
                $data->topic_id = $value;
                $data->save();
            }
            return $this->MessageSuccess("Berhasil Menambahkan Topic");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function deleteTopic($id, $topicId)
    {
        try {
            $data = PeriodTopic::where('period_id',$id)->where('topic_id',$topicId)->first();
            if($data){
                $data->delete();
                return $this->MessageSuccess("Berhasil Menghapus Topic");
            }
            return $this->MessageError("Data Not Found");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
