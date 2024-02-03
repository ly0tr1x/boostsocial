<?php



 
$smmapi   = new SMMApi();

$services = $conn->prepare("SELECT * FROM services INNER JOIN service_api ON service_api.id=services.service_api WHERE services.service_api!=:apitype  ORDER BY services.provider_lastcheck ASC limit 20");
$services->execute(array("apitype"=>0));
$services = $services->fetchAll(PDO::FETCH_ASSOC);

$there_change=0;

  foreach( $services as $service ):
      
    $update   = $conn->prepare("UPDATE services SET provider_lastcheck=:check WHERE service_id=:id ");
    $update  -> execute(array("id"=>$service["service_id"],"check"=>date("Y-m-d H:i:s") ));
      
    $there[$service["service_id"]] = 0;
    $apiServices  = $smmapi->action(array('key'=>$service["api_key"],'action'=>'services'),$service["api_url"]);
    $apiServices  = json_decode(json_encode($apiServices),true);
    
 
    if( !is_numeric($apiServices["0"]["service"]) && empty($apiServices["0"]["service"])  ):
      die; 
    endif;

      foreach ($apiServices as $apiService):
        if( $service["api_service"] == $apiService["service"] ):
            
          $there[$service["service_id"]] = 1;
          $extras = json_decode($service["api_detail"],true);
          
            if( $apiService["rate"] != $extras["rate"] ):

              $extra  = ["old"=>$extras["rate"],"new"=>$apiService["rate"] ];
              $insert = $conn->prepare("INSERT INTO serviceapi_alert SET service_id=:service, serviceapi_alert=:alert, servicealert_date=:date, servicealert_extra=:extra ");
              $insert->execute(array("service"=>$service["service_id"],"alert"=>"#".$service["service_id"]." numbered ".$service["service_name"]." The service price has been changed.","date"=>date("Y-m-d H:i:s"),"extra"=>json_encode($extra) ));
              if( $insert ): $there_change = $there_change+1; endif;
            endif;
            if( $apiService["min"] != $extras["min"] ):
              $extra  = ["old"=>$extras["min"],"new"=>$apiService["min"] ];
              $insert = $conn->prepare("INSERT INTO serviceapi_alert SET service_id=:service, serviceapi_alert=:alert, servicealert_date=:date, servicealert_extra=:extra ");
              $insert->execute(array("service"=>$service["service_id"],"alert"=>"#".$service["service_id"]." numbered ".$service["service_name"]." The service minimum amount has been changed.","date"=>date("Y-m-d H:i:s"),"extra"=>json_encode($extra) ));
              if( $insert ): $there_change = $there_change+1; endif;
            endif;
            if( $apiService["max"] != $extras["max"] ):
              $extra  = ["old"=>$extras["max"],"new"=>$apiService["max"] ];
              $insert = $conn->prepare("INSERT INTO serviceapi_alert SET service_id=:service, serviceapi_alert=:alert, servicealert_date=:date, servicealert_extra=:extra ");
              $insert->execute(array("service"=>$service["service_id"],"alert"=>"#".$service["service_id"]." numbered ".$service["service_name"]." The service maximum amount has been changed.","date"=>date("Y-m-d H:i:s"),"extra"=>json_encode($extra) ));
              if( $insert ): $there_change = $there_change+1; endif;
            endif;
              if( $service["api_servicetype"] == 1 && $there[$service["service_id"]] ):
                $extra  = ["old"=>"Passive at Provider","new"=>"Active on Provider" ];
                $update = $conn->prepare("UPDATE services SET api_servicetype=:type WHERE service_id=:service ");
                $update->execute(array("service"=>$service["service_id"],"type"=>2 ));
                $insert = $conn->prepare("INSERT INTO serviceapi_alert SET service_id=:service, serviceapi_alert=:alert, servicealert_date=:date, servicealert_extra=:extra ");
                $insert->execute(array("service"=>$service["service_id"],"alert"=>"#".$service["service_id"]." numbered ".$service["service_name"]." It has been reactivated by the service provider named.","date"=>date("Y-m-d H:i:s"),"extra"=>json_encode($extra) ));
                if( $insert ): $there_change = $there_change+1; endif;
              else:
                $update = $conn->prepare("UPDATE services SET api_servicetype=:type WHERE service_id=:service ");
                $update->execute(array("service"=>$service["service_id"],"type"=>2 ));
              endif;
        endif;
      endforeach;
  endforeach;

  foreach ($there as $service => $type):
    $serviceDetail = $conn->prepare("SELECT * FROM services WHERE service_id=:id ");
    $serviceDetail->execute(array("id"=>$service));
    $serviceDetail = $serviceDetail->fetch(PDO::FETCH_ASSOC);
    if( $type == 0 && $serviceDetail["api_servicetype"] == 2 ):
      $extra  = ["old"=>"Active on Provider","new"=>"Passive at Provider" ];
      
      if($settings["ser_sync"] == 1){
        $update = $conn->prepare("UPDATE services SET api_servicetype=:type, service_type=:service_type WHERE service_id=:service ");
        $update->execute(array("service"=>$service,"type"=>1,"service_type"=>1 ));
      }else{    
        $update = $conn->prepare("UPDATE services SET api_servicetype=:type WHERE service_id=:service ");
        $update->execute(array("service"=>$service,"type"=>1 ));
      }
      
      $insert = $conn->prepare("INSERT INTO serviceapi_alert SET service_id=:service, serviceapi_alert=:alert, servicealert_date=:date, servicealert_extra=:extra ");
      $insert->execute(array("service"=>$service,"alert"=>"#".$service." numbered ".$service["service_name"]." Removed by the service provider named.","date"=>date("Y-m-d H:i:s"),"extra"=>json_encode($extra) ));
      if( $update ): $there_change = $there_change+1; endif;
    endif;
  endforeach;

  if( $settings["alert_serviceapialert"] == 2 && $there_change ):
    if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
    if( $sendsms ):
        $rand = rand(1,99999);  
      SMSUser($settings["admin_telephone"],"You have services whose information is changed by the service provider.".$rand);
    endif;
    if( $sendmail ):
      //sendMail(["subject"=>"Provider information.","body"=>"You have services whose information is changed by the service provider.","mail"=>$settings["admin_mail"]]);
    endif;
  endif;


  $services = $conn->prepare("SELECT * FROM services INNER JOIN service_api ON service_api.id=services.service_api WHERE services.service_api!=:apitype ORDER BY services.sync_lastcheck ASC LIMIT 20");
  
  $services->execute(array("apitype"=>0));
  $services = $services->fetchAll(PDO::FETCH_ASSOC);
      $currency     = $conn->prepare("SELECT * FROM settings WHERE id=:id");
    $currency     ->execute(array("id"=>"1"));
    $currency     = $currency->fetch(PDO::FETCH_ASSOC);
  $there_change=0;
  
  foreach( $services as $service ):

    $update   = $conn->prepare("UPDATE services SET sync_lastcheck=:check WHERE service_id=:id ");
    $update  -> execute(array("id"=>$service["service_id"],"check"=>date("Y-m-d H:i:s") ));
      
    $there[$service["service_id"]] = 0;
    $apiServices  = $smmapi->action(array('key'=>$service["api_key"],'action'=>'services'),$service["api_url"]);
    $balance      = $smmapi->action(array('key' =>$service["api_key"],'action' =>'balance'),$service["api_url"]);
    $apiServices  = json_decode(json_encode($apiServices),true);
             $provider_currency=$balance->currency;
    if( !is_numeric($apiServices["0"]["service"]) && empty($apiServices["0"]["service"])  ):
      die; 
    endif;
  
  
      foreach ($apiServices as $apiService):
        if( $service["api_service"] == $apiService["service"] ):
          $there[$service["service_id"]] = 1;
          $detail["min"]=$apiService["min"];$detail["max"]=$apiService["max"];$detail["rate"]=$apiService["rate"];$detail["currency"]=$balance->currency;$detail=json_encode($detail);
          $extras = json_decode($service["api_detail"],true);
          
          
          if($service["sync_price"] == 1 || true):
                               if( $apiService["rate"] != $extras['rate'] ):
          $realPrice = $extras['rate']; // First value (Real price)
$profitPrice = $service["service_price"]; // Second value (Price with profit)
$thirdValue = $apiService["rate"]; // Third value

// Calculate profit percentage
$profitPercentage = (($profitPrice - $realPrice) / $realPrice) * 100;

// Calculate new price for the third value
$thirdValueWithProfit = $thirdValue + ($thirdValue * ($profitPercentage / 100));
      echo $thirdValueWithProfit;
                $update = $conn->prepare("UPDATE services SET service_price=:rate WHERE service_id=:service ");
                $update->execute(array("service"=>$service["service_id"],"rate"=>$thirdValueWithProfit ));
            endif;
          endif;
          if($service["sync_min"] == 1):
              if( $apiService["min"] != $extras["min"] ):
                $update = $conn->prepare("UPDATE services SET service_min=:min WHERE service_id=:service ");
                $update->execute(array("service"=>$service["service_id"],"min"=>$apiService["min"] ));     
            endif;
          endif;
          if($service["sync_max"] == 1):
            if( $apiService["max"] != $extras["max"] ):
                $update = $conn->prepare("UPDATE services SET service_max=:max WHERE service_id=:service ");
                $update->execute(array("service"=>$service["service_id"],"max"=>$apiService["max"] ));     
            endif;
          endif;    
          
          $update = $conn->prepare("UPDATE services SET api_detail=:detail WHERE service_id=:service ");
          $update->execute(array("service"=>$service["service_id"],"detail"=>$detail));
          $detail = [];
        endif;
      endforeach;
  endforeach;
echo "Sync Successsfully.";