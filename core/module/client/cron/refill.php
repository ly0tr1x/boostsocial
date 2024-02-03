<?php

 

 $smmapi = new SMMApi();
 $refills = $conn->prepare("SELECT * FROM refill_status WHERE  refill_status != 'Completed'");
$refills->execute();
$refills = $refills->fetchAll(PDO::FETCH_ASSOC);
foreach ($refills as $refill){

$order_id_refill = $refill['order_id'];
 $refill_id = $refill["refill_apiid"];
$order  = $conn->prepare("SELECT * FROM orders WHERE order_id=:id");
$order  = $conn->prepare("SELECT * FROM orders INNER JOIN services ON services.service_id = orders.service_id INNER JOIN service_api ON services.service_api = service_api.id WHERE orders.order_id=:id ");
    $order ->execute(array("id"=>$order_id_refill));
    $order  = $order->fetch(PDO::FETCH_ASSOC);
    $order = json_decode(json_encode($order),true);

$refill_apiurl = $order["api_url"]; 
$get_refill_status    = $smmapi->action(array('key' =>$order["api_key"],'action' =>'refill_status','refill'=>$refill_id),$refill_apiurl);

$status = $get_refill_status->status;

$skipWord = 'tasks.status.';
$replacedText = str_replace($skipWord, '', $status);
$replacedText = ucfirst($replacedText);


if($replacedText){
$task_status = $replacedText;
 } else {
$task_status = "Refilling";
 }
  $update  = $conn->prepare("UPDATE refill_status SET refill_status=:refill_status WHERE order_id=:id ");
        $update ->execute(array("id"=>$refill['order_id'] , "refill_status"=>$task_status));
    
}

$cancel_orders = $conn->prepare("SELECT * FROM tasks WHERE task_status=:status && task_type=:type ");

$cancel_orders->execute(array(

    "status" => "inprogress",
    "type" => 1
));
$cancel_orders = $cancel_orders->fetchAll(PDO::FETCH_ASSOC);

foreach($cancel_orders as $cancel){

if($cancel["check_refill_status"] == 2){

$cancel_api_response = json_decode($cancel["res"],true);

if($cancel_api_response["status"] == "Success" || $cancel_api_response["status"] == "success"){

$update = $conn->prepare("UPDATE tasks SET task_status=:status,check_refill_status=:check WHERE task_type=:type");
$update->execute(array(
"status" => "canceled",
"check" => 1,
"type" => 1
));
} else {
$update = $conn->prepare("UPDATE tasks SET task_status=:status,check_refill_status=:check WHERE task_type=:type");
$update->execute(array(
"status" => "failed",
"check" => 1,
"type" => 1
));
}
}
}

 $currencies = $conn->prepare('SELECT * FROM currency WHERE status=:status');
$currencies->execute(array(
    'status' => 1
));
 $currencies = $currencies->fetchAll(PDO::FETCH_ASSOC);

 $settings = abcus("id", $settings["site_currency"], "name");
 $url = "http://www.floatrates.com/daily/" . strtolower($settings) . ".json";
 $ab = fspcurlwithoutpost($url);
 $floatrates_array = json_decode($ab, true);

 foreach ($currencies as $currency) {
     if ($currency["rate"] == 2) {
         $currency_code = $currency["name"];
         $lower_case_currency_code = strtolower($currency_code);
         $currency_rate = $floatrates_array[$lower_case_currency_code]["rate"];
         $currency_rate = number_format($currency_rate, 3, '.', '');
          $inverse_value = $floatrates_array[$lower_case_currency_code]["inverseRate"];
         $inverse_value = number_format($inverse_value, 3, '.', '');
         if ($currency_rate == 0.000) {
            $currency_rate = 1;
                  $inverse_value = 1;
        }
         $conn->beginTransaction();
         $update = $conn->prepare("UPDATE currency SET value=:value,inverse_value=:inverse_value WHERE name=:name");
        $update = $update->execute(array("value" => $currency_rate,"inverse_value" => $inverse_value, "name" => $currency_code));
         $conn->commit();
    }
}

 echo "Successfully updated rates.";
 


