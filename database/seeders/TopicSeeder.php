<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Topic, PeriodTopic};
use App\Helpers\siaWeb;

class TopicSeeder extends Seeder
{

    public function run()
    {
        $datas = [
            ['name'=>'Bimbingan Ujian Akhir Semester'],
            ['name'=>'Bimbingan Ujian Tengah Semester'],
            ['name'=>'Bimbingan Tidak Terjadwal'],
            // ['id'=> 'RSP02', 'name'=>''],
            // ['id'=> 'RSP03', 'name'=>''],
        ];

        $dataSia = siaWeb::get('v1/semester-aktif');
        if($dataSia){
            $semesterAktif = $dataSia->data;
            foreach ($datas as $key => $data) {
                $data = Topic::create($data);
                PeriodTopic::create([
                    'period_id' => $semesterAktif->id,
                    'topic_id' => $data->id
                ]);
            }
        }
        
    }
}
