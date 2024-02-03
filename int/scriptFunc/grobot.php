<?php

function robot($value){ 
        $ch = curl_init($value);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result)) {
            $result = false;
        }
        curl_close($ch);
        return $result;
}

function private_str($str, $start, $end){
    $after = mb_substr($str, 0, $start, 'utf8');
    $repeat = str_repeat('*', $end);
    $before = mb_substr($str, ($start + $end), strlen($str), 'utf8');
    return $after.$repeat.$before;
 }
     