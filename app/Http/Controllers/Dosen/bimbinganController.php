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
            $dosenId = "";
            $dosenNama = "";
            $dosenAvatar = "";
            $dataSia = siaWeb::get("/v1/mahasiswa/$user->username/pembimbing");
            if($dataSia){
                $dosenNip = $dataSia->data->nip;
                $dataDosen = User::where('username',$dosenNip)->where('role',2)->first();
                if($dataDosen){
                    $dosenId = $dataDosen->id;
                    $dosenNama = $dataDosen->nama;
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
                    'topic' =>  $topicPeriodId->topic->name,
                    'period' =>  $topicPeriodId->period->name,
                    'totalChat' => 0,
                    'lastChat' => 0,
                    'namaUser' => $dosenNama,
                    'avataUser' => $dosenAvatar
                ]);
            }
            return $this->MessageError("ada error");
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
            })->groupby(['period_topics.period_id', 'period_topics.topic_id'])->get();

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
            }
            return $this->MessageSuccess("Berhasil Menambahkan Data");
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
            $data = Message::where(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)->where('receiver_id',$receiverId);
            })->orwhere(function ($query) use ($senderId, $receiverId) {
                $query->where('receiver_id', $senderId)->where('sender_id',$receiverId);
            })->where('topic_period_id',$topicPeriodId)->orderby('time','asc')->get();

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
}
