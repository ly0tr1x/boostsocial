<?php

$titleAdmin = "Reports";

  if( $user["access"]["reports"] != 1  ):
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

  $services       = $conn->prepare("SELECT * FROM services RIGHT JOIN categories ON categories.category_id = services.category_id LEFT JOIN service_api ON service_api.id = services.service_api ORDER BY categories.category_line,services.service_line ASC ");
  $services       -> execute(array());
  $services       = $services->fetchAll(PDO::FETCH_ASSOC);
  $serviceList    = array_group_by($services, 'category_name');

  if( !route(2) ):
    $action = "profit";
    $years    = $conn->query("SELECT order_create FROM orders GROUP BY YEAR(order_create) ORDER BY YEAR(order_create) ASC")->fetchAll(PDO::FETCH_ASSOC);
    $yearList = []; $i=0;
    foreach ($years as $key) {
      $yearList[$i] = date("Y",strtotime($key["order_create"]));
      $i+=1;
    }
  else:
    $action = route(2);
      if( $action == "orders" || $action == "profit" ):
        $years    = $conn->query("SELECT order_create FROM orders GROUP BY YEAR(order_create) ORDER BY YEAR(order_create) ASC")->fetchAll(PDO::FETCH_ASSOC);
        $yearList = []; $i=0;
        foreach ($years as $key) {
          $yearList[$i] = date("Y",strtotime($key["order_create"]));
          $i+=1;
        }
      elseif( $action == "payments" ):
        $methods  = $conn->prepare("SELECT * FROM payment_methods");
        $methods->execute(array());
        $methods  = $methods->fetchAll(PDO::FETCH_ASSOC);
        $years    = $conn->query("SELECT payment_create_date FROM payments GROUP BY YEAR(payment_create_date) ORDER BY YEAR(payment_create_date) ASC")->fetchAll(PDO::FETCH_ASSOC);
        $yearList = []; $i=0;
        foreach ($years as $key) {
          $yearList[$i] = date("Y",strtotime($key["payment_create_date"]));
          $i+=1;
        }
      endif;
  endif;

  if( count($yearList) == 0 ): $yearList[0] = date("Y"); endif;

  if( $_GET["year"] ):
    $year = $_GET["year"];
  else:
    $year = date("Y");
  endif;


  require admin_view('reports');
