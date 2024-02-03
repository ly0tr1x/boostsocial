<?php

if ($user["access"]["update-prices"] != 1):
    header("Location:" . site_url("admin"));
    exit();
endif;
  

  if( $_SESSION["client"]["data"] ):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
      $$key = $value;
    }
    unset($_SESSION["client"]);
  endif;
$services = $conn->prepare("SELECT * FROM services ");
 $services->execute(array());
 $services = $services->fetchAll(PDO::FETCH_ASSOC);
foreach($services as $service):

   if( $_POST ):
  
$new_profit     = $_POST["new_profit"];
$type     = $_POST["type"];
if( $type == 1 ) {

if( $service["service_api"] == 0 ):
     
    

$service_price = $service["service_price"];
  $service_id =  $service["service_id"];
     $service_api_price2 = $service["service_price"];
 $final = $service_api_price2 +($service_api_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array("service_id" => $service_id , "service_price" => $final));
   


else:
     
$extras = json_decode($service["api_detail"],true);
 $service_price = $service["service_price"];
 $service_api_price = $extras["rate"];
 $service_id =  $service["service_id"];
 $service_api_price2 = $service_api_price;
 $final = $service_api_price2 +($service_api_price2 * ($new_profit/100));
 
    try{
     
       
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array("service_id" => $service_id , "service_price" => $final));

   
    }
    catch(\Exception $e){
        echo $e;
        exit;
    }
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;

endif;

} elseif( $type == 2) {

if( $service["service_api"] == 0 ):


$service_price = $service["service_price"];
  $service_id =  $service["service_id"];
     $service_manual_price2 = $service["service_price"];
 $final = $service_manual_price2 +($service_manual_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array("service_id" => $service_id , "service_price" => $final));
  
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;

endif;
 



} elseif( $type == 3) {

if( $service["service_api"] == 0 ):


   

else:
$extras = json_decode($service["api_detail"],true);
 $service_price = $service["service_price"];
 $service_api_price = $extras["rate"];
 $service_id =  $service["service_id"];
 $service_api_price2 = $service_api_price;
 $final = $service_api_price2 +($service_api_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array( "service_id" => $service_id , "service_price" => $final));
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;
endif;
} 

 endif;
 if( route(2) == "decrease" ):

if( $_POST ):
$new_profit     = $_POST["new_profit"];
$type     = $_POST["type"];
if( $type == 1 ) {

if( $service["service_api"] == 0 ):


$service_price = $service["service_price"];
  $service_id =  $service["service_id"];
     $service_api_price2 = $service["service_price"];
 $final = $service_api_price2 -($service_api_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array("service_id" => $service_id , "service_price" => $final));
   

else:
$extras = json_decode($service["api_detail"],true);
 $service_price = $service["service_price"];
 $service_api_price = $extras["rate"];
 $service_id =  $service["service_id"];
 $service_api_price2 = $service_api_price;
 $final = $service_api_price2 -($service_api_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array( "service_id" => $service_id , "service_price" => $final));
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;
endif;

} elseif( $type == 2) {

if( $service["service_api"] == 0 ):


$service_price = $service["service_price"];
  $service_id =  $service["service_id"];
     $service_manual_price2 = $service["service_price"];
 $final = $service_manual_price2 -($service_manual_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array("service_id" => $service_id , "service_price" => $final));
  
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;

endif;
 



} elseif( $type == 3) {

if( $service["service_api"] == 0 ):


   

else:
$extras = json_decode($service["api_detail"],true);
 $service_price = $service["service_price"];
 $service_api_price = $extras["rate"];
 $service_id =  $service["service_id"];
 $service_api_price2 = $service_api_price;
 $final = $service_api_price2 -($service_api_price2 * ($new_profit/100));
 
      $update = $conn->prepare("UPDATE services SET service_price=:service_price WHERE service_id=:service_id");
     $update->execute(array( "service_id" => $service_id , "service_price" => $final));
$referrer = site_url("admin/update-prices");
                $icon = "success";
                $error = 1;
                $errorText = "Success";
                
                header("Location:".site_url("admin/update-prices"));
                echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);

              if( $update ):
                header("Location:" . site_url("admin/update-prices"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Successful";
              else:
                $errorText  = "Failed";
                $error      = 1;
				
              endif;
endif;
} 

 endif;
endif;



endforeach;

  require admin_view('update-prices');
