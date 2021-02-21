<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{siaWeb,firebase};
use App\Models\{Message, User, PeriodTopic};
use Illuminate\Support\Facades\{DB, Validator, Storage};
use App\Http\Resources\Bimbingan\listCollection;
use App\Http\Controllers\semesterController;

class bimbinganController extends Controller
{
    public function getListData()
    {
        try {
            $nip = app('auth')->user()->username;
            $dataSia = siaWeb::get("/v1/dosen/$nip/mahasiswa-bimbingan");
            if($dataSia){
                $data = [];
                foreach ($dataSia->data as $key => $value) {
                    $mUser = User::where("username", $value->nim)->where("role",3)->first();
                    $data []=[
                        'id' => $mUser ? $mUser->id : "",
                        'nim' => $value->nim,
                        'nama' => $value->nama,
                        'avatar' => $mUser ? $mUser->getAvatar() : "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode($value->nama)
                    ];
                }
                return $this->MessageSuccess($data);
            }
            return $this->MessageError("Web SIA Not Active");
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function getGroupChat()
    {
        try {
            $user = app('auth')->user();
            $dosenId = $user->id;
            $dosenNama = $user->name;
            $dosenAvatar = $user->getAvatar();
            $dosenNip = $user->username;
            
            $semesterController = new semesterController();
            $periodAktiv = $semesterController->active()->original['data']->id;

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
            return $this->MessageError("ada error");
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

    public function getListBimbingan($mhsId)
    {
        # berisikan list topic perpriode dan banyak chat dallam chat tersebut
        try {
            $userId = app('auth')->user()->id;
            $data = Message::select('messages.*', DB::RAW("count(messages.id) as totalChat, max(time) as lastChat"))->join('period_topics', 'period_topics.id', '=', 'messages.topic_period_id')->where(function ($query) use ($userId, $mhsId) {
                $query->where('sender_id',$userId)->Where('receiver_id',$mhsId);
            })->orwhere(function ($query) use ($userId, $mhsId) {
                $query->where('sender_id',$mhsId)->Where('receiver_id',$userId);
            })->where('period_topics.topic_id','<>','RSP03')->groupby(['period_topics.period_id', 'period_topics.topic_id'])->get();

            $data = listCollection::collection($data);
            return $this->MessageSuccess($data);

        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function lastSeen()
    {
        # berisikan list topic perpriode dan banyak chat dallam chat tersebut
        try {
            $userId = app('auth')->user()->id;
            // $data = Message::select('messages.*', DB::RAW("count(messages.id) as totalChat, max(time) as lastChat"))->join('period_topics', 'period_topics.id', '=', 'messages.topic_period_id')->where('period_topics.topic_id','<>','RSP03')->where(function ($query) use ($userId) {
            //     $query->where('sender_id',$userId)->orWhere('receiver_id',$userId);
            // })->groupby(['period_topics.period_id', 'period_topics.topic_id'])->orderby('time','desc')->limit(5)->get();

            $data = Message::select('messages.*', DB::RAW("count(messages.id) as totalChat, max(time) as lastChat"))->join(DB::RAW("(select max(time) as waktu_akhir, receiver_id from messages join period_topics on messages.topic_period_id = period_topics.id WHERE period_topics.topic_id <> 'RSP03' and (sender_id = '$userId') GROUP by receiver_id) as mMessages"), function($join){
                $join->on("messages.receiver_id","=","mMessages.receiver_id");
                $join->on("messages.time", "=", "mMessages.waktu_akhir");
            })->get();
            
            $data = listCollection::collection($data);
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

    public function createBimbingan($mhsId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topicPeriodId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->MessageError($validator->errors(), 422);
        }
        
        try {

            $dosen = app('auth')->user();

            $messages = Message::where('topic_period_id', $request->topicPeriodId)->where(function ($query) use ($dosen){
                $query->where("sender_id",$dosen->id)->orwhere("receiver_id",$dosen->id);
            })->first();

            if(!$messages){
                $messages = new Message();
                $messages->sender_id = app('auth')->user()->id;
                $messages->receiver_id = $mhsId;
                $messages->message = "Topik Bimbingan : ".PeriodTopic::find($request->topicPeriodId)->topic->name;
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

    public function cetakChatBimbingan($receiverId, $topicPeriodId)
    {
        try {
            $senderId = app('auth')->user()->id;
            $data = Message::where('topic_period_id',$topicPeriodId)->where(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)->where('receiver_id',$receiverId);
            })->orwhere(function ($query) use ($senderId, $receiverId) {
                $query->where('receiver_id', $senderId)->where('sender_id',$receiverId);
            })->orderby('time','asc')->get();

            $topicPeriod = PeriodTopic::find($topicPeriodId);
            $receiverPdf = User::find($receiverId);

            $dataEmail = [
                'pembimbing' => app('auth')->user()->name,
                'pembimbing_id' => app('auth')->user()->id,
                'mahasiswa' => $receiverPdf->name,
                'mahasiswa_id' => $receiverPdf->id,
                'topik' => $topicPeriod->topic->name,
                'period' => $topicPeriod->period->name,
                'to' => app('auth')->user()->email,
                'title' => 'Cetak Laporan Rekap Bimbingan',
                'titlePdf' => str_replace(" ", "_", "rekap_bimbingan_".app('auth')->user()->name."_W_".$receiverPdf->name."_topik_".$topicPeriod->topic->name."_".$topicPeriod->period->name.".pdf")
            ];
            if(app('auth')->user()->email){
                $sendEmail = siaWeb::sendMail((object)$dataEmail, $data);
                return $this->MessageSuccess($sendEmail);
            }
            return $this->MessageError("Set Email Terlebih Dahulu", 422);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }

    public function cetakChatBimbinganPeriod($receiverId, $periodId)
    {
        try {
            $senderId = app('auth')->user()->id;
            $data = Message::join('period_topics','messages.topic_period_id','=','period_topics.id')->where('period_topics.period_id',$periodId)->where(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)->where('receiver_id',$receiverId);
            })->orwhere(function ($query) use ($senderId, $receiverId) {
                $query->where('receiver_id', $senderId)->where('sender_id',$receiverId);
            })->orderby('time','asc')->get();

            $topicPeriod = PeriodTopic::where('period_id',$periodId)->get();
            $receiverPdf = User::find($receiverId);

            $dataEmail = [
                'pembimbing' => app('auth')->user()->name,
                'pembimbing_id' => app('auth')->user()->id,
                'mahasiswa' => $receiverPdf->name,
                'mahasiswa_id' => $receiverPdf->id,
                'topik' => $topicPeriod->count(),
                'period' => $periodId,
                'to' => app('auth')->user()->email,
                'title' => 'Cetak Laporan Rekap Bimbingan Periode '.$periodId,
                'titlePdf' => str_replace(" ", "_", "rekap_bimbingan_".app('auth')->user()->name."_W_".$receiverPdf->name."_period_".$periodId.".pdf")
            ];
            
            if(app('auth')->user()->email){
                $sendEmail = siaWeb::sendMail((object)$dataEmail, $data->groupby('topic_period_id'), true);
                return $this->MessageSuccess($sendEmail);
            }
            return $this->MessageError("Set Email Terlebih Dahulu", 422);
        } catch (\Exception $e) {
            return $this->MessageError($e->getMessage());
        }
    }
}
