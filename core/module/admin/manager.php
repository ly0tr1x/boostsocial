<?php

$e_route = route(2);

if($e_route){
    $e_route = $e_route;
}else{
    $e_route = "update";
}

if($e_route == "login" && $_POST && !$_SESSION["glycon_manager"]){

$site_version = VERSION_NUMBER;
$lisan_key_dinamik = DINAMICLISANCE;    
$key = $_POST["key"];



if($keys_key_moto == $key){
    
        $_SESSION['glycon_manager'] = "logined";
        header("Location:" . site_url("admin/manager/update"));

}

}elseif($e_route == "logout"){
    
    unset($_SESSION['glycon_manager']);
    header("Location:" . site_url("admin/manager"));


}elseif($e_route == "update" && $_POST){
    $site_version = VERSION_NUMBER;
$lisan_key_dinamik = DINAMICLISANCE;  

header("Refresh: 0; url=".(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/glyupdate.php?key=".$keys_key_moto);

}elseif($e_route == "optimization"){
    
    
    
function filesCount($klasor) {
    $dizi = array();
    $open = opendir($klasor);
        while($q=readdir($open)) {
            if ($q != "." && $q != "..") {
                $dizi[] = $q;
            }
        }
    $sayi = count($dizi); 
    closedir($open);  
    return $sayi; 
}

$total = filesCount("cache");

if(route(3) == "fresh"){
    
      $oku = opendir("cache");
         
        while ($sonuc = readdir($oku))
        {
         
            $sonuck = explode(".",$sonuc);
            $sonuck = end($sonuck);
             
                if($sonuck == "glycon"){
                    unlink("cache/".$sonuc);
            }
         
         
 
                 
         
         
        }
 
    header("Location:".site_url("admin/manager/optimization"));

}

}elseif($e_route == "guard" && $_POST){
    
    foreach ($_POST as $key => $value) {
                $$key = $value;
              }
         
                $update = $conn->prepare("UPDATE settings SET 
                    guard_system_status=:guard_system_status,
                    guard_services_status=:guard_services_status,
                    guard_services_type=:guard_services_type,
                    guard_notify_status=:guard_notify_status,
                    guard_notify_type=:guard_notify_type,
                    guard_roles_type=:guard_roles_type,
                    guard_roles_status=:guard_roles_status,
                    guard_apikey_type=:guard_apikey_type
                    WHERE id=:id ");
                $update = $update->execute(array(
                    "guard_system_status"=>$guard_system_status,
                    "guard_services_status"=>$guard_services_status,
                    "guard_services_type"=>$guard_services_type,
                    "guard_notify_status"=>$guard_notify_status,
                    "guard_notify_type"=>$guard_notify_type,
                    "guard_roles_type"=>$guard_roles_type,
                    "guard_roles_status"=>$guard_roles_status,
                    "guard_apikey_type"=>$guard_apikey_type,
                    "id"=>1));
                
                if($guard_system_status == 1):
                    $update2 = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id ");
                    $update2 = $update2->execute(array("status"=>1,"id"=>6));
                endif;
                
            if($guard_system_status == 2):
                $update3 = $conn->prepare("UPDATE settings SET guard_system_status=:guard_system_status WHERE id=:id ");
                $update3 = $update3->execute(array("guard_system_status"=>2,"id"=>1));
            elseif($guard_system_status == 1):
                $update3 = $conn->prepare("UPDATE settings SET guard_system_status=:guard_system_status WHERE id=:id ");
                $update3 = $update3->execute(array("guard_system_status"=>1,"id"=>1));      
            endif; 
                
                if( $update ):
                  $success    = 1;
                  $successText= "Transaction successful";
                  $icon     = "success";
                else:
                  $error    = 1;
                  $errorText= "Operation failed";
                  $icon     = "error";
                endif;
    

}elseif($e_route == "details"){
    

		$query = $conn->prepare("SELECT SUM(payment_amount) FROM payments WHERE payment_status='3' ");
$query -> execute();
$query = $query->fetch(PDO::FETCH_ASSOC);


		$query2 = $conn->prepare("SELECT sum(order_charge) as order_charge FROM orders");
$query2 -> execute();
$query2 = $query2->fetch(PDO::FETCH_ASSOC);

	
	$kazanc = $conn->prepare("SELECT SUM(payment_amount) FROM payments WHERE payment_status='3' AND  YEAR(payment_create_date) = YEAR(CURDATE()) AND MONTH(payment_create_date) = MONTH(CURDATE())   ");
$kazanc -> execute();
$kazanc = $kazanc->fetch(PDO::FETCH_ASSOC);

	$kazanc2 = $conn->prepare("SELECT SUM(payment_amount) FROM payments WHERE payment_status='3' AND  YEAR(payment_create_date) = YEAR(CURDATE()) AND DAY(payment_create_date) = DAY(CURDATE())   ");
$kazanc2 -> execute();
$kazanc2 = $kazanc2->fetch(PDO::FETCH_ASSOC);


	$uye = $conn->prepare("SELECT SUM(payment_amount) FROM payments WHERE payment_status='3' AND  YEAR(payment_create_date) = YEAR(CURDATE()) AND MONTH(payment_create_date) = MONTH(CURDATE())   ");
$uye -> execute();
$uye = $uye->fetch(PDO::FETCH_ASSOC);


  $count        = $conn->prepare("SELECT * FROM clients WHERE YEAR(register_date) = YEAR(CURDATE()) AND MONTH(register_date) = MONTH(CURDATE())  ");
        $count        -> execute(array());
        $count        = $count->rowCount();
	
	
	
  $count2        = $conn->prepare("SELECT * FROM clients WHERE YEAR(register_date) = YEAR(CURDATE()) AND DAY(register_date) = DAY(CURDATE())  ");
        $count2        -> execute(array());
        $count2        = $count2->rowCount();
        
          $count3        = $conn->prepare("SELECT * FROM orders WHERE YEAR(order_create) = YEAR(CURDATE()) AND MONTH(order_create) = MONTH(CURDATE())  ");
        $count3        -> execute(array());
        $count3        = $count3->rowCount();
        
          $count4        = $conn->prepare("SELECT * FROM orders WHERE YEAR(order_create) = YEAR(CURDATE()) AND DAY(order_create) = DAY(CURDATE())  ");
        $count4        -> execute(array());
        $count4        = $count4->rowCount();
	
	    $count5        = $conn->prepare("SELECT * FROM tickets WHERE YEAR(time) = YEAR(CURDATE()) AND MONTH(time) = MONTH(CURDATE())  ");
        $count5        -> execute(array());
        $count5        = $count5->rowCount();
        
          $count6        = $conn->prepare("SELECT * FROM tickets WHERE YEAR(time) = YEAR(CURDATE()) AND DAY(time) = DAY(CURDATE())  ");
        $count6        -> execute(array());
        $count6        = $count6->rowCount();
        
        $count7        = $conn->prepare("SELECT * FROM client_report WHERE YEAR(report_date) = YEAR(CURDATE()) AND MONTH(report_date) = MONTH(CURDATE())  ");
        $count7        -> execute(array());
        $count7        = $count7->rowCount();
        
        $count8        = $conn->prepare("SELECT * FROM client_report WHERE YEAR(report_date) = YEAR(CURDATE()) AND DAY(report_date) = DAY(CURDATE())  ");
        $count8        -> execute(array());
        $count8        = $count8->rowCount();

	$count9      = $conn->prepare("SELECT * FROM clients where balance > 0");
    $count9     -> execute();
    $count9      = $count9->rowCount();
    
    
}elseif($e_route == "proxy"){

  $proxy = $conn->prepare("SELECT * FROM proxy ORDER BY id DESC  ");
  $proxy->execute(array());
  $proxy = $proxy->fetchAll(PDO::FETCH_ASSOC);


if($_POST): 

    $insert = $conn->prepare("INSERT INTO proxy SET user=:user, pass=:pass, ip=:ip, port=:port ");
    $insert = $insert->execute(array("user"=>$_POST["user"],"pass"=>$_POST["pass"],"ip"=>$_POST["ip"],"port"=>$_POST["port"]));

    header("Location:".site_url("admin/manager/proxy"));
 
endif;
    
 

if(route(3) == "delete" && route(4)):
 
    $id     = route(4);
    $delete = $conn->prepare("DELETE FROM proxy WHERE id=:id ");
    $delete->execute(array("id"=>$id));

    header("Location:".site_url("admin/manager/proxy"));

endif;   

    
}



require admin_view('manager');
