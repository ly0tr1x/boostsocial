<?php
 

$smmapi   = new SMMApi();

$orders = $conn->prepare("SELECT *,services.service_id as service_id,services.service_api as api_id FROM orders
  INNER JOIN clients ON clients.client_id=orders.client_id
  INNER JOIN services ON services.service_id=orders.service_id
  LEFT JOIN categories ON categories.category_id=services.category_id
  INNER JOIN service_api ON service_api.id=orders.order_api
  WHERE orders.dripfeed=:dripfeed && orders.subscriptions_type=:subs && ( orders.order_status=:statu1 || orders.order_status=:statu2  || orders.order_status=:statu3 ) ");
$orders->execute(array("dripfeed"=>1,"subs"=>1,"statu1"=>"pending","statu2"=>"inprogress","statu3"=>"processing"));
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);

  foreach( $orders as $order ):

    $extra = json_decode($order["order_error"]);  
      
    if(@!$extra->error):  

    $user   = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
    $user->execute(array("id"=>$order["client_id"]));
    $user   = $user->fetch(PDO::FETCH_ASSOC);
    $order["balance"] =   $user["balance"];
    $clientBalance    =   $user["balance"];
    $orderid          =   $order["order_id"];

    if( $order["api_type"] == 1 ):
      ## Standart api başla ##
      
        $orderstatus= $smmapi->action(array('key' =>$order["api_key"],'action' =>'status','order'=>$order["api_orderid"]),$order["api_url"]);
        $api_charge = $orderstatus->charge;
        $statu      = str_replace(" ", "", strtolower($orderstatus->status));
        $start      = $orderstatus->start_count;
        $remains    = $orderstatus->remains; 
        $finish   = 0;
        if( !$api_charge ): $api_charge = 0; endif;
        
      ## Standart api bitti ##
    endif;

    if( empty( $start ) || !$start ):
      $start    = 0;
    endif;
    
    if( empty( $remains ) || !$remains ):
      $remains  = 0;
    endif;
    
    if( $remains > $order["order_quantity"] ):
      $remains  = $order["order_quantity"];
    endif;
    
    if(  0 > $remains ):
      $remains  = 0;
    endif;

      if( $statu == "canceled" || $statu == "cancel" ):
        $conn->beginTransaction();
     
        $update2= $conn->prepare("UPDATE clients SET balance=:balance, spent=:spent WHERE client_id=:id ");
        $update2= $update2->execute(array("id"=>$order["client_id"],"balance"=>$order["balance"]+$order["order_charge"],"spent"=>$order["spent"]-$order["order_charge"] ));
    
        $user   = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
        $user->execute(array("id"=>$order["client_id"]));
        $user   = $user->fetch(PDO::FETCH_ASSOC);
        
        $report = $conn->prepare("INSERT INTO client_report SET client_id=:id, action=:action, report_ip=:ip, report_date=:date ");
        $report = $report->execute(array("date"=>date("Y-m-d H:i:s"),"id"=>$order["client_id"],"action"=>"#".$orderid." Order No. has been canceled and ".$order["order_charge"]." TL fee refunded Old balance:".$clientBalance." / Yeni bakiye:".$user["balance"],"ip"=>"127.0.0.1" ));
     
        $update = $conn->prepare("UPDATE orders SET order_detail=:detail, order_start=:start, order_finish=:finish, order_remains=:remains, order_status=:status, order_charge=:charge, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("id"=>$orderid,"start"=>$start,"finish"=>$finish,"detail"=>json_encode($orderstatus),"remains"=>$remains,"status"=>"canceled","charge"=>0,"api_charge"=>0,"order_profit"=>0 ));
        
         if( $update && $update2 && $report ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      elseif( $statu == 'complete' || $statu == 'completed' ):
        $conn->beginTransaction();
        
        $report = $conn->prepare("INSERT INTO client_report SET client_id=:id, action=:action, report_ip=:ip, report_date=:date ");
        $report = $report->execute(array("date"=>date("Y-m-d H:i:s"),"id"=>$order["client_id"],"action"=>"#".$orderid." Order number completed.","ip"=>"127.0.0.1" ));
       
        $update = $conn->prepare("UPDATE orders SET order_start=:start, order_finish=:finish, order_remains=:remains, order_status=:status, order_remains=:remains, order_detail=:detail, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("start"=>$start,"finish"=>$finish,"remains"=>0,"status"=>"completed","detail"=>json_encode($orderstatus),"id"=>$orderid,"order_profit"=>$order["api_currencycharge"]*$api_charge,"api_charge"=>$api_charge,"remains"=>$remains));
         
          if( $update && $report ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      elseif( $statu == 'pending' ):
        $conn->beginTransaction();
      
        $update = $conn->prepare("UPDATE orders SET order_start=:start, order_remains=:remains, order_start=:start, order_finish=:finish, order_status=:status, order_detail=:detail, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("start"=>$start,"remains"=>$remains,"finish"=>$finish,"status"=>"pending","detail"=>json_encode($orderstatus),"id"=>$orderid,"order_profit"=>$order["api_currencycharge"]*$api_charge,"api_charge"=>$api_charge));
       
          if( $update ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      elseif( $statu == 'inprogress' ):
        $conn->beginTransaction();
  
        $update = $conn->prepare("UPDATE orders SET order_start=:start, order_remains=:remains, order_finish=:finish, order_status=:status, order_detail=:detail, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("start"=>$start,"remains"=>$remains,"finish"=>$finish,"status"=>"inprogress","detail"=>json_encode($orderstatus),"id"=>$orderid,"order_profit"=>$order["api_currencycharge"]*$api_charge,"api_charge"=>$api_charge));
     
           if( $update ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      elseif( $statu == 'processing' ):
        $conn->beginTransaction();
     
        $update = $conn->prepare("UPDATE orders SET order_start=:start, order_remains=:remains, order_finish=:finish, order_status=:status, order_detail=:detail, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("start"=>$start,"remains"=>$remains,"finish"=>$finish,"status"=>"processing","detail"=>json_encode($orderstatus),"id"=>$orderid,"order_profit"=>$order["api_currencycharge"]*$api_charge,"api_charge"=>$api_charge));
     
          if( $update ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      elseif( $statu == "partial" ):
        $conn->beginTransaction();
        
        if($order["service_package"] == 2):
            $return_price = ($order["order_charge"]/1000)*$remains;
        else:
            $return_price = ($order["order_charge"]/$order["order_quantity"])*$remains;
        endif;

        if(0 > $return_price ):
              $return_price = 0;
        endif;

        $update2= $conn->prepare("UPDATE clients SET balance=:balance, spent=:spent WHERE client_id=:id ");
        $update2= $update2->execute(array("id"=>$order["client_id"],"balance"=>$order["balance"]+$return_price,"spent"=>$order["spent"]-$return_price ));
        
        $user   = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
        $user->execute(array("id"=>$order["client_id"]));
        $user   = $user->fetch(PDO::FETCH_ASSOC);
       
        $report = $conn->prepare("INSERT INTO client_report SET client_id=:id, action=:action, report_ip=:ip, report_date=:date ");
        $report = $report->execute(array("date"=>date("Y-m-d H:i:s"),"id"=>$order["client_id"],"action"=>"#".$orderid." Order No. has been marked as partial and ".$return_price." TL fee refunded Old balance:".$clientBalance." / New balance:".$user["balance"],"ip"=>"127.0.0.1" ));
       
        $update = $conn->prepare("UPDATE orders SET order_detail=:detail, order_start=:start, order_finish=:finish, order_remains=:remains, order_status=:status, order_charge=:charge, api_charge=:api_charge, order_profit=:order_profit WHERE order_id=:id ");
        $update = $update->execute(array("id"=>$orderid,"start"=>$start,"finish"=>$finish,"detail"=>json_encode($orderstatus),"remains"=>$remains,"status"=>"partial","charge"=>$order["order_charge"]-$return_price,"order_profit"=>$order["api_currencycharge"]*$api_charge,"api_charge"=>$api_charge ));
         
          if( $update && $update2 && $report ):
            $conn->commit();
          else:
            $conn->rollBack();
          endif;
          
      endif;

     $update   = $conn->prepare("UPDATE orders SET last_check=:check WHERE order_id=:id ");
     $update  -> execute(array("id"=>$orderid,"check"=>date("Y-m-d H:i:s") ));

    endif;
    
  endforeach;
  
echo time(); 
