<?php

$title .= $languageArray["resetpassword.title"];

$captcha = true;

if( $_SESSION["neira_userlogin"] == 1  || $user["client_type"] == 1 || $settings["resetpass_page"] == 1  ){
  Header("Location:".site_url());
}

$resetType  = array();
if( $settings["resetpass_sms"] == 2 ):
$resetType[] = ["type"=>"sms","name"=>$languageArray["resetpassword.type.sms"]];
endif;
if( $settings["resetpass_email"] == 2 ):
$resetType[] = ["type"=>"email","name"=>$languageArray["resetpassword.type.email"]];
endif;
   $resetStep = 1;

if(route(1)){
   $search = $conn->prepare("SELECT * FROM reset_log WHERE token=:token && type=:type");
   $search->execute(array("token"=>route(1),"type"=>1 ));
   $resetStep = 2;
}

if( !route(1) && $_POST ):

  $captcha        = $_POST['g-recaptcha-response'];
  $googlesecret   = $settings["recaptcha_secret"];
  $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
  $captcha_control= json_decode($captcha_control);
  $user = htmlentities($_POST["user"]);
  $type = htmlentities($_POST["type"]);
    $row= $conn->prepare("SELECT * FROM clients WHERE username=:username || telephone=:tel ");
    $row->execute(array("username"=>$user,"tel"=>$user));
    if( empty($user) ):
      $error      = 1;
      $errorText  = $languageArray["error.resetpassword.user.empty"];
    elseif( !$row->rowCount() ):
      $error      = 1;
      $errorText  = $languageArray["error.resetpassword.user.notmatch"];
    elseif( $settings["recaptcha"] == 2 && $captcha_control->success == false ):
      $error      = 1;
      $errorText  = $languageArray["error.resetpassword.recaptcha"];
    else:
      $row    = $row->fetch(PDO::FETCH_ASSOC);

    $token   = CreateApiKey($_POST);
    $token .=  substr(md5(microtime()),rand(0,26),5);
      
      if( $type == "sms" ):
        $send = SMSUser($row["telephone"],"To change your password; ".site_url("resetpassword/$token"));
      endif;
      if( $type == "email" ):
        $send = sendMail(["subject"=>"I forgot my password.","body"=>"Your verification code to change your password : ".site_url("resetpassword/$token"),"mail"=>$row["email"]]);
      endif;

      if( $send ):  
        $insert = $conn->prepare("INSERT INTO reset_log SET client_id=:c_id, token=:token, type=:type ");
        $insert->execute(array("c_id"=>$row["client_id"],"token"=>$token,"type"=>1 ));
      
        $success    = 1;
        $successText= $languageArray["error.resetpassword.success"];
        echo '<script>setInterval(function(){window.location="'.site_url('').'"},2000)</script>';
       else:
        $error      = 1;
        $errorText  = $languageArray["error.resetpassword.fail"];
      endif;

    endif;

elseif(route(1)):
   if($search->rowCount()):
   
    if($_POST):
         $search      = $search->fetch(PDO::FETCH_ASSOC);
  $new_pass = $_POST["password"];
  $new_again= $_POST["confirm_password"];

  if( strlen($new_pass) < 8 ){
    $error    = 1;
    $errorText= $languageArray["error.account.password.length"];
  }elseif( $new_pass != $new_again ){
    $error    = 1;
    $errorText= $languageArray["error.account.passwords.notmach"];
  }else{
    $conn->beginTransaction();
    $insert = $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
    $insert->execute(array("c_id"=>$search["client_id"],"action"=>"Password reset has been done.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            
            
    $update = $conn->prepare("UPDATE clients SET password=:pass WHERE client_id=:id ");
    $update = $update->execute(array("id"=>$search["client_id"],"pass"=>md5(sha1(md5($new_pass))) ));

      
        if( $update  && $insert ):
           $update = $conn->prepare("UPDATE reset_log SET type=:type WHERE token=:token ");
           $update->execute(array("type"=>2,"token"=>route(1) ));
    
          $conn->commit();
          $success    = 1;
          $successText= $languageArray["error.account.password.success"];
          echo '<script>setInterval(function(){window.location="'.site_url('').'"},2000)</script>';
        else:
          $conn->rollBack();
          $error    = 1;
          $errorText= $languageArray["error.account.password.fail"];
        endif;
  }
        
    endif;
    
    else:
        header("Location:".site_url('resetpassword'));
        
    endif;

endif;
