
 
    
<div class="col-md-8">
    
    
     
    
    
  <div class="panel panel-default">
    <div class="panel-body">
<h1>Fake Orders</h1>
 
   
<div class="form-group">
<center><div class="alert alert-info">Order ID Skip [Per 5 Minute]</div></center>
</div>
<form method="post" action="/admin/settings/site_counts">
  <div class="form-group">
    <label class="control-label">ONN/OFF mode</label>
    <select class="form-control" name="fake_order_service_enabled"> 
      <option value="1" <?= $settings["fake_order_service_enabled"] == 1 ? "selected" : null; ?>>Off</option>
      <option value="2" <?= $settings["fake_order_service_enabled"] == 2 ? "selected" : null; ?> >Onn</option>
    </select>
  </div> 
<div class="form-group">
          <label class="control-label">Min</label>

    <input class="form-control" type="number" name="min" value="<?php if(is_numeric($settings["min"])){ echo $settings["min"]; } ?>">
  </div>

  <div class="form-group">
          <label class="control-label">Max</label>

    <input class="form-control" type="number" name="fake_order_max" value="<?php if(is_numeric($settings["fake_order_max"])){ echo $settings["fake_order_max"]; } ?>">
  </div>

  <div class="form-group">
    <button class="btn btn-primary" type="submit">Submit</button>
  </div>
</form>





</div>
