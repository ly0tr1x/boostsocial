<?php

$route[0]	=	"neworder";
$title .= $languageArray["neworder.title"];

$order	=	$conn->prepare("SELECT * FROM orders INNER JOIN services ON services.service_id=orders.service_id WHERE client_id=:client && order_id=:orderid ");
$order-> execute(array("client"=>$user["client_id"],"orderid"=>route(1) ));

if( !$order->rowCount() ):
	header("Location:".site_url());
else:
	$order	=	$order->fetch(PDO::FETCH_ASSOC);
	$order_data                     = ['success'=>1,'id'=>route(1),"service"=>$order["service_name"],"link"=>$order["order_url"],"quantity"=>$order["order_quantity"],"price"=>$order["order_charge"],"balance"=>$user["balance"] ];
	$_SESSION["data"]["services"]   = $order["service_id"];
	$_SESSION["data"]["categories"] = $order["category_id"];
	$_SESSION["data"]["order"]      = $order_data;
endif;


if( $_SESSION["neira_userlogin"] != 1  || $user["client_type"] == 1  ){
  header("Location:".site_url('logout'));
}

if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }
endif;
     $news = $conn->prepare("SELECT * FROM news ORDER BY news_date DESC");
  $news-> execute(array());
  $news = $news->fetchAll(PDO::FETCH_ASSOC);
  $newsList = [];
    foreach ($news as $new) {
      foreach ($new as $key => $value) {
          $t[$key] = $value;
      }
      array_push($newsList,$t);
    }
 
$categoriesRows = $conn->prepare("SELECT * FROM categories WHERE category_type=:type  ORDER BY categories.category_line ASC ");
$categoriesRows->execute(array("type"=>2));
$categoriesRows = $categoriesRows->fetchAll(PDO::FETCH_ASSOC);

$categories = [];
  
  foreach ( $categoriesRows as $categoryRow ) {
    $search = $conn->prepare("SELECT * FROM clients_category WHERE category_id=:category && client_id=:c_id ");
    $search->execute(array("category"=>$categoryRow["category_id"],"c_id"=>$user["client_id"]));
    if( $categoryRow["category_secret"] == 2 || $search->rowCount() ):
      $rows     = $conn->prepare("SELECT * FROM services WHERE category_id=:id ORDER BY service_line ASC");
      $rows     ->execute(array("id"=>$categoryRow["category_id"] ));
      $rows     = $rows->fetchAll(PDO::FETCH_ASSOC);
      $services = [];
        foreach ( $rows as $row ) {
          $s["service_price"] = service_price($row["service_id"]);
          $s["service_id"]    = $row["service_id"];
						$multiName   =  json_decode($row["name_lang"],true);
							if( $multiName[$user["lang"]] ):
								$s["service_name"] = $multiName[$user["lang"]];
							else:
								$s["service_name"] = $row["service_name"];
							endif;
          $s["service_min"]   = $row["service_min"];
          $s["service_max"]   = $row["service_max"];
          $search = $conn->prepare("SELECT * FROM clients_service WHERE service_id=:service && client_id=:c_id ");
          $search->execute(array("service"=>$row["service_id"],"c_id"=>$user["client_id"]));
          if( $row["service_secret"] == 2 || $search->rowCount() ):
            array_push($services,$s);
          endif;
        }
      $c["category_name"]          = $categoryRow["category_name"];
      $c["category_id"]            = $categoryRow["category_id"];
      $c["services"]               = $services;
      array_push($categories,$c);
    endif;

  }
