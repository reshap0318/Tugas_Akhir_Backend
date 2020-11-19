<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{

    public function run()
    {
        $datas = [
            [
                'title'=> 'Perpanjangan Masa Pembayaran Heregistrasi Semester Ganjil 2020/2021', 
                'description'=> 'Diberitahukan kepada seluruh mahasiswa Universitas Andalas Bahwa:

                1.Pembayaran uang kuliah Periode Ganjil 2020/2021 d diperpanjang sampai 03 Agustus 2020. Pembayaran dilakukan pada bank mitra Universitas Andalas (Bank Nagari, Bank Syariah Mandiri, Bank Mandiri, BNI). Tata cara pembayaran dapat dilihat di menu pembayaran->tagihan pada portal akademik.
                
                2. Pengisian KRS Semester Ganjil 2020/2021 dibuka mulai tanggal 27 Juli 2020 s/d 07 Agustus 2020, jika diportal masih ada pesan "Bukan Periode Krs atau Revisi", silakan hubungi bagian akademik fakultas/ admin SIA fakultas masing-masing.
                
                3. Bagi Mahasiswa yang disetujui usulan keringanan pembayaran UKT dapat melakukan pembayaran setelah data tagihan di proses, Silahkan tunggu dan cek tagihan pada menu pembayaran->tagihan, jika tagihan sudah sesuai silahkan melakukan pembayaran.'
            ],
            [
                'title'=> 'Heregistrasi Semester Ganjil 2020/2021', 
                'description'=> 'Diberitahukan kepada seluruh mahasiswa Universitas Andalas Bahwa:

                1.Pembayaran uang kuliah Periode Ganjil 2020/2021 di mulai tanggal 15 Juli 2020 s/d 24 Juli 2020. Pembayaran dilakukan pada bank mitra Universitas Andalas (Bank Nagari, Bank Syariah Mandiri, Bank Mandiri, BNI). Tata cara pembayaran dapat dilihat di menu pembayaran->tagihan pada portal akademik.
                
                2. Sesuai Kalender Akademik pengisian KRS Semester Ganjil 2020/2021 dibuka mulai tanggal 27 Juli 2020 s/d 04 Agustus 2020, jika diportal masih ada pesan "Bukan Periode Krs atau Revisi", silakan hubungi bagian akademik fakultas/ admin SIA fakultas masing-masing.'
            ],
        ];

        foreach ($datas as $data) {
            News::create($data);
        }
    }
}
