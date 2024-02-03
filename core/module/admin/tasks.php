<?php

  if( $user["access"]["tasks"] != 1  ):
    header("Location:".site_url("admin"));
    exit();
  endif;

 
  if( $_SESSION["client"]["data"] ):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
      $$key = $value;
    }
    unset($_SESSION["client"]);
  endif;

    if( route(2) && is_numeric(route(2)) ):
      $page = route(2);
    else:
      $page = 1;
    endif;

    if( $_GET["search_type"] == "order_id" && $_GET["search"] ):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);

      $search       = "WHERE tasks.order_id LIKE '%".$search_word."%' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
    endif;     
    
    $count        = $conn->prepare("SELECT * FROM tasks");
      $count        -> execute(array());
      $count        = $count->rowCount();
    $to             = 50;
    $pageCount      = ceil($count/$to); 
    
    if( $page > $pageCount ): 
        $page = 1;
    endif;
    
    $where          = ($page*$to)-$to;
    $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
    $orders         = $conn->prepare("SELECT * FROM tasks LEFT JOIN clients ON clients.client_id=tasks.client_id LEFT JOIN orders ON orders.order_id=tasks.order_id LEFT JOIN services ON services.service_id=tasks.service_id $search ORDER BY tasks.task_id DESC LIMIT $where,$to ");
    $orders         -> execute(array());
    $orders         = $orders->fetchAll(PDO::FETCH_ASSOC);


 if(route(3) && route(2) == "no"){
   
  $id     = route(3);

  $update = $conn->prepare("UPDATE tasks SET task_status=:status WHERE task_id=:id");
  $update = $update->execute(array("status"=>'canceled',"id"=>$id));

  header("Location:".site_url("admin/tasks"));

 }elseif(route(2) == "success"){

    $id     = route(3);

if($settings["auto_refill"] != 2 ):
    $update = $conn->prepare("UPDATE tasks SET task_status=:status WHERE task_id=:id");
    $update = $update->execute(array("status"=>'success',"id"=>$id));
else:
    $smmapi   = new SMMApi(); 
    
    $order  = $conn->prepare("SELECT * FROM tasks LEFT JOIN services ON services.service_id = tasks.service_id LEFT JOIN orders ON orders.order_id = tasks.order_id LEFT JOIN service_api ON services.service_api = service_api.id WHERE tasks.task_id=:id ");
    $order ->execute(array("id"=>$id));
    $order  = $order->fetch(PDO::FETCH_ASSOC);
              
    $send_refill = $smmapi->action(array('key' =>$order["api_key"],'action' =>'refill','order'=>$order["api_orderid"]),$order["api_url"]);
    
    if(@$send_refill->refill):
        $success = 1;
        $successText = "Your refill request has been sent to your provider.";
        $r_id = $send_refill->refill;
        $update = $conn->prepare("UPDATE tasks SET task_status=:status, refill_orderid=:r_id WHERE task_id=:id");
        $update = $update->execute(array("status"=>'success',"id"=>$id,"r_id"=>$r_id));
    else:
        $send_refill = json_encode($send_refill, true);
        $error = 1;
        $errorText = "Your refill request could not be sent <code>".$send_refill."</code>";
    endif;
endif;    


 }elseif(route(3) && route(2) == "canceled"){
 
  $id     = route(3);

  $order  = $conn->prepare("SELECT * FROM orders INNER JOIN clients ON clients.client_id = orders.client_id WHERE orders.order_id=:id ");
  $order ->execute(array("id"=>$id));
  $order  = $order->fetch(PDO::FETCH_ASSOC);

  $balance = $order["balance"]+$order["order_charge"];
  $spent   = $order["spent"]-$order["order_charge"];
  $order["order_quantity"]=$order["order_quantity"];

  $update = $conn->prepare("UPDATE orders SET api_charge=:api_charge, order_profit=:order_profit, order_status=:status, order_error=:error, order_charge=:price, order_quantity=:quantity, order_remains=:remains WHERE order_id=:id ");
  $update = $update->execute(array("api_charge"=>0,"order_profit"=>0,"status"=>"canceled","price"=>0,"error"=>"-","quantity"=>0,"remains"=>$order["order_quantity"],"id"=>$id));
 
  $update2 = $conn->prepare("UPDATE clients SET balance=:balance, spent=:spent WHERE client_id=:id");
  $update2 = $update2->execute(array("id"=>$order["client_id"],"balance"=>$balance,"spent"=>$spent ));

  $update3 = $conn->prepare("UPDATE tasks SET task_status=:status WHERE order_id=:id");
  $update3 = $update3->execute(array("status"=>'success',"id"=>$id));

  header("Location:".site_url("admin/tasks"));

}

require admin_view('tasks');

