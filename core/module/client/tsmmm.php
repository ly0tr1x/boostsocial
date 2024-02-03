<?php
 $dbh =$conn;
 $sql = "SHOW TABLE STATUS LIKE 'orders'";

 $result = $dbh->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$current_auto_increment_value = $row['Auto_increment'];
 

if($settings["fake_order_service_enabled"] == 2){
$min_orders = $settings["min"];
$max_orders = $settings["fake_order_max"];
$next_order_id = rand($min_orders,$max_orders);
$next_order_id = $current_auto_increment_value+$next_order_id;
 
 
 $sql = "ALTER TABLE orders AUTO_INCREMENT={$next_order_id}";

 if ($dbh->exec($sql)) {
  echo "AUTO_INCREMENT value updated successfully";
} else {
  echo "Error updating AUTO_INCREMENT value";
}

 $dbh = null;
}

