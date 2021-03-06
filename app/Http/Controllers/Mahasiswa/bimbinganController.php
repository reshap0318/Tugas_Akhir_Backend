<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Helpers\firebase;
use App\Helpers\siaWeb;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Message, User, PeriodTopic};
use Illuminate\Support\Facades\{DB, Validator, Storage};
use App\Http\Resources\Bimbingan\listCollection;
use App\Http\Controllers\semesterController;


class bimbinganController extends Controller
{
    public function getListBimbingan()
    {
        # berisikan list topic perpriode dan banyak chat dallam chat tersebut
        try {
            
            $userId = app('auth')->user()->id;
            $data = Message::select('messages.*', DB::RAW("count(messages.id) as totalChat, max(time) as lastChat"))->join('period_topics', 'period_topics.id', '=', 'messages.topic_period_id')->where('period_topics.topic_id','<>','RSP03')->where('sender_id',$userId)->orWhere('receiver_id',$userId)->groupby(['period_topics.period_id', 'period_topics.topic_id'])->orderby('time','desc')->get();
            
            $data = listCollection::collection($data);
            return $this->MessageSuccess($data);

        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function getGroupChat()
    {
        try {
            $user = app('auth')->user();
            $dosenId = "";
            $dosenNama = "";
            $dosenAvatar = "";
            $dosenNip = "";
            $dataSia = siaWeb::get("/v1/mahasiswa/$user->username/pembimbing");
            if($dataSia){
                $dosenNip = $dataSia->data->nip;
                $dataDosen = User::where('username',$dosenNip)->where('role',2)->first();
                if($dataDosen){
                    $dosenId = $dataDosen->id;
                    $dosenNama = $dataDosen->name;
                    $dosenAvatar = $dataDosen->getAvatar();
                }
            }
            
            $semesterController = new semesterController();
            $periodAktiv = $semesterController->active()->original['data']->id;

            if($dosenId){
                $topicPeriodId = PeriodTopic::where('topic_id','RSP03')->where('period_id',$periodAktiv)->first();
                if(!$topicPeriodId){
                    $topicPeriodId = PeriodTopic::create(['topic_id' => 'RSP03', 'period_id' => $periodAktiv]);
                }

                return $this->MessageSuccess([
                    'to' => $dosenId,
                    'topicPeriodId' => $topicPeriodId->id,
                    'groupChanel' => $dosenNip,
                    'groupName' => "Group Bimbingan ".$dosenNama,
                    'groupAvatar' => $dosenAvatar
                ]);
            }
            return $this->MessageError("ada error");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function getDetailChat($receiverId, $topicPeriodId)
    {
        try {
            $senderId = app('auth')->user()->id;
            $data = Message::where(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)->where('receiver_id',$receiverId);
            })->orwhere(function ($query) use ($senderId, $receiverId) {
                $query->where('receiver_id', $senderId)->where('sender_id',$receiverId);
            })->where('topic_period_id',$topicPeriodId)->get();
            return $this->MessageSuccess($data);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiverId'   => 'required',
            'message' => 'required',
            'topicPeriodId' => 'required',
            'img' => 'image|mimes:jpg,png,jpeg,gif',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {
            $messages = new Message();
            $messages->sender_id = app('auth')->user()->id;
            $messages->receiver_id = $request->receiverId;
            $messages->message = $request->message;
            $messages->topic_period_id = $request->topicPeriodId;
            if ($request->hasFile('img') && $request->img->isValid()) {
                $fileext = $request->img->extension();
                $filename = 'bimbingan_'.time().'.'.$fileext;
                $messages->path_img = $request->file('img')->storeAs('imgs', $filename,'public');
            }

            $messages->save();
            if($messages->id){
                firebase::sendChat($messages); 
                firebase::sendNotificationToUID($messages->receiver->fcm_token,[
                    'title' => $messages->sender->name,
                    'body' => $messages->message,
                    'type' => 'chat',
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date("H:i")
                ], false); 
            }
            return $this->MessageSuccess("Berhasil Menambahkan Data");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function sendGroupChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiverId'   => 'required',
            'groupchanel' => 'required',
            'message' => 'required',
            'topicPeriodId' => 'required',
            'img' => 'image|mimes:jpg,png,jpeg,gif',
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {
            $messages = new Message();
            $messages->sender_id = app('auth')->user()->id;
            $messages->receiver_id = $request->receiverId;
            $messages->message = $request->message;
            $messages->topic_period_id = $request->topicPeriodId;
            if ($request->hasFile('img') && $request->img->isValid()) {
                $fileext = $request->img->extension();
                $filename = 'bimbingan_'.time().'.'.$fileext;
                $messages->path_img = $request->file('img')->storeAs('imgs', $filename,'public');
            }

            $messages->save();
            if($messages->id){
                $messages->groupchanel = $request->groupchanel;
                firebase::sendChatGroup($messages);
            }
            return $this->MessageSuccess("Berhasil Menambahkan Data");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function delete($chatId)
    {
        try {
            $data = Message::where('id',$chatId)->where('sender_id',app('auth')->user()->id);
            if($data->get()->isNotEmpty()){
                firebase::deleteMessage($chatId, app('auth')->user()->id);
                $data->delete();
                return $this->MessageSuccess("berhasil menghapus chat");
            }
            return $this->MessageError("gagal menghapus data");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function createBimbingan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topicPeriodId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {

            $user = app('auth')->user();
            $dosenId = "";
            $dataSia = siaWeb::get("/v1/mahasiswa/$user->username/pembimbing");
            if($dataSia){
                $dosenNip = $dataSia->data->nip;
                $dataDosen = User::where('username',$dosenNip)->where('role',2)->first();
                if($dataDosen){
                    $dosenId = $dataDosen->id;
                }
            }

            $messages = Message::where('topic_period_id', $request->topicPeriodId)->where(function ($query) use ($user){
                $query->where("sender_id",$user->id)->orwhere("receiver_id",$user->id);
            })->first();

            if(!$messages){
                $messages = new Message();
                $messages->sender_id = app('auth')->user()->id;
                $messages->receiver_id = $dosenId;
                $messages->message = "Jenis Bimbingan : ".PeriodTopic::find($request->topicPeriodId)->topic->name;
                $messages->topic_period_id = $request->topicPeriodId;
                $messages->save();
                if($messages->id){
                    firebase::sendChat($messages);
                    firebase::sendNotificationToUID($messages->receiver->fcm_token,[
                        'title' => $messages->sender->name,
                        'body' => $messages->message,
                        'type' => 'chat',
                        'tanggal' => date("Y-m-d"),
                        'waktu' => date("H:i")
                    ]); 
                }
            }
            $data = new listCollection($messages);
            return $this->MessageSuccess($data);

        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
