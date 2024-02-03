<?php

 
 
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





 if( $_GET["search_type"] == "refill_apiid" && $_GET["search"] ):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);
      $count        = $conn->prepare("SELECT * FROM refill_status");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE refill_apiid LIKE '%".$search_word."%' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
        
  
    $to             = 25;
    $pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
    $where          = ($page*$to)-$to;
    $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
    $refills         = $conn->prepare("SELECT * FROM refill_status INNER JOIN clients ON clients.client_id = refill_status.client_id WHERE refill_apiid=:refill_apiid  LIMIT $where, $to ");
    $refills -> execute(array("refill_apiid"=>$search_word));
    $refills  = $refills->fetchAll(PDO::FETCH_ASSOC);
    
    elseif( $_GET["search_type"] == "order_id" && $_GET["search"] ):
      $search_where = $_GET["search_type"];
      $search_word  = urldecode($_GET["search"]);
      $count        = $conn->prepare("SELECT * FROM refill_status");
      $count        -> execute(array());
      $count        = $count->rowCount();
      $search       = "WHERE order_id LIKE '%".$search_word."%' ";
      $search_link  = "?search=".$search_word."&search_type=".$search_where;
        
  
    $to             = 25;
    $pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
    $where          = ($page*$to)-$to;
    $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
    $refills         = $conn->prepare("SELECT * FROM refill_status INNER JOIN clients ON clients.client_id = refill_status.client_id WHERE order_id=:order_id  LIMIT $where, $to ");
    $refills -> execute(array("order_id"=>$search_word));
    $refills  = $refills->fetchAll(PDO::FETCH_ASSOC);
    
    
    
  
  
    
      
    else :
$count        = $conn->prepare("SELECT * FROM refill_status ");
$count        -> execute(array());
$count        = $count->rowCount();
$to             = 25;
$pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
$where          = ($page*$to)-$to;
$paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
$refills = $conn->prepare("SELECT * FROM refill_status INNER JOIN clients ON clients.client_id = refill_status.client_id ORDER BY id DESC LIMIT $where, $to");
$refills->execute(array());
$refills = $refills->fetchAll(PDO::FETCH_ASSOC);

    
    
  endif;
  
  
  
if( route(2) == "multi-action" ):
          $orders   = $_POST["order"];
          $action   = $_POST["bulkStatus"];
          if( $action ==  "Pending" ):
            foreach ($orders as $id => $value):
              $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
              $update->execute(array("status"=>"Pending","id"=>$id));
            endforeach;
          elseif( $action ==  "Refilling" ):
            foreach ($orders as $id => $value):
              $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
              $update->execute(array("status"=>"Refilling","id"=>$id));
            endforeach;
          elseif( $action ==  "Completed" ):
            foreach ($orders as $id => $value):
              $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
              $update->execute(array("status"=>"Completed","id"=>$id));
            endforeach;
elseif( $action ==  "Rejected" ):
            foreach ($orders as $id => $value):
              $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
              $update->execute(array("status"=>"Rejected","id"=>$id));
            endforeach;
elseif( $action ==  "Error" ):
            foreach ($orders as $id => $value):
              $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
              $update->execute(array("status"=>"Error","id"=>$id));
            endforeach;
          endif;
          header("Location:".site_url("admin/refill"));
        
      exit();
    endif;
if( route(2) == "refill_Pending" ):
    $id     = route(3);
    $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
    $update->execute(array("status"=>"Pending","id"=>$id));header("Location:".site_url("admin/refill"));
elseif( route(2) == "refill_Error" ):
    $id     = route(3);
    $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
    $update->execute(array("status"=>"Error","id"=>$id));header("Location:".site_url("admin/refill"));
  elseif( route(2) == "refill_refilling" ):
    $id     = route(3);
    $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
    $update->execute(array("status"=>"Refilling","id"=>$id));

  elseif( route(2) == "refill_complete" ):
    $id     = route(3);
    $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
    $update->execute(array("status"=>"Completed","id"=>$id));
    header("Location:".site_url("admin/refill"));
  elseif( route(2) == "refill_reject" ):
    $id     = route(3);
    $update = $conn->prepare("UPDATE refill_status SET refill_status=:status WHERE id=:id ");
    $update->execute(array("status"=>"Rejected","id"=>$id));
    header("Location:".site_url("admin/refill"));
endif;
  
  
 require admin_view('refill');