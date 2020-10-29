<?php

namespace App\Http\Controllers\SIA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SIA\News\{detailCollection, listCollection};
use App\Models\{News};
use Illuminate\Support\Facades\Validator;

class newsController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $data = News::all();
            // return $this->MessageSuccess(listCollection::collection($data));
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function getData($id)
    {
        try {
            $data = news::find($id);
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = news::find($id);
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|unique:news,title',
            'body'    => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $data = new news();
            $data->title = $request->title;
            $data->body = $request->body;
            $data->user_id = app('auth')->user()->id;
            $data->save();
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|unique:news,title,'.$id,
            'body'    => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {
            $data = news::find($id);
            $data->title = $request->title;
            $data->body = $request->body;
            $data->save();
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $data = news::find($id);
            $data->delete();
            return $this->MessageSuccess($data);
        } catch (\Throwable $th) {
            return $this->MessageError($th->getMessage());
        }
    }
}
