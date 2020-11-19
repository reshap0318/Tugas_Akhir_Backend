<?php

namespace App\Http\Controllers\SIA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\siaWeb;
use App\Models\Unit;

class unitController extends Controller
{
    public function getlist()
    {
        try {
            $data = Unit::all();
            return $this->MessageSuccess($data);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function syn()
    {
        try {
            $dataSia = siaWeb::get("v1/units");
            $newData = [];
            if($dataSia){
                $data = unit::get()->pluck('name')->toArray();
                foreach ($dataSia->data->units as $value) {
                    $sia = $value->name;
                    $temp = ['id'=>$value->id,'name'=> $sia, 'unit_id'=>$value->unit_id];
                    if(!in_array($sia, $data)){
                        $newData []= $temp;
                    }
                    
                }
                $newData = array_map("unserialize", array_unique(array_map("serialize", $newData)));
                $changeID = [];
                if($newData){
                    foreach ($newData as $key => $val) {
                        $id = $val['id'];
                        if($id){
                            if(unit::find($id)){
                                $newID = unit::latest('id')->first() ? unit::latest('id')->first()->id + 1 : 1;
                                $changeID = [$newID=>$id];
                            }
                        }else{
                            $oldUnitID = $val['unit_id'];
                            $newKey = array_search($oldUnitID, $changeID);
                            if($newKey){
                                $newData[$key]['unit_id'] = $newKey;
                            }
                        }
                    }
                    foreach ($newData as $val1) {
                        $data = unit::create($val1);
                    }
                    return $this->MessageSuccess(count($newData)." Syncron into Database");
                }
            }
            return $this->MessageError("No Data Syn", 201);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }

    }
}
