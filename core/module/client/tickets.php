<?php

$title .= $languageArray["tickets.title"];

if( $_SESSION["neira_userlogin"] != 1  || $settings["ticket_system"] == 1  || $user["client_type"] == 1  ){
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

if( $settings["ticket_system"] == 1 ){
  Header("Location:".site_url(''));
}

if( !route(1) ){
    

    $orders = $conn->prepare("SELECT * FROM ticket_subjects ORDER BY subject_id ASC");
    $orders-> execute(array( ));
    $orders = $orders->fetchAll(PDO::FETCH_ASSOC);

  $ordersList = [];

    foreach ($orders as $order) {
      $o["subject"]    = $order["subject"];
      array_push($ordersList,$o);
    }
  
  $tickets = $conn->prepare("SELECT * FROM tickets WHERE client_id=:c_id ORDER BY lastupdate_time DESC ");
  $tickets-> execute(array("c_id"=>$user["client_id"]));
  $tickets = $tickets->fetchAll(PDO::FETCH_ASSOC);
  $ticketList = [];
    foreach ($tickets as $ticket) {
      foreach ($ticket as $key => $value) {
        if( $key == "status" ){
          $t[$key] = $languageArray["tickets.status.".$value];
        }else{
          $t[$key] = $value;
        }
      }
      array_push($ticketList,$t);
    }

  if( $_POST ){
    foreach ($_POST as $key => $value) {
      $_SESSION["data"][$key]  = $value;
    }
    $subject  = htmlspecialchars($_POST["subject"]);
    $message  = htmlentities($_POST["message"]);
      if( empty($subject) ){
        $error    = 1;
        $errorText= $languageArray["error.tickets.new.subject"];
      }elseif( strlen(str_replace(' ','',$message)) < 10 ){
        $error    = 1;
        $errorText= str_replace("{length}","10",$languageArray["error.tickets.new.message.length"]);
      }elseif( open_ticket($user["client_id"]) >= $settings["max_ticket"] ){
        $error    = 1;
        $errorText= str_replace("{limit}",$settings["max_ticket"],$languageArray["error.tickets.new.limit"]);
      }else{
        $conn->beginTransaction();
        $insert = $conn->prepare("INSERT INTO tickets SET client_id=:c_id, subject=:subject, time=:time, lastupdate_time=:last_time ");
        $insert = $insert->execute(array("c_id"=>$user["client_id"],"subject"=>$subject,"time"=>date("Y.m.d H:i:s"),"last_time"=>date("Y.m.d H:i:s") ));
          if( $insert ){ $ticket_id = $conn->lastInsertId(); }
          
        $insert2= $conn->prepare("INSERT INTO ticket_reply SET ticket_id=:t_id, message=:message, time=:time ");
        $insert2= $insert2->execute(array("t_id"=>$ticket_id,"message"=>$message,"time"=>date("Y.m.d H:i:s")));
        
      
        $post = $conn->prepare("SELECT * FROM ticket_subjects WHERE subject=:subject and auto_reply=:auto_reply");
        $post->execute(array("subject"=>$subject,"auto_reply"=>1));
        $post = $post->fetch(PDO::FETCH_ASSOC); 

        $insert3= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
        $insert3= $insert3->execute(array("c_id"=>$user["client_id"],"action"=>"New support request created#".$ticket_id,"ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
       
      if($post){

        $insert4= $conn->prepare("INSERT INTO ticket_reply SET ticket_id=:t_id, support=:support, message=:message, time=:time ");
        $insert4= $insert4->execute(array("t_id"=>$ticket_id,"support"=>2,"message"=>$post["content"],"time"=>date("Y.m.d H:i:s")));
          
        $insert5= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
        $insert5= $insert5->execute(array("c_id"=>$user["client_id"],"action"=>"Support request <strong>Automatic</strong> answered as. ID:".$ticket_id,"ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
      }
        if( $insert && $insert2 && $insert3 ):
          unset($_SESSION["data"]);
          header('Location:'.site_url('tickets/').$ticket_id);
          $conn->commit();
     if( $settings["alert_newticket"] == 2 ):
            if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
            if( $sendsms ):
              SMSUser($settings["admin_telephone"],"On your website #".$ticket_id." There is a new support request with id.");
            endif;
            if( $sendmail ):
              sendMail(["subject"=>"New support request available.","body"=>"On your website #".$ticket_id."There is a new support request with id.","mail"=>$settings["admin_mail"]]);
            endif;
          endif;
        else:
          $error    = 1;
          $errorText= $languageArray["error.tickets.new.fail"];
          $conn->rollBack();
        endif;
      }
  }

}elseif( route(1) && preg_replace('/[^0-9]/', '', route(1)) && !preg_replace('/[^a-zA-Z]/', '', route(1))  ){
  $templateDir  = "viewticket";
  
  if(new_ticket($user['client_id'])){
    $ticketUpdate = $conn->prepare("UPDATE tickets SET support_new=:new WHERE client_id=:c_id && ticket_id=:t_id ");
    $ticketUpdate-> execute(array("c_id"=>$user["client_id"], "new"=>1, "t_id"=>route(1) ));
  }

  $messageList  = $conn->prepare("SELECT * FROM ticket_reply WHERE ticket_id=:t_id ");
  $messageList  -> execute(array("t_id"=>route(1)));
  $messageList  = $messageList->fetchAll(PDO::FETCH_ASSOC);
  $ticketList = $conn->prepare("SELECT * FROM tickets WHERE client_id=:c_id && ticket_id=:t_id ");
  $ticketList-> execute(array("c_id"=>$user["client_id"], "t_id"=>route(1) ));
  $ticketList = $ticketList->fetch(PDO::FETCH_ASSOC);
  $messageList["ticket"]  = $ticketList;
       
if ($ticketList <> true ){
  include 'themes/404.php';
			exit;
		}
  if( $_POST ){
    foreach ($_POST as $key => $value) {
      $_SESSION["data"][$key]  = $value;
    }
   $message  = htmlspecialchars($_POST["message"]);
      if( strlen(str_replace(' ','',$message)) < 5 ){
        $error    = 1;
        $errorText= str_replace("{length}","5",$languageArray["error.tickets.read.message.length"]);
      }elseif( $ticketList["canmessage"] == 1 ){
        $error    = 1;
        $errorText= $languageArray["error.tickets.read.message.cant"];
      }else{
        $conn->beginTransaction();
        $update = $conn->prepare("UPDATE tickets SET lastupdate_time=:last_time, status=:status, client_new=:new WHERE ticket_id=:t_id ");
        $update = $update->execute(array("last_time"=>date("Y.m.d H:i:s"),"t_id"=>route(1),"new"=>2,"status"=>"pending" ));
        $insert = $conn->prepare("INSERT INTO ticket_reply SET ticket_id=:t_id, message=:message, time=:time ");
        $insert = $insert->execute(array("t_id"=>route(1),"message"=>$message,"time"=>date("Y.m.d H:i:s")));
        $insert3= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
        $insert3= $insert3->execute(array("c_id"=>$user["client_id"],"action"=>"Support request responded #".route(1),"ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
        if( $update && $insert && $insert3 ):
          unset($_SESSION["data"]);
          $conn->commit();
          header("Location:".site_url('tickets/').route(1));
        else:
          $error    = 1;
          $errorText= $languageArray["error.tickets.read.fail"];
          $conn->rollBack();
        endif;
      }
  }

}elseif( route(1) && preg_replace('/[^a-zA-Z]/', '', route(1))  ){
  include 'themes/404.php';
  die();
}