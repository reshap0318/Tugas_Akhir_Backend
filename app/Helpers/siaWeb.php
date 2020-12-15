<?php

namespace App\Helpers;

class siaWeb
{
    public static function get($link)
    {
        try {
            $baseurl = "http://127.0.0.1:1234";
            $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
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
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                                "Content-Length: ".strlen($query)."\r\n".
                                "User-Agent:MyAgent/1.0\r\n",
                    'method'  => "POST",
                    'content' => $query,
                ),
            );
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($baseurl.'/'.$link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $th) {
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
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                                "Content-Length: ".strlen($query)."\r\n".
                                "User-Agent:MyAgent/1.0\r\n",
                    'method'  => "DELETE",
                    'content' => $query,
                ),
            );
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($baseurl.'/'.$link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $th) {
            return false;
        }
    }
}