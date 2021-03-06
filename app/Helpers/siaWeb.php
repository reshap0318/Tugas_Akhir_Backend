<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;

class siaWeb
{
    static $webToken = "HOuauWbbkU8E9a9GIeUd";

    public static function get($link)
    {
        try {
            $baseurl = "http://127.0.0.1:1234";
            $opts = array('http'=>array(
                'header' => "User-Agent:MyAgent/1.0\r\n"
            ));
            
            $opts['http']['header'] = $opts['http']['header'] . "Authorization: Bearer ".self::$webToken."\r\n";
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($baseurl.'/'.$link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $th) {
            return false;
        }
    }

    public static function post($link, $data)
    {
        try {
            $baseurl = "http://127.0.0.1:1234";
            $query = http_build_query($data);
            $opts = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method'  => "POST",
                    'content' => $query,
                ),
            );
            $opts['http']['header'] = $opts['http']['header'] . "Authorization: Bearer ".self::$webToken."\r\n";
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($baseurl.'/'.$link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function mDelete($link, $data)
    {
        try {
            $baseurl = "http://127.0.0.1:1234";
            $query = http_build_query($data);
            $opts = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method'  => "DELETE",
                    'content' => $query,
                ),
            );
            $opts['http']['header'] = $opts['http']['header'] . "Authorization: Bearer ".self::$webToken."\r\n";
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($baseurl.'/'.$link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $th) {
            return false;
        }
    }

    public static function sendMail($dataEmail, $dataChat, $pdfformat=false)
    {  
        if($pdfformat){
            $pdf = PDF::loadView('send.pdfTemplatePeriod', array('data' => $dataEmail, 'chats'=>$dataChat));
        }else{
            $pdf = PDF::loadView('send.pdfTemplate', array('data' => $dataEmail, 'chats'=>$dataChat));
        }
        $pdf->setPaper('a4', 'landscape');
        Mail::send('send.emailTemplate', array('data' => $dataEmail), function($message)use($dataEmail, $pdf) {
            $message->to($dataEmail->to, $dataEmail->to)
                    ->subject($dataEmail->title)
                    ->attachData($pdf->output(), $dataEmail->titlePdf);
        });
        return "Berhasil Mengirim Email";
    }
}