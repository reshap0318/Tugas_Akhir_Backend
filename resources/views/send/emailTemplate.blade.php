<!DOCTYPE html>
<html>
<head>
    <title>SIA Pengembangan Android</title>
</head>
<body>
    <h1>{{ $data->title }}</h1>
    <p>Berikut File Bimbingan Bapak / Ibu : </p>
    <p> 
        <ul>
            <li>Nama Dosen : {{ $data->pembimbing }}</li>
            <li>Nama Mahasiswa : {{ $data->mahasiswa }}</li>
            <li>Topik : {{ $data->topik }}</li>
            <li>Period : {{ $data->period }}</li>
        </ul>    
    </p>
     
    <p>Thank you</p>
</body>
</html>