<?php include 'header.php'; ?>

 <div class="container-fluid">
   <div class="row">
      
      <div class="col-lg-12">
          <ul class="nav nav-tabs p-b">
     <li class=""><a href="/admin/logs">System Logs</a></li>
     <li><a href="/admin/provider_logs">Provider Logs</a></li>        
     <li class="active"><a href="/admin/guard_logs">Protection Logs <?php if(countRow(['table'=>'guard_log'])): ?>
<span class='badge' style='background-color: #6d47bb'><?=countRow(['table'=>'guard_log']);?></span>
<?php endif; ?></a></li>        
    
   </ul>
   <div class="alert alert-info">
       Delete the protection logs you see in order to remove the warnings from the menu.!<hr>
      The number of protection logs appears in the menu as a warning so that important situations are not overlooked. 
   </div>
   
         <div class="panel panel-default">
            <div class="panel-body">   
              <h4>Protection Logs</h4>
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
                                         <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> batch operations<span class="caret"></span></button>
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
                          <th>Official</th>
                           <th>Event</th>
                           <th>History</th>
                           <th>Detailed IP</th>
                           <th></th>
                        </tr>
                     </thead>
                     <form id="changebulkForm" action="<?php echo site_url("admin/guard_logs/multi-action") ?>" method="post">
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
                             <td><?php echo date("H:i:s d.m.Y"); ?></td>
                             <td><?php $s = $log["ip"]; echo "<a href='https://ipapi.co/$s/json/'><i class='fa fa-search'></i> view </a>"; ?></td>
                             <td> <a href="<?php echo site_url("admin/guard_logs/delete/".$log["id"]) ?>" style="font-size:12px">delete</a> </td>
                          </tr>
                        <?php endforeach; ?>
                       </tbody>
                       <input type="hidden" name="bulkStatus" id="bulkStatus" value="0">
                     </form>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
   <div class="modal-dialog modal-dialog-center" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h4>Are you sure you want to perform the operation?</h4>
            <div align="center">
               <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include 'footer.php'; ?>
