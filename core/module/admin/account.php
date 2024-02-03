<?php

if( $_POST ){

  $pass     = $_POST["current_password"];
  $new_pass = $_POST["password"];
  $new_again= $_POST["confirm_password"];
if(empty($pass) || empty($new_pass) || empty($new_again)){
         $error    = 1;
    $errorText= "Do not leave any blank space.";
  }elseif( !userdata_check('password',md5(sha1(md5($pass)))) ){
    $error    = 1;
    $errorText= "Your old password is incorrect.";
  }elseif( strlen($new_pass) < 8 ){
    $error    = 1;
    $errorText= "Your password is too short.";
  }elseif( $new_pass != $new_again ){
    $error    = 1;
    $errorText= "The new passwords did not match.";
  }else{
      $update = $conn->prepare("UPDATE clients SET password=:pass WHERE client_id=:id ");
      $update = $update->execute(array("id"=>$user["client_id"],"pass"=>md5(sha1(md5($new_pass))) ));
	  header("Location:".site_url("admin"));
  }
}

require admin_view('account');