<?php

$title .= $languageArray["verify.title"];

if( $_SESSION["neira_userlogin"] != 1  && ( $settings["sms_verify"] == 1 || $settings["mail_verify"] == 1  || $user["client_type"] == 1)  ){
    header("Location:".site_url('logout'));
    die;
  }
  

  if(!route(1)){
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }
  }

  $search = $conn->prepare("SELECT * FROM verify_log WHERE token=:token && type=:type");
   $search->execute(array("token"=>route(1),"type"=>1 ));

if(route(1) == "mail" && $user["mail_verify"] != 2){
        $verify = true;

}elseif(route(1) == "sms" && $user["sms_verify"] != 2){
        $verify = false;
}

if(route(1) == "mail" && $_POST && $user["mail_verify"] != 2):
    

    $captcha        = $_POST['g-recaptcha-response'];
    $googlesecret   = $settings["recaptcha_secret"];
    $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    $captcha_control= json_decode($captcha_control);
      if( $settings["recaptcha"] == 2 && $captcha_control->success == false ):
        $error      = 1;
        $errorText  = $languageArray["error.resetpassword.recaptcha"];
      else:
  
        $token   = CreateApiKey($_POST);
        $token .=  substr(md5(microtime()),rand(0,26),5);
        
          $send = sendMail(["subject"=>"Verify your account.","body"=>"Your verification link : ".site_url('verify/'.$token),"mail"=>$row["email"]]);
  
        if( $send ):  

          $insert = $conn->prepare("INSERT INTO verify_log SET client_id=:c_id, token=:token, type=:type, verify=:verify ");
          $insert->execute(array("c_id"=>$user["client_id"],"token"=>$token,"type"=>1,"verify"=>"mail" ));
          $success    = 1;
          $successText= $languageArray["error.verify.success"];
         else:
          $error      = 1;
          $errorText  = $languageArray["error.verify.fail"];
        endif;
  
      endif;
  

elseif(route(1) == "sms" && $_POST && $user["sms_verify"] != 2):

    $captcha        = $_POST['g-recaptcha-response'];
    $googlesecret   = $settings["recaptcha_secret"];
    $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    $captcha_control= json_decode($captcha_control);
      if( $settings["recaptcha"] == 2 && $captcha_control->success == false ):
        $error      = 1;
        $errorText  = $languageArray["error.resetpassword.recaptcha"];
      else:

        $token   = CreateApiKey($_POST);
        $token .=  substr(md5(microtime()),rand(0,26),5);
     
      $send = SMSUser($user["telephone"],"Your verification link : ".site_url('verify/'.$token));

        if( $send ):  

          $insert = $conn->prepare("INSERT INTO verify_log SET client_id=:c_id, token=:token, type=:type, verify=:verify ");
          $insert->execute(array("c_id"=>$user["client_id"],"token"=>$token,"type"=>1,"verify"=>"sms" ));

          $success    = 1;
          $successText= $languageArray["error.verify.success"];
         else:
          $error      = 1;
          $errorText  = 'Unsuccessful'.$languageArray["error.verify.fail"];
        endif;
  
      endif;
  
elseif(route(1) == "edit" && $_POST):
    
    if($_POST["telephone"]):
    
        $phone = htmlspecialchars($_POST["telephone"]);
    
        if( empty($phone) ){
            $error      = 1;
            $errorText  = $languageArray["error.verify.empty"];
        }elseif( userdata_check("telephone",$phone) ){
            $error      = 1;
            $errorText  = $languageArray["error.signup.telephone.used"];
        }else{    
            $update = $conn->prepare("UPDATE clients SET telephone=:telephone WHERE client_id=:id ");
            $update = $update->execute(array("id"=>$user["client_id"],"telephone"=>$phone));
            header("Location:".site_url("verify"));
        }
        
    endif;

    if($_POST["email"]):
    
        $email = htmlspecialchars($_POST["email"]);
        
        if(empty($email)){
            $error      = 1;
            $errorText  = $languageArray["error.verify.empty"]; 
        }elseif( !email_check($email) ){
            $error      = 1;
            $errorText  = $languageArray["error.signup.email"];
        }elseif( userdata_check("email",$email) ){
            $error      = 1;
            $errorText  = $languageArray["error.signup.email.used"];
        }else{
            $update = $conn->prepare("UPDATE clients SET email=:email WHERE client_id=:id ");
            $update = $update->execute(array("id"=>$user["client_id"],"email"=>$email));
            header("Location:".site_url("verify"));
        }
        
    endif;
    
elseif(route(1) && $search->rowCount()):
    $search      = $search->fetch(PDO::FETCH_ASSOC);


    if($search["verify"] == "sms"){
        $update = $conn->prepare("UPDATE clients SET sms_verify=:sms_verify WHERE client_id=:id ");
        $update = $update->execute(array("id"=>$search["client_id"],"sms_verify"=>2));   
    }elseif($search["verify"] == "mail"){
        $update = $conn->prepare("UPDATE clients SET mail_verify=:mail_verify WHERE client_id=:id ");
        $update = $update->execute(array("id"=>$search["client_id"],"mail_verify"=>2));   
    }
    
    $update = $conn->prepare("UPDATE verify_log SET type=:type WHERE token=:token ");
    $update->execute(array("type"=>2,"token"=>route(1) ));
    
    header("Location:".site_url());

endif;