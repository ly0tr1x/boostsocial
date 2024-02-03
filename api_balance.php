<?php

require __DIR__.'/core/lib/autoload.php';
require __DIR__.'/int/int.php';

$smmapi   = new SMMApi();

$api_details = $conn->prepare("SELECT * FROM service_api");
$api_details->execute(array());
$api_details = $api_details->fetchAll(PDO::FETCH_ASSOC);

if($user["access"]["providers"]){ ?>

<?php if(!$_GET["q"] == 1){ ?>


	<div class="form-group">

			
<table class="table providers_list">
	<thead>
		<tr>
			<th class="p-l" width="45%">Provider</th>
			<th>Balance</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	


<?php foreach($api_details as $provider): ?>				
				
				<?php
			 $balance = $smmapi->action(array('key' =>$provider["api_key"],'action' =>'balance'),$provider["api_url"]);
$balance1 = $balance->balance;
$balance2 = $balance->currency;
if($balance1 == null){
    $error = 1;
$call = '<i class="fas fa-question-circle"></i>';
}else{        
$error = 0;
$call = $balance1." ".$balance2;
}

?>			
		<tr <?php if($error == 1 ): echo 'class="grey"'; endif; ?> class="list_item ">
			<td class="name p-l"><?php echo $provider["api_name"]; ?> </td>
			<td><?=$call?></td>
			<td class="p-r">
 
			<button type="button" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#modalDiv" data-action="edit_provider" data-id="<?=$provider["id"]?>" >Edit</button> 
			</td>
	<td class="p-r">
<button type="button" class="btn btn-default des-default btn-xs pull-right" data-id="<?=$provider["id"]?>">Desc</button>

 			</td>
		
									<input type="hidden" name="privder_changes" value="privder_changes" >
					<?php endforeach; ?>  	
									</tbody>
</table>
	  </div>
</div>


<?php }else{ ?>


	<div class="form-group">

			
<table class="table providers_list">
	<thead>
		<tr>
			<th class="p-l" width="45%">Provider</th>
			<th>Balance</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	


<?php foreach($api_details as $provider): ?>				
				
							
		<tr id="" class="list_item ">
			<td class="name p-l"><?php echo $provider["api_name"]; ?> </td>
			<td>  <i class="fas fa-spinner fa-spin"></i>
</td>
			<td class="p-r">
 
				<button type="button" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#modalDiv" data-action="edit_provider" data-id="<?=$provider["id"]?>" >Edit</button>
			</td>
			<td class="p-r">
<button type="button" class="btn btn-default des-default btn-xs pull-right" data-id="<?=$provider["id"]?>">Desc</button>

 			</td>

		
									<input type="hidden" name="privder_changes" value="privder_changes" >
					<?php endforeach; ?>  	
									</tbody>
</table>
	  </div>
</div>


<?php } ?>

<?php  } ?>


 	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">

<script>
$(document).ready(function() {
    $('.des-default').click(function() {
        var id = $(this).data('id');
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
            url: '/admin/fetch',
            type: 'POST',
            data: {id: id},
            success: function(response) {
                if (response == 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: 'Fetched.',
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                      title: 'Fail',
                        text: 'Not Fetched.',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                  
                        title: 'Success',
                        text: 'Fetched.',
                        icon: 'success'
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
 
