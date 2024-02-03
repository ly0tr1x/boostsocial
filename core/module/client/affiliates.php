<?php

$title .= $languageArray["affiliates.title"];

if( $_SESSION["neira_userlogin"] != 1  || $user["client_type"] == 1  ){
    Header("Location:".site_url('logout'));
}



if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }
    endif;
    
if( $settings["referral"] == 1  ){
  include 'themes/404.php';
  die();
}
