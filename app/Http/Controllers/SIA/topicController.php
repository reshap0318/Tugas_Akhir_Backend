<?php

namespace App\Http\Controllers\SIA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SIA\Topic\{detailCollection, listCollection};
use App\Models\{Topic};
use Illuminate\Support\Facades\Validator;

class topicController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $data = Topic::all();
            return $this->MessageSuccess(listCollection::collection($data));
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function getData($id)
    {
        try {
            $data = topic::find($id);
            return $this->MessageSuccess(new detailCollection($data));
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
            'name'   => 'required|unique:news,title',
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
            'name'   => 'required|unique:news,title,'.$id,
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
}
