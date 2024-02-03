<?php include 'header.php'; ?>
<div class="container-fluid">
        <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
    
  <ul class="nav nav-tabs p-b">      
      <li class="pull-right custom-search">
         <form class="form-inline" action="<?=site_url("admin/tasks")?>" method="get">
            <div class="input-group">
               <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Search...">
               <span class="input-group-btn search-select-wrap">
                  <select class="form-control search-select" name="search_type">
                     <option value="order_id" <?php if( $search_where == "order_id" ): echo 'selected'; endif; ?> >Order ID</option>
                  </select>
                  <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
               </span>
            </div>
         </form>
      </li>
   </ul>
   <table class="table">
      <thead>
         <tr>
         <th>Cancel ID</th>
          <th>Order ID</th>
          <th>User</th>
          <th>Service</th>
          <th>Link</th>
          <th>Beginning</th>
          <th>Quantity</th>
          <th>Request</th>
          <th>Task Status</th>
          <th>Task Date</th>
          <th class="dropdown-th"></th>
         </tr>
      </thead>
      <form id="changebulkForm" action="<?php echo site_url("admin/tasks/multi-action") ?>" method="post">
        <tbody>
          <?php foreach( $orders as $order ): ?>
              <tr>
                 <td class="p-l"><?=$order["task_id"]?>
                 <div class="service-block__provider-value"><?php if($order["refill_orderid"]){ echo $order["refill_orderid"]; } ?></div></td>
                 <td><?php echo $order["order_id"] ?>
                 <div class="service-block__provider-value"><?php if($order["api_orderid"]){ echo $order["api_orderid"]; } ?></div></td>
                 <td><?php echo $order["username"]; ?></td>
                 <td><?php echo $order["service_name"]; ?></td>
                 <td><?php echo $order["order_url"]; ?></td>
                 <td><?php echo $order["order_start"]; ?></td>
                 <td><?php echo $order["order_quantity"]; ?></td>
                 <td>Cancel</td>
                 <td><?php if($order["task_status"] == "pending"): echo "Awaiting For Cancel"; elseif($order["task_status"] == "success"): echo "Confirmed"; elseif($order["task_status"] == "canceled"): echo "Denied"; endif; ?></td>
                 <td><?php echo $order["task_date"] ?></td>

                 <td class="service-block__action">
                     <div class="dropdown pull-right">
                     <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown" <?php if( $order["task_status"] !== "pending" ):  echo "disabled"; endif; ?>>Transactions <span class="caret"></span></button>
                       <ul class="dropdown-menu">
                           <li><a href="<?=site_url("admin/tasks/no/".$order["task_id"])?>">reject</a></li>
                           <?php if($order["task_type"] == 1){ ?>
                           <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/tasks/canceled/".$order["order_id"])?>">Cancel and refund</a></li>
                           <?php } ?>
                       </ul>
                     </div>               
                 </td>
              </tr>
            <?php endforeach; ?>
        </tbody>
        <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
      </form>
   </table>
   <?php if( $paginationArr["count"] > 1 ): ?>
     <div class="row">
        <div class="col-sm-8">
           <nav>
              <ul class="pagination">
                <?php if( $paginationArr["current"] != 1 ): ?>
                 <li class="prev"><a href="<?php echo site_url("admin/tasks/1/".$status.$search_link) ?>">&laquo;</a></li>
                 <li class="prev"><a href="<?php echo site_url("admin/tasks/".$paginationArr["previous"]."/".$status.$search_link) ?>">&lsaquo;</a></li>
                 <?php
                     endif;
                     for ($page=1; $page<=$pageCount; $page++):
                       if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
                 ?>
                 <li class="<?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/tasks/".$page."/".$status.$search_link) ?>"><?=$page?></a></li>
                 <?php endif; endfor;
                       if( $paginationArr["current"] != $paginationArr["count"] ):
                 ?>
                 <li class="next"><a href="<?php echo site_url("admin/tasks/".$paginationArr["next"]."/".$status.$search_link) ?>" data-page="1">&rsaquo;</a></li>
                 <li class="next"><a href="<?php echo site_url("admin/tasks/".$paginationArr["count"]."/".$status.$search_link) ?>" data-page="1">&raquo;</a></li>
                 <?php endif; ?>
              </ul>
           </nav>
        </div>
     </div>
   <?php endif; ?>
</div>
<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
   <div class="modal-dialog modal-dialog-center" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h4>Cancellation Confirmation</h4>
            <h5>If you cancel the order, the order will be canceled and the order fee will be refunded to your customer..</h5>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include 'footer.php'; ?>
