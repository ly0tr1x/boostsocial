<?php include 'header.php'; ?>
  <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
 <div class="container container-md"> <div class="row"><div class="col-md-12">
  <div class="panel panel-default">
    <div class="panel-body">
      <form action="" method="post" enctype="multipart/form-data">
      

<h3><center>Increase Profit</center></h3>
<div class="form-group">
          <label for="" class="control-label">For Services</label>
          <select class="form-control" name="type">
         
                    <option value="1" >All Services</option>
 <option value="2" >Manual Services</option>
 <option value="3" >Api Services</option>
          
          </select>
        </div>
<div class="form-group">
          <label for="" class="control-label">Profit (%)</label>
          <input type="text" class="form-control" name="new_profit" >
        </div> 
<center><button type="submit" class="btn btn-primary">Increase Profit</button></center>
      </form>
    



<hr>
      <form action="admin/update-prices/decrease" method="post" enctype="multipart/form-data">
      

<h3><center>Decrease Profit</center></h3>
<div class="form-group">
          <label for="" class="control-label">For Services</label>
          <select class="form-control" name="type">
         
                    <option value="1" >All Services</option>
 <option value="2" >Manual Services</option>
 <option value="3" >Api Services</option>
          
          </select>
        </div>
<div class="form-group">
          <label for="" class="control-label">Profit (%)</label>
          <input type="text" class="form-control" name="new_profit" >
        </div> 

<hr>
        <center><button type="submit" class="btn btn-primary">Decrease Profit</button></center>
      </form>
    </div>
  </div>
</div>


<?php include 'footer.php'; ?>


