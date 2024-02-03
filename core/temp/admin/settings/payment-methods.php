<div class="col-md-8">
<div class="settings-header__table">

<div class="settings-header__table">
<a href="/admin/settings/bank-accounts">
<button type="button" class="btn btn-default m-b">Bank Accounts</button>
</a>
</div>

            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
   <table class="table">
      <thead>
         <tr>
            <th>
               <span class="table__drag-header">Method</span>
            </th>
            <th>Visible Name</th>
            <th>Min</th>
            <th>Max</th>
            <th>Status</th>
            <th></th>
         </tr>
      </thead>
      <tbody class="methods-sortable">
         <?php foreach($methodList as $method): $extra = json_decode($method["method_extras"],true); ?>
           <tr class="<?php if( $method["method_type"] == 1 ): echo "grey"; endif; ?>"  data-toggle="<?php echo $method["id"]; ?>" data-id="<?php echo $method["id"]; ?>">
            <td>
                 <div class="table__drag handle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                       <title>Drag-Handle</title>
                       <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                    </svg>
                 </div>
               <?php echo $method["method_name"]; ?>
            </td>
            <td><?php echo $extra["name"]; ?></td>
            <td><?php echo $method["method_min"]; ?></td>
            <td><?php  if($method["method_max"] == 0 ){
            echo("∞");
            }else{
             echo $method["method_max"];
            }
            ?></td>
            <td> <input type="checkbox" class="tiny-toggle" data-tt-palette="blue" data-url="<?=site_url("admin/settings/payment-methods/type")?>" data-id="<?php echo $method["id"]; ?>" <?php if( $method["method_type"] == 2 ): echo "checked"; endif; ?>> </td>
            <td class="p-r">
               <button type="button" class="btn btn-default btn-xs pull-right edit-payment-method" data-toggle="modal" data-target="#modalDiv" data-action="edit_paymentmethod" data-id="<?php echo $method["method_get"]; ?>">Edit</button>
            </td>
         </tr>
         <?php endforeach; ?>
         
      </tbody>
   </table>
</div>
