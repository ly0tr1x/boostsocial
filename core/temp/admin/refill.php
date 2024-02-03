<?php include 'header.php'; ?>

<div class="container-fluid">

 <ul class="nav nav-tabs">

      <li class="pull-right custom-search">
         <form class="form-inline" action="<?=site_url("admin/refill")?>" method="get">
            <div class="input-group">
               <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Search">
               <span class="input-group-btn search-select-wrap">
                  <select class="form-control search-select" name="search_type">
                     <option value="refill_apiid" <?php if( $search_where == "refill_apiid" ): echo 'selected'; endif; ?> >Refill ID</option>
                      <option value="order_id" <?php if( $search_where == "order_id" ): echo 'selected'; endif; ?> >Order ID</option>
                      
                     
                  </select>
                  <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
               </span>
            </div>
         </form> </li></ul>
         
         
          <table class="table">
            <thead>
                <tr stulr>
                    <td style="font-weight:600;" >ID</td>
                    <td style="font-weight:600;">Username</td>
                    <td style="font-weight:600;">Order ID</td>
                    
                      <td style="font-weight:600;">Link</td>
                     <td style="font-weight:600;">Service Name</td>
                    <td style="font-weight:600;">Date</td>
                    <td style="font-weight:600;">Status</td>
            
<td style="font-weight:600;">Action</td>
                </tr>
                </thead>
                
                
                
                
            <tbody>
             <?php foreach( $refills as $refill ): ?>
             <tr>
                  <td> <?php echo $refill["id"] ?>
               <?php if( $refill["refill_apiid"] != 0 ): echo '<div class="label label-api">'.$refill["refill_apiid"].'</div>'; endif; ?>
                </td>
                 
                 <td><?php echo $refill["username"]; if( $refill["refill_where"] == "api" ): echo ' <span class="label label-api">API</span>'; endif; ?> </td>
 
                     <td><a target="_blank" href="<?php site_url(); ?>/admin/orders?search=<?php echo $refill["order_id"]; ?>&search_type=order_id"><?php echo $refill["order_id"]; ?></a><?php if( $refill["order_apiid"] != 0 ): echo '<div class="label label-api">'.$refill["order_apiid"].'</div>'; endif; ?>
                </td>
                     
            <td><a target="_blank" href=<?php echo $refill["order_url"]; ?>><?php echo $refill["order_url"]; ?></a></td>
                     <td><?php echo $refill["service_name"]; ?></td>
                          <td><?php echo $refill["creation_date"]; ?></td>
                    <td><?php echo $refill["refill_status"]; ?></td>
                    <td class="service-block__action">
                   <div class="dropdown pull-right">
                     <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Action<span class="caret"></span></button>
                     <ul class="dropdown-menu">
                       
                       <li class="dropdown dropdown-submenu">
                           <a href="#" class="dropdown_menu">Update Refill Status</a>
                          <ul class="dropdown-menu submenu_drop">
                            
                            <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/refill/refill_refilling/".$refill["id"])?>">Refilling</a></li>
                              <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/refill/refill_complete/".$refill["id"])?>">Refill Complete</a></li>
                            
                              <li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/refill/refill_reject/".$refill["id"])?>">Refill Reject</a></li>
<li><a href="#" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/refill/refill_Error/".$refill["id"])?>">Refill Error</a></li>
             </tr>
               
             <?php endforeach ?>
            </tbody>
            </table>
     <?php if( $paginationArr["count"] > 1 ): ?>
     <div class="row">
        <div class="col-sm-8">
           <nav>
              <ul class="pagination">
                <?php if( $paginationArr["current"] != 1 ): ?>
                 <li class="prev"><a href="<?php echo site_url("admin/refill/1/".$status.$search_link) ?>">&laquo;</a></li>
                 <li class="prev"><a href="<?php echo site_url("admin/refill/".$paginationArr["previous"]."/".$status.$search_link) ?>">&lsaquo;</a></li>
                 <?php
                     endif;
                     for ($page=1; $page<=$pageCount; $page++):
                       if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
                 ?>
                 <li class="<?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/refill/".$page."/".$status.$search_link) ?>"><?=$page?></a></li>
                 <?php endif; endfor;
                       if( $paginationArr["current"] != $paginationArr["count"] ):
                 ?>
                 <li class="next"><a href="<?php echo site_url("admin/refill/".$paginationArr["next"]."/".$status.$search_link) ?>" data-page="1">&rsaquo;</a></li>
                 <li class="next"><a href="<?php echo site_url("admin/refill/".$paginationArr["count"]."/".$status.$search_link) ?>" data-page="1">&raquo;</a></li>
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
            <h4>Are you sure you want to update the status?</h4>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
         </div>
      </div>
   </div>
</div>











<?php include 'footer.php'; ?>