<?php include 'header.php'; ?>
<div class="container-fluid">

 <ul class="nav nav-tabs p-b">
        
     <li class="active"><a href="/admin/logs">System Logs</a></li>
     <li ><a href="/admin/provider_logs">Provider Logs</a></li>
          <li ><a href="/admin/guard_logs">Protection Logs <?php if(countRow(['table'=>'guard_log'])): ?>
<span class='badge' style='background-color: #6d47bb'><?=countRow(['table'=>'guard_log']);?></span>
<?php endif; ?></a></li>        

    
     <li class="pull-right custom-search">
        <form class="form-inline" action="<?=site_url("admin/logs")?>" method="get">
           <div class="input-group">
              <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Word">
              <span class="input-group-btn search-select-wrap">
                 <select class="form-control search-select" name="search_type">
                    <option value="username" <?php if( $search_where == "username" ): echo 'selected'; endif; ?> >Username</option>
                    <option value="action" <?php if( $search_where == "action" ): echo 'selected'; endif; ?> >Event</option>
                 </select>
                 <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
              </span>
           </div>
        </form>
     </li>
  </ul>

   <div class="row">
      <div class="col-lg-12">
         <div class="panel panel-default">         

            <!-- /.panel-heading -->
            <div class="panel-body">
                 <h4>Loglar</h4>
               <div class="table-responsive">
                  <table class="table">
                     <thead>
                        <tr>
                          <th class="checkAll-th">
                             <div class="checkAll-holder">
                                <input type="checkbox" id="checkAll">
                                <input type="hidden" id="checkAllText" value="order">
                             </div>
                             <div class="action-block">
                                <ul class="action-list" style="margin:5px 0 0 0!important">
                                   <li><span class="countOrders"></span> log selected</li>
                                   <li>
                                      <div class="dropdown">
                                         <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> batch operations <span class="caret"></span></button>
                                         <ul class="dropdown-menu">
                                            <li>
                                              <a class="bulkorder" data-type="delete">delete</a>
                                            </li>
                                         </ul>
                                      </div>
                                   </li>
                                </ul>
                             </div>
                          </th>
                           <th>Username</th>
                           <th>Detail</th>
                           <th>IP Address</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <form id="changebulkForm" action="<?php echo site_url("admin/logs/multi-action") ?>" method="post">
                       <tbody>
                         <?php if( !$logs ): ?>
                           <tr>
                             <td colspan="4"><center>No logs found</center></td>
                           </tr>
                         <?php endif; ?>
                         <?php foreach($logs as $log): ?>
                          <tr>
                            <td><input type="checkbox" class="selectOrder" name="log[<?php echo $log["id"] ?>]" value="1" style="border:1px solid #fff"></td>
                             <td><?php echo $log["username"] ?></td>
                             <td><?php echo $log["action"] ?></td>
                             <td><a href="https://dnschecker.org/ip-location.php?ip=<?php echo $log["report_ip"] ?>"  target="_blank"><?php echo $log["report_ip"] ?></a></td>
                             <td><?php echo $log["report_date"] ?></td>
                          </tr>
                        <?php endforeach; ?>
                       </tbody>
                       <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
                     </form>
                  </table>
               </div>
            </div>
         </div>
         <?php if( $paginationArr["count"] > 1 ): ?>
           <div class="row">
              <div class="col-sm-8">
                 <nav>
                    <ul class="pagination">
                      <?php if( $paginationArr["current"] != 1 ): ?>
                       <li class="prev"><a href="<?php echo site_url("admin/logs/1/".$search_link) ?>">&laquo;</a></li>
                       <li class="prev"><a href="<?php echo site_url("admin/logs/".$paginationArr["previous"]."/".$search_link) ?>">&lsaquo;</a></li>
                       <?php
                           endif;
                           for ($page=1; $page<=$pageCount; $page++):
                             if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
                       ?>
                       <li class="<?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> "><a href="<?php echo site_url("admin/logs/".$page."/".$status.$search_link) ?>"><?=$page?></a></li>
                       <?php endif; endfor;
                             if( $paginationArr["current"] != $paginationArr["count"] ):
                       ?>
                       <li class="next"><a href="<?php echo site_url("admin/logs/".$paginationArr["next"]."/".$search_link) ?>" data-page="1">&rsaquo;</a></li>
                       <li class="next"><a href="<?php echo site_url("admin/logs/".$paginationArr["count"]."/".$search_link) ?>" data-page="1">&raquo;</a></li>
                       <?php endif; ?>
                    </ul>
                 </nav>
              </div>
           </div>
         <?php endif; ?>
      </div>
   </div>
</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
   <div class="modal-dialog modal-dialog-center" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h4>Are you sure you want to perform the operation??</h4>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include 'footer.php'; ?>
