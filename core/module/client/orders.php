<!-- Include the SweetAlert2 CSS and JavaScript files -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.5/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.5/dist/sweetalert2.min.js"></script>

<?php

$title .= $languageArray["orders.title"];
 
if( $_SESSION["neira_userlogin"] != 1  || $user["client_type"] == 1  ){
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

  $request = route(1);
  $o_id = route(2);


  if($request == 'refill' && $o_id){
     $order  = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
        $order  = $conn->prepare("SELECT * FROM orders INNER JOIN services ON services.service_id = orders.service_id INNER JOIN service_api ON services.service_api = service_api.id WHERE orders.order_id=:id ");
        $order ->execute(array("id"=>$o_id));
        $order  = $order->fetch(PDO::FETCH_ASSOC);
        $order = json_decode(json_encode($order),true);
        
        
          $services  = $conn->prepare("SELECT * FROM services WHERE service_id=:id ");
        $services ->execute(array("id"=>$order["service_id"]));
        $services  = $services->fetch(PDO::FETCH_ASSOC);
        $services = json_decode(json_encode($services),true);
        

 
            
       $smmapi   = new SMMApi();
       
       $get_refill = $smmapi->action(array('key' => $order["api_key"],'action' =>'refill','order'=>$order["api_orderid"]),$order["api_url"]);
       
        
             
            
         
        
        $refill_id = $get_refill->refill;
        $refill_placed_status = $get_refill->status;
        $refill_error = $get_refill->error;
        
        
        
        
        
            
        if(!$refill_error){
                   
            
            
                    
            $refill_placed_time = date("Y-m-d H:i:s");
            $refill_end_time = strtotime($refill_placed_time) + 86400;
        
            $refill_end_time = date("Y-m-d H:i:s" , $refill_end_time);
                         $update = $conn -> prepare("UPDATE orders SET last_check=:last_check WHERE order_id=:order_id");
             $update -> execute(array("order_id"=>$order["order_id"] , "last_check"=>$refill_placed_time));
            if(empty($refill_id)){
            $refill_id = "0";
            }
            
            $insert = $conn->prepare("INSERT INTO refill_status SET client_id=:client_id , order_id=:order_id , refill_apiid=:refill_apiid ,order_apiid=:order_apiid , refill_response=:refill_response , creation_date=:creation_date , ending_date=:ending_date ,  order_url=:order_url , service_name=:service_name ");
            $insert ->execute(array("client_id"=>$order["client_id"] , "order_id"=>$order["order_id"] , "refill_apiid"=> $refill_id , "order_apiid"=>$order["api_orderid"] , "refill_response"=>"Success" , "creation_date"=>$refill_placed_time , "ending_date" => $refill_end_time , "order_url"=>$order[order_url] , "service_name"=>$order[service_name]));
             
             
            if($insert):
                 $rd=site_url("refill");
            echo "<script>
Swal.fire({
  icon: 'success',
  title: 'Successfully Placed',
  showConfirmButton: true,
  confirmButtonText: 'Okay'
}).then(function() {
  // Perform the redirect here
  window.location.href = '$rd';
});
</script>";
else:
             die;
            endif;
           

             
      
        }else {
                      $rd=site_url("orders");
  
    echo "<script>
Swal.fire({
  icon: 'warning',
  title: '$refill_error',
  showConfirmButton: true,
  confirmButtonText: 'Dismiss'
}).then(function() {
  // Perform the redirect here
  window.location.href = '$rd';
});
</script>";

                 
                
          
           
        }
    
    
   
    
     
 

            $route[1]         = "all";

  }elseif($request == 'cancel' && $o_id){
    
        if(!countRow(['table'=>'tasks','where'=>['task_type'=>1,'task_status'=>'pending','client_id'=>$user["client_id"],'order_id'=>$o_id]])){
    
                    $orders = $conn->prepare("SELECT * FROM orders INNER JOIN services LEFT JOIN service_api ON services.service_api = service_api.id WHERE services.service_id = orders.service_id
  AND orders.client_id=:c_id AND orders.order_id=:order_id ");
$orders->execute(['c_id' => $user['client_id'], 'order_id' => $o_id]);
$orders = $orders->fetch(PDO::FETCH_ASSOC);
        $smmapi = new SMMApi();

$get_cancel = $smmapi->action(
[
'key' => $orders['api_key'],
'action' => 'cancel',
'order' => $orders['api_orderid'],
],
$orders['api_url']
);
$res = json_encode($get_cancel,true);

            $insert = $conn->prepare("INSERT INTO tasks SET client_id=:c_id, order_id=:o_id, service_id=:s_id, task_type=:type, task_date=:date, res=:res ");
            $insert->execute(array("c_id"=>$orders["client_id"],"o_id"=>$orders["order_id"],"s_id"=>$orders["service_id"],"type"=>1,"date"=>date("Y-m-d H:i:s"),"res"=>$res ));
            
              $rd=site_url("orders");
            echo "<script>
Swal.fire({
  icon: 'success',
  title: 'Successfully Canceled',
  showConfirmButton: true,
  confirmButtonText: 'Okay'
}).then(function() {
  // Perform the redirect here
  window.location.href = '$rd';
});
</script>";
    
        }
            $route[1]         = "all";


  }

  $status_list = ["all", "pending", "inprogress", "completed", "partial", "processing", "canceled"];
  $search_statu = route(1);
  if (!route(1)):
      $route[1] = "all";
  endif;
  if (!in_array($search_statu, $status_list)):
      $route[1] = "all";
  endif;
  if (route(2)):
      $page = route(2);
  else:
      $page = 1;
  endif;
  if (route(1) != "all"):
      $search = "&& order_status='" . route(1) . "'";
  else:
      $search = "";
  endif;
  if (!empty($_GET["search"])):
      $search.= " && ( order_url LIKE '%" . $_GET["search"] . "%' || order_id LIKE '%" . $_GET["search"] . "%' ) ";
  endif;
  if (!empty($_GET["subscription"])):
      $search.= " && ( subscriptions_id LIKE '%" . $_GET["subscription"] . "%'  ) ";
  endif;
  if (!empty($_GET["dripfeed"])):
      $search.= " && ( dripfeed_id LIKE '%" . $_GET["dripfeed"] . "%'  ) ";
  endif;
  $c_id = $user["client_id"];
  $to = 25;
  $count = $conn->query("SELECT * FROM orders WHERE client_id='$c_id' && dripfeed='1' && subscriptions_type='1' $search ")->rowCount();
  $pageCount = ceil($count / $to);
  if ($page > $pageCount):
      $page = 1;
  endif;
  $where = ($page * $to) - $to;
  $paginationArr = ["count" => $pageCount, "current" => $page, "next" => $page + 1, "previous" => $page - 1];
  $orders = $conn->prepare("SELECT * FROM orders INNER JOIN services WHERE services.service_id = orders.service_id && orders.dripfeed=:dripfeed && orders.subscriptions_type=:subs && orders.client_id=:c_id $search ORDER BY orders.order_id DESC LIMIT $where,$to ");
  $orders->execute(array("c_id" => $user["client_id"], "dripfeed" => 1, "subs" => 1));
  $orders = $orders->fetchAll(PDO::FETCH_ASSOC);
  $ordersList = [];
  foreach ($orders as $order) {
        
                 $o["refillButton"] = false;            

         
        $order["refill_hours"]="24";

        $d1= new DateTime($order["order_create"]); // first date
          $d2= new DateTime(date("Y-m-d H:i:s")); // second date
 $today = $order["last_check"];
  $interval= $d1->diff($d2); 
          $diff = ($interval->days * $order["refill_hours"] ) + $interval->h;

 if($order["order_status"] == "completed" ):

 if($order["refill_type"] == 2 ):

 
  if($diff >= $order["refill_hours"] ):

            $o["refillButton"] = true;            

endif;
endif;
endif;
       
        if($order["cancel_type"] == 2 && ( $order["order_status"] == 'pending' || $order["order_status"] == 'processing' || $order["order_status"] == 'inprogress' ) && !countRow(['table'=>'tasks','where'=>['task_type'=>1,'task_status'=>'pending','client_id'=>$user["client_id"],'order_id'=>$order["order_id"]]]) && !countRow(['table'=>'tasks','where'=>['task_type'=>1,'task_status'=>'canceled','client_id'=>$user["client_id"],'order_id'=>$order["order_id"]]])){
            
            $o["cancelButton"] = true; 
        }else{
            $o["cancelButton"] = false; 
        }
 

      $o["id"]    = htmlentities($order["order_id"]);
      $o["date"]  = date("Y-m-d H:i:s", (strtotime($order["order_create"])+$user["timezone"]));
      $o["link"]    = htmlentities($order["order_url"]);
      $o["charge"]  = htmlentities($order["order_charge"]);
      $o["start_count"]  = htmlentities($order["order_start"]);
      $o["quantity"]  = htmlentities($order["order_quantity"]);
      
       $o["service_id"]  = htmlentities($order["service_id"]);
      
      $o["service"]  = htmlentities($order["service_name"]);
      $o["status"]  = $languageArray["orders.status.".$order["order_status"]];
      if( $order["order_status"] == "completed" && substr($order["order_remains"], 0,1) == "-" ):
        $o["remains"]  = "+".substr($order["order_remains"], 1);
      else:
        $o["remains"]  = htmlentities($order["order_remains"]);
      endif;
      array_push($ordersList,$o);
    }
