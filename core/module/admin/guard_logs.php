<?php

  if( $user["access"]["guard_logs"] != 1  ):
    header("Location:".site_url("admin"));
    exit();
  endif;

  $logs = $conn->prepare("SELECT * FROM guard_log INNER JOIN clients ON clients.client_id=guard_log.client_id ORDER BY guard_log.id DESC ");
  $logs->execute(array());
  $logs = $logs->fetchAll(PDO::FETCH_ASSOC);

  if( route(2) == "delete" ):
    $id     = route(3);
    $delete = $conn->prepare("DELETE FROM guard_log WHERE id=:id ");
    $delete->execute(array("id"=>$id));
    header("Location:".site_url("admin"));
  elseif( route(2) == "multi-action" ):
    $logs     = $_POST["log"];
    $action   = $_POST["bulkStatus"];
    foreach ($logs as $id => $value):
      $delete = $conn->prepare("DELETE FROM guard_log WHERE id=:id ");
      $delete->execute(array("id"=>$id));
    endforeach;
    header("Location:".site_url("admin/guard_logs"));
  endif;

require admin_view('guard_logs');
