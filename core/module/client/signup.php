<?php

$title .= $languageArray["signup.title"];

if( $_SESSION["neira_userlogin"]){
  Header("Location:".site_url(''));
}
elseif( $settings["register_page"] == 1 ){
  include 'themes/404.php';
die();
}
  $referral       = $_SESSION['referral'];

  if( $_POST ){
    foreach ($_POST as $key => $value) {
      $_SESSION["data"][$key]  = $value;
    }

  $first_name     = htmlentities($_POST["first_name"]);
  $last_name      = htmlentities($_POST["last_name"]);
  $email          = mb_strtolower(htmlentities($_POST["email"]));
  $username       = mb_strtolower(htmlentities($_POST["username"]));
  $phone          = htmlentities($_POST["telephone"]);
  $pass           = htmlentities($_POST["password"]);
  $pass_again     = htmlentities($_POST["password_again"]);
  $terms          = htmlentities($_POST["terms"]);
  $captcha        = $_POST['g-recaptcha-response'];
  $googlesecret   = $settings["recaptcha_secret"];
  $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
  $captcha_control= json_decode($captcha_control);
  if( $captcha && $captcha_control->success == false ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.recaptcha"];
  }elseif( $settings["name_secret"] == 2 && empty($first_name) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.name"];
  }elseif( $settings["name_secret"] == 2 && empty($last_name) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.name"];
  }elseif( !email_check($email) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.email"];
  }elseif( userdata_check("email",$email) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.email.used"];
  }elseif( !username_check($username) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.username.character"];
  }elseif( userdata_check("username",$username) ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.username.used"];
  }elseif( $settings["skype_area"] == 2 && empty($phone)){
    $error      = 1;
    $errorText  = $languageArray["error.signup.telephone"];
  }elseif( strlen($pass) < 8 ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.password"];
  }elseif( $pass != $pass_again ){
    $error      = 1;
    $errorText  = $languageArray["error.signup.password.notmatch"];
  }elseif( $settings["terms_checkbox"] == 2 && !$terms ){ 
    $error      = 1;
    $errorText  = $languageArray["error.signup.terms"];
  }else{
    $apikey = CreateApiKey($_POST);
    $referral_code = substr(md5(microtime()),rand(0,26),5);
   
    $conn->beginTransaction();
    $insert = $conn->prepare("INSERT INTO clients SET 
    first_name=:first_name,
    last_name=:last_name,
    username=:username,
    email=:email,
    password=:pass,
    lang=:lang,
    telephone=:phone,
    register_date=:date,
    apikey=:key,
    timezone=:timezone,
    referral=:referral,
    referral_code=:referral_code
    ");
    $insert = $insert-> execute(array(
        "lang"=>$selectedLang,
        "first_name"=>$first_name,
        "last_name"=>$last_name,
        "username"=>$username,
        "email"=>$email,
        "pass"=>md5(sha1(md5($pass))),
        "phone"=>$phone,
        "date"=>date("Y.m.d H:i:s"),
        'key'=>$apikey,
        "timezone"=>$settings["site_timezone"],
        "referral"=>$referral,
        "referral_code"=>$referral_code
        ));
    
      if( $insert ): $client_id = $conn->lastInsertId(); endif;
      if( $referral ): $ref = $referral; endif;

    if($settings["free_balance"] == 2):
        $update = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
        $update->execute(array("id"=>$client_id,"balance"=>$settings["free_amount"] ));
    endif;
    
    $insert2= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
    $insert2= $insert2->execute(array("c_id"=>$client_id,"action"=>"User registration made.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));

    
      if( $insert && $insert2 ):
        $conn->commit();
        unset($_SESSION["data"]);
        $success    = 1;
        $successText= $languageArray["error.signup.success"];
        echo '<script>setInterval(function(){window.location="'.site_url('').'"},2000)</script>';
      else:
        $conn->rollBack();
        $error      = 1;
        $errorText  = $languageArray["error.signup.fail"];
      endif;
  }

}

