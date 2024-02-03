<?php include 'header.php'; ?>

 <div class="container container-md"> <div class="row"><div class="col-md-12">
  <div class="panel panel-default">
    <div class="panel-body">
  <center> <h3>Create Broadcast</h3></center>

          <hr>
          <?php 
           $return = '<form class="form" action="' . site_url("admin/broadcasts/new") . '" method="POST">
              <div class="form-group">
               <label class="form-group__service-name">Title</label>
                <input type="text" class="form-control" name="title" value="" required>
              </div>
              <div class="form-group">
                        <label class="form-group__service-name">Button Link (Optional)</label>
                        <input type="text" class="form-control" name="action_link" value="">
                </div>
               <div class="form-group">
                        <label class="form-group__service-name">Button Text (Optional)</label>
                        <input type="text" class="form-control" name="action_text" value="">
                </div>
                 <div class="form-group">
    <label class="form-group__service-name">Icon</label>
    <select class="form-control" name="icon" required>
        <option value="">Select an icon</option>
        <option value="success" >Success</option>
        <option value="error">Error</option>
        <option value="warning">Warning</option>
        <option value="info">Info</option>
        <option value="question">Question</option>
        <option value="custom">Custom</option>
    </select>
</div>
                <div class="form-group">
                        <label class="form-group__service-name">Description</label>
<!-- <textarea
						name="description"	class="textarea_editor form-control border-radius-0"
							placeholder="Enter text ..."
						required></textarea> -->
						
						<textarea class="form-control" id="summernote" rows="5" name="description" placeholder=""></textarea>
						
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Display On All Pages</label>
                        <input type="checkbox" id="isAllPage" name="isAllPage" value="1">
                </div>
                <div class="form-group" id="allPages">
                        <label class="form-group__service-name">Select Pages</label>
                        <select multiple class="form-control" name="allPages[]">';
foreach ($pages as $page) { 
    $return .= '<option value="' . $page['page_get'] . '">' . $page['page_name'] . '</option>';
}
$return .= '</select>
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Select Users</label>
                        <input type="radio" class="" name="isAllUser" value="0"> All Users</br>
                        <input type="radio" class="" name="isAllUser" value="1"> Logged-In User
                </div>
                <div class="form-group">
                        <label class="form-group__service-name">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" value="">
                </div>
              <button type="submit" class="btn btn-primary">Submit</button>
        </form>
';
     echo $return; ?>
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