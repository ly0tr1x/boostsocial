<?php include 'header.php'; ?>

          <div class="container container-md"> <div class="row"><div class="col-md-12">
  <div class="panel panel-default">
    <div class="panel-body">
  <center> <h3>Edit Broadcast</h3></center>

          <hr>

          
     
        <?php
        
         $return = '<form class="form" action="'.site_url("admin/broadcasts/edit").'"  method="POST" id="new" name="new">
              <div class="form-group">
               <label class="form-group__service-name">Title</label>
               <input type="hidden" name="id" value="'.$notifData['id'].'">
                <input type="text" class="form-control" name="title" value="'.$notifData['title'].'" required>
              </div>
      
               <div class="form-group">
                        <label class="form-group__service-name">Action Link</label>
                        <input type="text" class="form-control" name="action_link" value="'.$notifData['action_link'].'">
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Button Text (Optional)</label>
                        <input type="text" class="form-control" name="action_text" value="'.$notifData['action_text'].'">
                </div>
              <div class="form-group">
    <div class="form-group">
                <label class="form-group__service-name">Icon</label>
                <select class="form-control" name="icon" required>
                  <option value="">Select an icon</option>
                  <option value="success" '.($notifData['icon'] == 'success' ? 'selected' : '').'>Success</option>
                  <option value="error" '.($notifData['icon'] == 'error' ? 'selected' : '').'>Error</option>
                  <option value="warning" '.($notifData['icon'] == 'warning' ? 'selected' : '').'>Warning</option>
                  <option value="info" '.($notifData['icon'] == 'info' ? 'selected' : '').'>Info</option>
                  <option value="question" '.($notifData['icon'] == 'question' ? 'selected' : '').'>Question</option>
                  <option value="custom" '.($notifData['icon'] == 'custom' ? 'selected' : '').'>Custom</option>
                </select>
              </div>


                <div class="form-group">
                        <label class="form-group__service-name">Description</label>
                    <!--    <textarea
						name="description"	class="textarea_editor form-control border-radius-0"
							placeholder="Enter text ..."
						required>'.$notifData['description'].'</textarea> -->
						
						 <textarea class="form-control" id="summernote" rows="5" name="description" placeholder="" required>'.$notifData['description'].'</textarea>

                         
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Display On All Pages</label>
                        <input type="checkbox" id="isAllPage" name="isAllPage" value="1"'.(($notifData['isAllPage']==1)? ' checked' : '').'>
                </div>
                <div class="form-group" id="allPages"'.(($notifData['isAllPage']==1)? ' style="display:none"' : '').'>
                        <label class="form-group__service-name">Select Pages</label>
                        <select multiple class="form-control" name="allPages[]">';
                            foreach($pages as $page){
                                $selected = (strpos($notifData['allPages'], $page['page_get']) !== false) ? ' selected' : '';
                                $return .= '<option value="'.$page['page_get'].'"'.$selected.'>'.$page['page_name'].'</option>';
                            }
                        $return .= '</select>
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Select Users</label>
                        <input type="radio" class="" name="isAllUser" value="0"'.(($notifData['isAllUser']==0)? ' checked' : '').'> All Users<br>
                        <input type="radio" class="" name="isAllUser" value="1"'.(($notifData['isAllUser']==1)? ' checked' : '').'> Logged-In User
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" value="'.$notifData['expiry_date'].'">
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Status</label>
                        <select class="form-control" name="status">
                                
                            <option value="0"'.(($notifData['status']==0)? ' selected' : '').'>Inactive</option>
                            <option value="1"'.(($notifData['status']==1)? ' selected' : '').'>Active</option>
                        </select>    
                </div>
              <button type="submit" class="btn btn-primary">Update</button>
        </form>';
         echo $return;
        ?>
  


<?php include 'footer.php'; ?>
<script>
$('input#isAllPage').change(function() {
if ($('input#isAllPage').prop('checked')) {    
   $('div#allPages').hide();
}else{
    $('div#allPages').show();
}

});
</script>