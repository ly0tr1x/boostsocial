<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="col-md-8">
            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
  <div class="settings-header__table">
     <button type="button"  class="btn btn-default m-b" data-toggle="modal" data-target="#modalDiv" data-action="new_provider" >Add New Provider</button> 
     
     
      <button id="rateUpdateBtn" type="button" class="btn btn-default m-b">Sync Button</button>
      
           <button type="button"  class="btn btn-default m-b" data-toggle="modal" data-action="capture_description" data-target="#modalDiv2" >Enlazar Descripcion</button> 

     
  </div>
  
  
  
  <div class="modal fade in" id="modalDiv2" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
<div class="modal-dialog" role="document" id="modalSize">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
<h4 class="modal-title" id="modalTitle">Enlazar descripciones</h4>
</div>
<div id="modalContent">
    
    <form class="form" action="https://boostsocialsmm.com/core/module/admin/capture_descriptions.php" method="post">

        <div class="modal-body">

          <div class="form-group">
            <label class="form-group__service-name">API URL</label>
            <select class="form-control" id="service_page_url" name="service_page_url">
                <option value="https://n1panel.com/services"> https://n1panel.com/services</option>
                                <option value="https://seguidorlatino.com/services"> https://seguidorlatino.com/services</option>

                                <option value="https://marketfollowers.com/services"> https://marketfollowers.com/services</option>
                <option value="https://growfollows.com/services"> https://growfollows.com/services</option>
                <option value="https://joysmm.net/services"> https://joysmm.net/services</option>
                <option value="https://smmfollows.com/services"> https://smmfollows.com/services</option>

                 </select>
          </div>
         

        </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Agregar descripciones</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
          </form>
  

    
</div>
</div>
</div>
</div>

  
  
  
  
  
  
  
  
  
  <script>

document.write('<center id=loading><img src="/img/ajax-loader-2.gif"></center>');
window.onload=function(){
    document.getElementById("loading").style.display="none";
}

</script>







<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">

<script>
$(document).ready(function() {
    $('#rateUpdateBtn').click(function() {
         Swal.fire({
            title: 'Please Wait(About 1min)',
            text: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: function() {
                Swal.showLoading();
            }
        });
        $.ajax({
            url: '/admin/cron-sync',
            type: 'POST',
             success: function(response) {
                if (response == 'Sync Successsfully.') {
                    Swal.fire({
                        title: 'Success',
                        text: 'Sync Successfully.',
                        icon: 'success'
                    });
                    setTimeout(function() {
location.reload();
}, 2000); // adjust the delay as needed
                } else {
                    Swal.fire({
                      title: 'Fail',
                        text: 'Fail to Sync.',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                      title: 'Fail',
                        text: 'Fail to Sync.',
                        icon: 'error'
                    });
            },
            complete: function() {
                setTimeout(function() {
                    Swal.close();
                }, 3000);
            }
        });
    });
});

 </script>


   				
<div class="providers"></div>


   