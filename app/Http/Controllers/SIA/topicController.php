<?php

namespace App\Http\Controllers\SIA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Topic\{detailCollection, listCollection};
use App\Models\{Topic};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\userController;

class topicController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $data = Topic::all();
            $data = listCollection::collection($data);
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function getData($id)
    {
        try {
            $data = topic::find($id);
            $data = $data ? new detailCollection($data) : [];
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = topic::find($id);
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|unique:topics,name'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $data = new Topic();
            $data->name = $request->name;
            $data->save();
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|unique:topics,name,'.$id
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        try {
            $data = topic::find($id);
            $data->name = $request->name;
            $data->save();
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $data = topic::find($id);
            $data->delete();
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function getListActif()
    {
        try {
            $userController = new userController();
            $periodAktif = $userController->periodAktif();
            $data = [];
            if($periodAktif){
                $data = Topic::whereRAW("id in (select topic_id from period_topics where period_id = '".$periodAktif->id."')")->get();
                $data = listCollection::collection($data);
            }
            return $this->MessageSuccess($data);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
