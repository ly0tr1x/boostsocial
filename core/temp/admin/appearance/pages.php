<?php if( !route(4) ): ?>
<div class="col-md-8">
   <table class="table">
      <thead>
         <tr>
            <th>Page Name</th>
            <th></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach($pageList as $page): ?>
         <tr>
            <td> <?php echo $page["page_name"]; ?> </td>
            <td class="text-right col-md-1">
               <div class="dropdown">
                  <a href="<?php echo site_url('admin/appearance/pages/edit/'.$page["page_get"]) ?>" class="btn btn-default btn-xs">
                  Edit
                  </a>
               </div>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</div>
<?php elseif( route(3) == "edit" ): ?>
<div class="col-md-8">
   <div class="panel panel-default">
      <div class="panel-body">
         <form action="<?php echo site_url('admin/
appearance/pages/edit/'.route(4)) ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
               <label class="control-label">Page Name</label>
               <input type="text" class="form-control" readonly value="<?=$page["page_name"];?>">
            </div>
            <div class="form-group">
               <label class="control-label">Contents</label>
               <textarea class="form-control" id="summernote" rows="5" name="content" placeholder=""><?php echo $page["page_content"]; ?></textarea>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Update</button>
              <a href="/admin/appearance/pages" class="btn btn-default">Back</a>
         </form>
      </div>
   </div>
</div>
<?php endif; ?>
