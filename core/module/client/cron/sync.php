<?php



 
 
$smmapi   = new SMMApi();

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
