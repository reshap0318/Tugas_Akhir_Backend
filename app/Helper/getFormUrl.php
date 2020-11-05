<?php

namespace App\Helper;

class getFormUrl
{
    public static function get($link)
    {
        try {
            $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
            $context = stream_context_create($opts);
            $strDsn = file_get_contents($link, false, $context);
            // ;
            return json_decode($strDsn);

        } catch (\Exception $th) {
            return false;
        }
    }
}