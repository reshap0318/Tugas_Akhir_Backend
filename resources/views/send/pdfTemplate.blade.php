<!DOCTYPE html>
<html>
<head>
    <title>SIA Pengembangan Android</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even){background-color: #f2f2f2}
    </style>
</head>
<body>
    <h1>{{ $data->title }}</h1>
    <p>Berikut Bimbingan Bapak / Ibu dengan Mahasiswa {{ $data->mahasiswa }} </p>
    <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>Waktu</th>
                <th>{{ $data->pembimbing }}</th>
                <th>{{ $data->mahasiswa }}</th>
            </tr>
            @foreach ($chats as $chat)
                <tr>
                    <td>{{ $chat->time }}</td>
                    <td> 
                        @if ($chat->sender_id == $data->pembimbing_id)
                            @if ($chat->path_img)
                                {{ $chat->getImg() }}    
                            @else
                                {{ $chat->message }}
                            @endif
                        @endif 
                    </td>
                    <td>
                        @if ($chat->sender_id != $data->pembimbing_id)
                            @if ($chat->path_img)
                                {{ $chat->getImg() }}    
                            @else
                                {{ $chat->message }}
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</body>
</html>