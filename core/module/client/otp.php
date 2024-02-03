<?php

$email= $_SESSION['email'];

   if(route(1)== "send"){
       $otp_code = rand(100000, 999999);

    $_SESSION['otp'] = $otp_code;
 
   
       if (send_email($email, $otp_code)) {
        echo json_encode(['status' => 'success']);
     } else {
        echo json_encode(['status' => 'error']);
 
    }
   
    }
if($_SESSION['otp']){
    
 
 
    if($_POST){
     if(isset($_SESSION['otp']) && $_POST["otp"] == $_SESSION['otp']){
    $otp=true;  
  }else{
    
$otp=false;
 	
     
}
      if ($otp) {
          
          $row    = $conn->prepare("SELECT * FROM clients WHERE email=:email");
    $row  -> execute(array("email"=>$email  ));
    $row    = $row->fetch(PDO::FETCH_ASSOC);
    $access = json_decode($row["access"],true);

          
                 $_SESSION["neira_adminlogin"] = 1;
	    $_SESSION["neira_userlogin"]      = 1;
	    $_SESSION["neira_userid"]         = $row["client_id"];
	   
 	    $_SESSION["recaptcha"]                = false;
	
	      if( $access["admin_access"] ):
	        setcookie("a_login", 'ok', time()+(60*60*24*7), '/', null, null, true );
	      endif;
	      setcookie("u_id", $row["client_id"], time()+(60*60*24*7), '/', null, null, true );
	      setcookie("u_password", $row["password"], time()+(60*60*24*7), '/', null, null, true );
	      setcookie("u_login", 'ok', time()+(60*60*24*7), '/', null, null, true );
	    
 
	    
 	      $insert = $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
	      $insert->execute(array("c_id"=>$row["client_id"],"action"=>"Admin logged in.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
	      $update = $conn->prepare("UPDATE clients SET login_date=:date, login_ip=:ip WHERE client_id=:c_id ");
	      $update->execute(array("c_id"=>$row["client_id"],"date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
          
          	    unset($_SESSION['otp']);
	    header('Location:'.site_url('admin'));

          
      
      }else{
          	$error      = 1;
    	$errorText  = "OTP INVALID.";
    
      }
    }
          require admin_view(otp);
          
}else{
    require PATH.'/core/temp/404.php';
    
}
