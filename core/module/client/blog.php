<?php

if( countRow(["table"=>"blogs"])<1 ){
  Header("Location:".site_url(''));
}


if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }
    endif;
    
if( !route(1) ){
$title = $languageArray["blog.title"];
  $blogs = $conn->prepare("SELECT * FROM blogs ORDER BY blog_created DESC ");
  $blogs-> execute(array());
  $blogs = $blogs->fetchAll(PDO::FETCH_ASSOC);
  $blogList = [];
    foreach ($blogs as $blog) {
      foreach ($blog as $key => $value) {

          $t[$key] = $value;

      }
      array_push($blogList,$t);
    }

}elseif( route(1) ){
  $templateDir  = "blogpost";
   $blogs = $conn->prepare("SELECT * FROM blogs WHERE url=:url ORDER BY blog_created DESC ");
  $blogs-> execute(array("url"=>route(1)));
  $blogs = $blogs->fetchAll(PDO::FETCH_ASSOC);
  $blogList = [];
    foreach ($blogs as $blog) {
      foreach ($blog as $key => $value) {
        if( $key == "blog_title" ){
          $title = $value;
          $t[$key] = $value;
        }else{
          $t[$key] = $value;
        }

      }
      array_push($blogList,$t);
    }

}

