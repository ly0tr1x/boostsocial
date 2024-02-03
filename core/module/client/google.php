<?php

if($settings["google"]==1){
        require PATH.'/core/temp/404.php';

 }
function sir($username){
    global $conn;
    
     $row    = $conn->prepare("SELECT * FROM clients WHERE username=:username");
        $row  -> execute(array("username"=>$username));
        $row    = $row->fetch(PDO::FETCH_ASSOC);
        return $row['client_id'];
       
}

function generateRandomUsernamePassword($usernameLength = 8, $passwordLength = 12) {
     $usernameChars = 'abcdefghijklmnopqrstuvwxyz0123456789';
     $passwordChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-={}[]|:;"<>,.?/';

     $username = '';
     $password = '';

     for ($i = 0; $i < $usernameLength; $i++) {
        $username .= $usernameChars[rand(0, strlen($usernameChars) - 1)];
     }

     for ($i = 0; $i < $passwordLength; $i++) {
        $password .= $passwordChars[rand(0, strlen($passwordChars) - 1)];
     }

     return array('username' => $username, 'password' => $password);
}
function generateApiKeys() {
  $length = 32;
  $bytes = openssl_random_pseudo_bytes($length, $strong);
  $hex = bin2hex($bytes);
  return $hex;
}
function convertEmailToUsername($email) {
   $username = strstr($email, '@', true);
  
   $username = str_replace('.', '', $username);
  
  return $username;
}
 $client = new Google_Client();
 $client->setClientId($settings["gsecret"]);
 $client->setClientSecret($settings["gkey"]);
 $client->setRedirectUri(site_url("google"));

 $client->addScope("email");
$client->addScope("profile");


if(isset($_REQUEST['code'])):

    $token = $client->fetchAccessTokenWithAuthCode($_REQUEST['code']);

    if(!isset($token["error"])){

        $client->setAccessToken($token['access_token']);

         $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
            $_SESSION['login_id'] = $id; 
           $name= $google_account_info->name;
 
 
  $email=$google_account_info->email;
  
        if(userdata_check("email",$email) ){
          $row    = $conn->prepare("SELECT * FROM clients WHERE email=:email");
        $row  -> execute(array("email"=>$email));
        $row    = $row->fetch(PDO::FETCH_ASSOC);
        $access = json_decode($row["access"],true);

        unset($_SESSION["recaptcha"]);

        $_SESSION["neira_userlogin"]      = 1;
        $_SESSION["neira_userid"]         = $row["client_id"];
        $_SESSION["neira_userpass"]       = $row["password"];
        $_SESSION["recaptcha"]                = false;
        if( $access["admin_access"] ):
            $_SESSION["neira_adminlogin"] = 1;
        endif;
        if( $remember ){
            if($access["admin_access"]):
                setcookie("a_login", 'ok', strtotime('+7 days'), '/', null, null, true);
            endif;
            setcookie("u_id", $row["client_id"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_password", $row["password"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_login", 'ok', strtotime('+7 days'), '/', null, null, true);
        }else{
            setcookie("u_id", $row["client_id"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_password", $row["password"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_login", 'ok', strtotime('+7 days'), '/', null, null, true );
        }
        
        header('Location:'.site_url(''));
        $insert = $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
        $insert->execute(array("c_id"=>$row["client_id"],"action"=>"Member logged in.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
        $update = $conn->prepare("UPDATE clients SET login_date=:date, login_ip=:ip WHERE client_id=:c_id ");
        $update->execute(array("c_id"=>$row["client_id"],"date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
     
    }else{
       $credentials = generateRandomUsernamePassword();
$username= convertEmailToUsername($email);
$pass= $credentials['password'];

  $referral       = $_SESSION['referral'];

        if( userdata_check("email",$email) ){
          header('Location:'.site_url(''));
   
   
     }else{
        
    $apikey = generateApiKeys();
    $referral_code = substr(md5(microtime()),rand(0,26),5);
   
    $conn->beginTransaction();
    $insert = $conn->prepare("INSERT INTO clients SET 
       first_name=:first_name,
       username=:username,
       email=:email,
       password=:password,
       register_date=:date,
       apikey=:key,
       timezone=:timezone,
       referral=:referral,
       referral_code=:referral_code
    ");
$insert = $insert->execute(array(
        "first_name"=>$name,
        "username"=>$username,
        "email"=>$email,
        "password"=>md5(sha1(md5($pass))),
        "date"=>date("Y.m.d H:i:s"),
        'key'=>$apikey,
        "timezone"=>$settings["site_timezone"],
        "referral"=>$referral,
        "referral_code"=>$referral_code
));
      if( $insert ): 
                  $conn->commit();

          $client_id = sir($username); 
          
       $_SESSION["neira_userlogin"]      = 1;
        $_SESSION["neira_userid"]         = $client_id;
        $_SESSION["neira_userpass"]       = md5(sha1(md5($pass)));
        $_SESSION["recaptcha"]                = false;
         
      
            
            setcookie("u_id", $client_id, strtotime('+7 days'), '/', null, null, true);
            setcookie("u_password", md5(sha1(md5($pass))), strtotime('+7 days'), '/', null, null, true);
            setcookie("u_login", 'ok', strtotime('+7 days'), '/', null, null, true );
     
       
           $insert = $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
        $insert->execute(array("c_id"=>$client_id,"action"=>"Login with google.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
               header('Location:'.site_url(''));
else:
            $conn->rollBack();
               header('Location:'.site_url(''));

          endif;
  
        
          
    }
 
    }
   
            }
            else{
        header('Location:'.site_url(''));
 }

     
    
else: 
 
                    header('location:'.$client->createAuthUrl());

 

  endif; ?>