<?php

$title .= $languageArray["account.title"];

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

$user["apikey"] = private_str($user["apikey"], 10, 12);

if(isset($_SESSION["apikey_success"])):
    $success    = 1;
    $successText= "API key has been generated: <br>".$_SESSION["apikey_success"];
    unset($_SESSION["apikey_success"]);
endif;    

if( route(1) == "currency_preferred" ){
    
  
$conn->beginTransaction();
    $id = route(2);
    $update = $conn->prepare("UPDATE clients SET currency=:type WHERE client_id=:id ");
    $update = $update->execute(array("id"=>$user["client_id"],"type"=>$id ));
    $conn->commit();
header("Location:".site_url(@$_GET['url'] =='neworder' ? '' : $_GET['url']));
 } 
 
if( route(1) == "newapikey" ){
    $conn->beginTransaction();
    $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
    $insert= $insert->execute(array("c_id"=>$user["client_id"],"action"=>"API Key değiştirildi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
    $apikey = CreateApiKey(["email"=>$user["email"],"username"=>$user["username"]]);
    $update = $conn->prepare("UPDATE clients SET apikey=:key WHERE client_id=:id ");
    $update = $update->execute(array("id"=>$user["client_id"],"key"=>$apikey ));
    if( $update && $insert ):
      $conn->commit();
      $_SESSION["apikey_success"] = $apikey;
    else:
      $conn->rollBack();
    endif;
    header('Location:'.site_url('account'));
}elseif( route(1) == "change_lang" && $_POST ){
    $lang     = $_POST["lang"];
    
     $_SESSION['lang'] = $lang;
     
    if($user['auth']){
    $update = $conn->prepare("UPDATE clients SET lang=:lang WHERE client_id=:id ");
    $update = $update->execute(array("id"=>$user["client_id"],"lang"=>$lang ));
     header("Location:".site_url('account'));
    }
    else{
          header("Location:".site_url(''));
    }
    
   
   
}elseif( route(1) == "timezone" && $_POST ){
    $timezone = $_POST["timezone"];
    $update   = $conn->prepare("UPDATE clients SET timezone=:timezone WHERE client_id=:id ");
    $update   = $update->execute(array("id"=>$user["client_id"],"timezone"=>$timezone ));
    header("Location:".site_url('account'));
}elseif( route(0) == "account" && $_POST ){

  $pass     = $_POST["current_password"];
  $new_pass = $_POST["password"];
  $new_again= $_POST["confirm_password"];

  if( !userdata_check('password',md5(sha1(md5($pass)))) ){
    $error    = 1;
    $errorText= $languageArray["error.account.password.notmach"];
  }elseif( strlen($new_pass) < 8 ){
    $error    = 1;
    $errorText= $languageArray["error.account.password.length"];
  }elseif( $new_pass != $new_again ){
    $error    = 1;
    $errorText= $languageArray["error.account.passwords.notmach"];
  }else{
    $conn->beginTransaction();
      $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
      $insert= $insert->execute(array("c_id"=>$user["client_id"],"action"=>"User password changed","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
      $update = $conn->prepare("UPDATE clients SET password=:pass WHERE client_id=:id ");
      $update = $update->execute(array("id"=>$user["client_id"],"pass"=>md5(sha1(md5($new_pass))) ));
        if( $update && $insert ):
          $_SESSION["neira_userpass"]       = md5(sha1(md5($new_pass)));
          setcookie("u_password", md5(sha1(md5($new_pass))), time()+(60*60*24*7), '/', null, null, true );

          $conn->commit();
          $success    = 1;
          $successText= $languageArray["error.account.password.success"];

        else:
          $conn->rollBack();
          $error    = 1;
          $errorText= $languageArray["error.account.password.fail"];
        endif;
  }

}
