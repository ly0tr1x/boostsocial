<?php

 
if( route(1) == "v2" ):
    
header('Content-Type: application/json');

  function servicePackage($type){
    switch ($type) {
      case 1:
        $service_type = "Default";
      break;
      case 2:
        $service_type = "Package";
      break;
      case 3:
        $service_type = "Custom Comments";
      break;
      case 4:
        $service_type = "Custom Comments Package";
      break;
      default:
        $service_type = "Subscriptions";
      break;
    }
    return $service_type;
  }

  if( (empty($_POST) || !$_POST) && $_GET ):
      $_POST = $_GET; 
      $_POST["link"] = urldecode($_POST["link"]);
  endif; 

  $action           = htmlspecialchars($_POST["action"]);
  $key              = htmlspecialchars($_POST["key"]);
  $orderid          = htmlspecialchars($_POST["order"]);
  $serviceid        = htmlspecialchars($_POST["service"]);
  $quantity         = htmlspecialchars($_POST["quantity"]);
  $link             = htmlspecialchars($_POST["link"]);
  $username         = htmlspecialchars($_POST["username"]);
  $posts            = htmlspecialchars($_POST["posts"]);
  $delay            = htmlspecialchars($_POST["delay"]);
  $otoMin           = htmlspecialchars($_POST["min"]);
  $otoMax           = htmlspecialchars($_POST["max"]);
  $comments         = htmlspecialchars($_POST["comments"]);
  $runs             = htmlspecialchars($_POST["runs"]);
  $interval         = htmlspecialchars($_POST["interval"]);
  $expiry           = date("Y.m.d", strtotime($_POST["expiry"]));
  $subscriptions    = 0;


  $client = $conn->prepare("SELECT * FROM clients WHERE apikey=:key ");
  $client->execute(array("key"=>$key));
  $clientDetail = $client->fetch(PDO::FETCH_ASSOC);

  if ( empty( $action ) || empty( $key ) ):
    $output    = array('error'=>'Incorrect request');
  elseif ( !$client->rowCount() ):
    $output    = array('error'=>'API key hatalı','status'=>"102");
  elseif ( $clientDetail["client_type"] == 1 ):
    $output    = array('error'=>'Hesabınız pasif','status'=>"103");
  else:
    ## actionlar başla ##
      if( $action == "balance" ):
        $output    = array('balance'=>$clientDetail["balance"],'currency'=>abcus("id",$settings["site_currency"],"name"));
      elseif( $action == "status" ):
        $order        = $conn->prepare("SELECT * FROM orders WHERE order_id=:id && client_id=:client ");
        $order        -> execute(array("client"=>$clientDetail["client_id"],"id"=>$orderid ));
        $orderDetail  = $order->fetch(PDO::FETCH_ASSOC);
        if( $order->rowCount() ):
          if( $orderDetail["subscriptions_type"] == 2 ):
            $output    = array('status'=>ucwords($orderDetail["subscriptions_status"]),"posts"=>$orderDetail["subscriptions_posts"]);
          elseif( $orderDetail["dripfeed"] != 1 ):
            $output    = array('status'=>ucwords($orderDetail["subscriptions_status"]),"runs"=>$orderDetail["dripfeed_runs"]);
          else:
            $output    = array('charge'=>$orderDetail["order_charge"],"start_count"=>$orderDetail["order_start"],'status'=>ucfirst($orderDetail["order_status"]),"remains"=>$orderDetail["order_remains"],"currency"=>abcus("id",$settings["site_currency"],"name"));

          endif;
        else:
          $output    = array('error'=>'Sipariş bulunamadı.','status'=>"104");
        endif;
      elseif( $action == "services" ):
        $servicesRows = $conn->prepare("SELECT *, services.name_lang as name_lang FROM services INNER JOIN categories ON categories.category_id=services.category_id WHERE categories.category_type=:type2 && services.service_type=:type  ORDER BY categories.category_line,services.service_line ASC ");
        $servicesRows->execute(array("type"=>2,"type2"=>2));
        $servicesRows = $servicesRows->fetchAll(PDO::FETCH_ASSOC);

        $services = [];
          foreach ( $servicesRows as $serviceRow ) {
            $search = $conn->prepare("SELECT * FROM clients_service WHERE service_id=:service && client_id=:c_id ");
            $search->execute(array("service"=>$serviceRow["service_id"],"c_id"=>$clientDetail["client_id"]));
            $search2 = $conn->prepare("SELECT * FROM clients_category WHERE category_id=:category && client_id=:c_id ");
            $search2->execute(array("category"=>$serviceRow["category_id"],"c_id"=>$clientDetail["client_id"]));
            if( ( $serviceRow["service_secret"] == 2 || $search->rowCount() ) && ( $serviceRow["category_secret"] == 2 || $search2->rowCount() ) ):
                
                
                 $multiName   =  json_decode($serviceRow["name_lang"],true);
                $multiDesc   =  json_decode($serviceRow["description_lang"],true);
         
             $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        $default_lang =  $language_code['language_code'] ?? "ar";
         
             $name = $multiName[$default_lang];
            $desc = $multiDesc[$default_lang]; 

              $s["rate"]    = client_price($serviceRow["service_id"],$clientDetail["client_id"]);
              $s['service'] = $serviceRow["service_id"];
              $s['category']= $serviceRow["category_name"];
              $s['name']    = $name ?? $serviceRow["service_name"];
              $s['desc']    = $desc ?? $serviceRow["service_description"];
              $s['type']    = servicePackage($serviceRow["service_package"]);
              $s['min']     = $serviceRow["service_min"];
              $s['max']     = $serviceRow["service_max"];
                array_push($services,$s);
            endif;
          }
          $output  = $services;
      elseif( $action == "add" ):
        $clientBalance = $clientDetail["balance"];
        $serviceDetail = $conn->prepare("SELECT * FROM services INNER JOIN categories ON categories.category_id=services.category_id LEFT JOIN service_api ON service_api.id=services.service_api WHERE services.service_id=:id ");
        $serviceDetail->execute(array("id"=>$serviceid));
        $serviceDetail = $serviceDetail->fetch(PDO::FETCH_ASSOC);

        $search = $conn->prepare("SELECT * FROM clients_service WHERE service_id=:service && client_id=:c_id ");
        $search->execute(array("service"=>$serviceid,"c_id"=>$clientDetail["client_id"]));
        $search2 = $conn->prepare("SELECT * FROM clients_category WHERE category_id=:category && client_id=:c_id ");
        $search2->execute(array("category"=>$serviceDetail["category_id"],"c_id"=>$clientDetail["client_id"]));

        $link = $_POST["link"];
            
        if( ( $serviceDetail["service_secret"] == 2 || $search->rowCount() ) && $serviceDetail["category_type"] == 2 && $serviceDetail["service_type"] == 2 && ( $serviceDetail["category_secret"] == 2 || $search2->rowCount() ) ):
          ## sipariş geç ##
          
             if( $serviceDetail["service_package"] == 2 ):
              $price = client_price($serviceDetail["service_id"],$clientDetail["client_id"]);
              $serviceDetail["service_min"] = 1;
              $serviceDetail["service_max"] = 1;
              $quantity = 1;
             elseif( $serviceDetail["service_package"] == 3 || $serviceDetail["service_package"] == 4 ):
              $comments = str_replace("\\n","\n",$comments);
              $quantity = count(explode("\n",$comments));// count custom comments
              $price    = client_price($serviceDetail["service_id"],$clientDetail["client_id"])/1000*$quantity;
              $extras   = json_encode(["comments"=>$comments]);
              $subscriptions_status = "active";
              $subscriptions = 1; 
            else:
              $price  = client_price($serviceDetail["service_id"],$clientDetail["client_id"])/1000*$quantity;
            endif;
      
          if( $runs && $interval  ):
            $dripfeed  = 2; $totalcharges  = $price*$runs; $totalquantity = $quantity*$runs; $price = $price*$runs;
          else:
            $dripfeed  = 1; $totalcharges  = ""; $totalquantity = "";
          endif;
          
          $price = abs($price);

          if( ( $runs && empty( $interval ) ) || ( $interval && empty( $runs ) ) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( $serviceDetail["service_package"] == 1 && ( empty($link) || empty($quantity) ) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( $serviceDetail["service_package"] == 2 && empty($link) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ($serviceDetail["service_package"] == 14 || $serviceDetail["service_package"] == 15 ) && empty($link) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( $serviceDetail["service_package"] == 3 && ( empty($link) || empty($comments) ) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( $serviceDetail["service_package"] == 4 && ( empty($link) || empty($comments) ) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ( $serviceDetail["service_package"] != 11 && $serviceDetail["service_package"] != 12 && $serviceDetail["service_package"] != 13  ) && ( ( $dripfeed == 2 && $totalquantity < $serviceDetail["service_min"] ) || ( $dripfeed == 1 && $quantity < $serviceDetail["service_min"]  ) ) ):
            $output        = array('error'=>"You did not meet the minimum number.",'status'=>108);
          elseif( ( $serviceDetail["service_package"] != 11 && $serviceDetail["service_package"] != 12 && $serviceDetail["service_package"] != 13  ) && ( ( $dripfeed == 2 && $totalquantity > $serviceDetail["service_max"] ) || ( $dripfeed == 1 && $quantity > $serviceDetail["service_max"]  ) ) ):
            $output        = array('error'=>"Maximum number exceeded.",'status'=>109);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && empty($username) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && empty($otoMin) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && empty($otoMax) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && empty($posts) ):
            $output        = array('error'=>"You must fill in the required fields.",'status'=>107);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && $otoMax < $otoMin ):
            $output        = array('error'=>"The minimum number cannot be greater than the maximum number.",'status'=>110);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && $otoMin < $serviceDetail["service_min"] ):
            $output        = array('error'=>"You did not meet the minimum number.",'status'=>111);
          elseif( ( $serviceDetail["service_package"] == 11 || $serviceDetail["service_package"] == 12 || $serviceDetail["service_package"] == 13  ) && $otoMax > $serviceDetail["service_max"] ):
            $output        = array('error'=>"Maximum number exceeded",'status'=>112);
          elseif( ( $price > $clientDetail["balance"] ) && $clientDetail["balance_type"] == 2 ):
            $output        = array('error'=>"You have insufficient balance",'status'=>113);
          elseif( ( $clientDetail["balance"] - $price < "-".$clientDetail["debit_limit"] ) && $clientDetail["balance_type"] == 1 ):
            $output        = array('error'=>"You have insufficient balance",'status'=>113);
          elseif( 0 > $price ):
            $output        = array('error'=>"You have insufficient balance",'status'=>114);
          elseif( strstr($price, "-") ):    
            $output        = array('error'=>"You have insufficient balance",'status'=>115);
          else:
              if( !$runs ):  $runs = 1; endif;
              
              if ($runs < 1) {
    $runs = 1;
  }

            if( $serviceDetail["service_package"] == 3 || $serviceDetail["service_package"] == 4 ):
              $comments = str_replace("\\n","\n",$comments);
              $quantity = count(explode("\n",$comments));// count custom comments
              $price    = client_price($serviceDetail["service_id"],$clientDetail["client_id"])/1000*$quantity;
              $extras   = json_encode(["comments"=>$comments]);
              $subscriptions_status = "active";
              $subscriptions = 1;
            elseif( $serviceDetail["service_package"] == 11 ||  $serviceDetail["service_package"] == 12 ||  $serviceDetail["service_package"] == 13 ):
              $quantity         = $otoMin."-".$otoMax; // Sipariş miktarı
              $price            = 0;
              $extras = json_encode([]);
              $subscriptions = 1;
            elseif( $serviceDetail["service_package"] == 14 ||  $serviceDetail["service_package"] == 15 ):
              $quantity         = $serviceDetail["service_min"];
              $price            = service_price($service["service_id"]);
              $posts            = $serviceDetail["service_autopost"];
              $delay            = 0;
              $time             = '+'.$serviceDetail["service_autotime"].' days';
              $expiry           = date('Y-m-d H:i:s', strtotime($time));
              $otoMin           = $serviceDetail["service_min"];
              $otoMax           = $serviceDetail["service_min"];
              $extras = json_encode([]);
            else:
              $posts            = 0;
              $delay            = 0;
              $expiry           = "1970-01-01";
              $extras = json_encode([]);
              $subscriptions_status = "active";
              $subscriptions = 1;
            endif;

              if( $serviceDetail["service_api"] == 0 ):
                /* manuel sipariş - başla */
                //$conn->beginTransaction();
             
                $insert = $conn->prepare("INSERT INTO orders SET order_where=:order_where, order_start=:count, order_profit=:profit, order_error=:error, client_id=:c_id, service_id=:s_id, order_extras=:extras,order_quantity=:quantity, order_charge=:price, order_url=:url, order_create=:create, last_check=:last ");
                $insert = $insert-> execute(array("order_where"=>"api","count"=>0,"c_id"=>$clientDetail["client_id"],"error"=>"-","s_id"=>$serviceDetail["service_id"],"extras"=>$extras,"quantity"=>$quantity,"price"=>$price,"profit"=>$price,"url"=>$link,"create"=>date("Y.m.d H:i:s"),"last"=>date("Y.m.d H:i:s")));
               
                if( $insert ): $last_id = $conn->lastInsertId(); endif;
          
                  $update = $conn->prepare("UPDATE clients SET balance=:balance, spent=:spent WHERE client_id=:id");
                $update = $update-> execute(array("balance"=>$clientDetail["balance"]-$price,"spent"=>$clientDetail["spent"]+$price,"id"=>$clientDetail["client_id"]));
             
                $insert2= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
                $insert2= $insert2->execute(array("c_id"=>$clientDetail["client_id"],"action"=>"via API ".$price." A new order amounting to TL has been placed.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
               
                if ( $insert && $update && $insert2 ):
                    //$conn->commit();
                    $output        = array('status'=>100,'order'=>$last_id );
                    if( $settings["alert_newmanuelservice"] == 2 ):
                      if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                      if( $sendsms ):
                        SMSUser($settings["admin_telephone"],"your website #".$last_id." idli a new order is available.");
                      endif;
                      if( $sendmail ):
                        sendMail(["subject"=>"New order available.","body"=>"your website #".$last_id." idli a new order is available.","mail"=>$settings["admin_mail"]]);
                      endif;
                    endif;
                  else:
                    //$conn->rollBack();
                    $output        = array('error'=>"An error occurred while placing your order.",'status'=>114);
                  endif;
                /* manuel sipariş - bitir */
              else:
                /* api ile sipariş - başla */
                //$conn->beginTransaction();

                  $insert = $conn->prepare("INSERT INTO orders SET order_where=:order_where, order_error=:error, order_detail=:detail, client_id=:c_id,
                    service_id=:s_id, order_quantity=:quantity, order_charge=:price, order_url=:url, order_create=:create, order_extras=:extra, last_check=:last_check,
                    order_api=:api, api_serviceid=:api_serviceid, subscriptions_status=:s_status,
                    subscriptions_type=:subscriptions, subscriptions_username=:username, subscriptions_posts=:posts, subscriptions_delay=:delay, subscriptions_min=:min,
                    subscriptions_max=:max, subscriptions_expiry=:expiry
                    ");
                  $insert = $insert-> execute(array("order_where"=>"api","c_id"=>$clientDetail["client_id"],"detail"=>"cronpending","error"=>"-",
                    "s_id"=>$serviceDetail["service_id"],"quantity"=>$quantity,"price"=>$price / $runs,"url"=>$link,
                    "create"=>date("Y.m.d H:i:s"),"extra"=>$extras,"last_check"=>date("Y.m.d H:i:s"),"api"=>$serviceDetail["id"],
                    "api_serviceid"=>$serviceDetail["api_service"],"s_status"=>$subscriptions_status,"subscriptions"=>$subscriptions,"username"=>$username,
                    'posts'=>$posts,
                    "delay"=>$delay,"min"=>$otoMin,"max"=>$otoMax,"expiry"=>$expiry));
                    if( $insert ): $last_id = $conn->lastInsertId(); endif;
                   
                    $insert2		= 	$conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
                    $insert2		= 	$insert2->execute(array("c_id"=>$clientDetail["client_id"],"action"=>"via API ".$price." A new order amounting to TL has been placed #".$last_id." Old Balance: ".$clientBalance." / New Balance:".$clientDetail["balance"],"ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));

                	$update_client	= 	$conn->prepare("UPDATE clients SET balance=:balance, spent=:spent WHERE client_id=:id");
                    $update_client	= 	$update_client-> execute(array("balance"=>$clientDetail["balance"]-$price,"spent"=>$clientDetail["spent"]+$price,"id"=>$clientDetail["client_id"]));
                    
                 
                    if ( $insert ):
                      //$conn->commit();
                      $output        = array('order'=>$last_id );
                    else:
                     // $conn->rollBack();
                      $output        = array('error'=>"An error occurred while placing your order.",'status'=>114);
                    endif;
                /* api ile sipariş - bitir */
              endif;
          endif;
          ## sipariş geç  bitti ##
        else:
          $output    = array('error'=>'Service is inactive or not found','status'=>"105");
        endif;
      endif;
    ## actionlar bitti ##
  endif;
   print_r(json_encode($output));
   die;
elseif( !route(1) ):
    
    if($_SESSION["neira_userlogin"] == 1 ):
        if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
            header("Location:".site_url('verify/sms'));
        }
        if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
            header("Location:".site_url('verify/mail')); 
        }
        endif;
  $title .= $languageArray["api.title"];
  $user["apikey"] = private_str($user["apikey"], 10, 12); 
else:
  header("Location:".site_url());
endif;
