<?php include 'header.php'; ?>


<div class="container-fluid">
  <div class="row">    
   <div class=" col-md-12">
   <ul class="nav nav-tabs">
   
          <a href="<?php echo site_url("admin/broadcasts/create") ?>" <button type="button" class="btn btn-default" data-toggle="modal" data-target="" data-action="">Create Notifications </button> </a>
   
    
	
	  
   </ul>
<br>
   <div class="row row-xs">

            <div class="col">
                <div class="card dwd-100">
                    <div class="card-body pd-20 table-responsive dof-inherit">
                        <div class="container-fluid pd-t-20 pd-b-20">
                            
                                    
   
   <table class="table order-table">
      <thead>
         <tr>
            <th width="5%">ID</th>
            <th width="20%">Title</th>
            <th width="30%">Action Link</th>
            <th width="10%">All Pages</th>
            <th width="10%">All Users</th>
            <th width="10%">Date Expiry</th>
            <th width="5%">Status</th>
            <th width="10%">Action</th>
         </tr>
      </thead>
      
        <tbody>
          <?php foreach($notifications as $notification ): ?>
              <tr>
                
                 <td><?php echo $notification["id"] ?></td>
                 <td><?php echo $notification["title"] ?></td>
                 <td><?php echo $notification["action_link"] ?></td>
                 <td><?php if($notification["isAllPage"]){ echo 'Yes';}else{ echo 'No';}   ?></td>
                 <td><?php if($notification["isAllUser"]){ echo 'No';}else{ echo 'Yes';}   ?></td>
                 <td><?php echo $notification["expiry_date"] ?></td>
                 <td><?php if($notification["status"] == 1){ echo 'Active';}else{ echo 'Inactive';}   ?></td>
                 <td>
                   <form id="changebulkForm" action="<?php echo site_url("admin/broadcasts/delete") ?>" method="post" onsubmit="return confirm('Do you want to delete it?');">
					   <div class="btn-group">
                     <input type="hidden" name="notification_id" value="<?php echo $notification["id"] ?>">
					   
					   <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                     
                     <a href="<?php echo site_url("admin/broadcasts/edit/".$notification["id"]) ?>" class="btn btn-info btn-xs">Edit</a>
					    </div>
                    </form>
                 </td>
              </tr>
            <?php endforeach; ?>
        </tbody>
        
   </table>
  
</div>
</div>
</div>

<?php

include 'footer.php'; ?>