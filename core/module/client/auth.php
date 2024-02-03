<?php

$title = $settings["site_title"];

if( !route(1) ){
    $route[1] = "login";
}

if( $settings['resetpass_page'] == 1 ){
    $resetPage = false;
  }elseif( $settings['resetpass_page'] == 2 ){
    $resetPage = true;
  }



if( $_SESSION["neira_userlogin"] ){
     Header("Location:".site_url());
}

if(route(1) !== 'login') {
    header("Location:".site_url()); 
    exit();
}


if( $route[1] == "login" && $_POST ){
    
    $username       = htmlentities($_POST["username"]);
    $pass           = htmlentities($_POST["password"]);
    $captcha        = $_POST['g-recaptcha-response'];
    $remember       = htmlentities($_POST["remember"]);
    $googlesecret   = $settings["recaptcha_secret"];
    $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    $captcha_control= json_decode($captcha_control);

    if( $settings["recaptcha"] == 2 && $captcha_control->success == false && $_SESSION["recaptcha"]  ){
        $error      = 1;
        $errorText  = $languageArray["error.signin.recaptcha"];
        if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
    }elseif( empty($username) ){
        $error      = 1;
        $errorText  = $languageArray["error.signin.username"];
        if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
    }elseif( !userdata_check("username",$username) ){
        $error      = 1;
        $errorText  = $languageArray["error.signin.username"];
        if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
    }elseif( !userlogin_check($username,$pass) ){
        $error      = 1;
        $errorText  = $languageArray["error.signin.notmatch"];
        if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
    }elseif( countRow(["table"=>"clients","where"=>["username"=>$username,"client_type"=>1]]) ){
        $error      = 1;
        $errorText  = $languageArray["error.signin.deactive"];
        if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
    }else{
        $row    = $conn->prepare("SELECT * FROM clients WHERE username=:username && password=:password ");
        $row  -> execute(array("username"=>$username,"password"=>md5(sha1(md5($pass))) ));
        $row    = $row->fetch(PDO::FETCH_ASSOC);
        $access = json_decode($row["access"],true);

        unset($_SESSION["recaptcha"]);

        $_SESSION["neira_userlogin"]      = 1;
        $_SESSION["neira_userid"]         = $row["client_id"];
        $_SESSION["neira_userpass"]       = md5(sha1(md5($pass)));
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
    }


}
