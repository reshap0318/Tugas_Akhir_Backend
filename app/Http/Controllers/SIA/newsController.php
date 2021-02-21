<?php

namespace App\Http\Controllers\SIA;

use App\Events\sendNewsNotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\News\{detailCollection, listCollection};
use App\Models\{News,unit};
use App\Helpers\firebase;
use Illuminate\Support\Facades\Validator;

class newsController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $id  = app('auth')->user()->unit_id;
            $data = News::whereRAW("unit_id in (SELECT id FROM `units` where id='$id' or id in (select unit_id from units where id='$id')) or unit_id is null")->orderby('created_at','desc')->get();
            if($id==""||$id==null){
                $data = News::orderby('created_at','desc')->get();
            }
            return $this->MessageSuccess(listCollection::collection($data));
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function getData($id)
    {
        try {
            $data = News::find($id);
            return $this->MessageSuccess(new detailCollection($data));
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = News::find($id);
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|unique:news,title',
            'description'    => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }

        try {
            $data = new news();
            $data->title = $request->title;
            $data->description = $request->description;
            $data->unit_id = app('auth')->user()->unit_id;
            $data->save();

            if($data->id){
                // event(new sendNewsNotificationEvent($data));
                $mUnitId = $data->unit_id;
                $notice = [
                    'title' => $data->title,
                    'body' => $data->description,
                    'type' => 'news',
                    'id' => $data->id
                ];
                $units = unit::whereRaw("id = $mUnitId or unit_id = $mUnitId")->get();
                foreach($units as $unit){
                    firebase::sendNotificationToTopic($unit->id, $notice);
                }
            }
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|unique:news,title,'.$id,
            'description'    => 'required',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {
            $data = news::find($id);
            $data->title = $request->title;
            $data->description = $request->description;
            $data->save();
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $data = news::find($id);
            $data->delete();
            return $this->MessageSuccess($data);
        } catch (\Exception $th) {
            return $this->MessageError($th->getMessage());
        }
    }
}
