<div class="col-md-8">
            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
  <div class="panel panel-default">
    <div class="panel-body">
        
      <form action="" method="post" enctype="multipart/form-data">

        
<div class="settings-emails__block">
    <div class="settings-emails__block-title">
    Notification Settings   </div>
    <div class="settings-emails__block-body">
        <table>
            <thead>
            <tr>
                <th class="settings-emails__th-name"></th>
                <th class="settings-emails__th-actions"></th>
            </tr>
            </thead>
            <tbody>
                            <tr class="settings-emails__row <?php if($settings["alert_apibalance"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">API Low Balance</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_apibalance"] == 1){ echo 'grey'; } ?>">
                    You'll get a notification if your balance drops below the amount you set.                 </div>
                </td>
                <td class="settings-emails__td-actions">
                                                
                          <?php if($settings["alert_apibalance"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_apibalance">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_apibalance">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>

            </tr> 
           
         
                            <tr class="settings-emails__row <?php if($settings["alert_newmanuelservice"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">New Manual Order Notification</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_newmanuelservice"] == 1){ echo 'grey'; } ?>">
                    Notification sent if new manual orders are received.</div>
                </td>
                <td class="settings-emails__td-actions">
                        
              <?php if($settings["alert_newmanuelservice"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_newmanuelservice">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_newmanuelservice">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
                 
            </tr>
                       
                      
                            <tr class="settings-emails__row <?php if($settings["alert_newpayment"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">New Payment Notice</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_newpayment"] == 1){ echo 'grey'; } ?>">
                    Notification will be sent if new payment is received.</div>
                </td>
                <td class="settings-emails__td-actions">
                     
                 <?php if($settings["alert_newpayment"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_newpayment">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_newpayment">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
                 
            </tr>
                       
             <tr class="settings-emails__row <?php if($settings["alert_newbankpayment"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">New Bank Payment Request Notification</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_newbankpayment"] == 1){ echo 'grey'; } ?>">
                    Notification will be sent if new payment request is received.</div>
                </td>
                <td class="settings-emails__td-actions">

                 <?php if($settings["alert_newbankpayment"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_newbankpayment">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_newbankpayment">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
                 
            </tr>
                       
             
                            <tr class="settings-emails__row <?php if($settings["alert_failorder"] == 1){ echo 'grey'; } ?> ">
                <td>
                    <div class="settings-emails__row-name">New Failed Order Notification</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_failorder"] == 1){ echo 'grey'; } ?>">
                    Notification will be sent if new failed order is received.</div>
                </td>
                <td class="settings-emails__td-actions">
                      
             <?php if($settings["alert_failorder"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_failorder">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_failorder">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
                 
            </tr>
                      </div>
                      
                      
                            <tr class="settings-emails__row <?php if($settings["alert_serviceapialert"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">Changing Provider Information</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_serviceapialert"] == 1){ echo 'grey'; } ?>">
                    You will receive notifications of All Updates Related to the Services at the Service Provider.</div>
                </td>
                <td class="settings-emails__td-actions">
                       

                    <?php if($settings["alert_serviceapialert"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_serviceapialert">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_serviceapialert">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
            </tr>
                        
                            <tr class="settings-emails__row <?php if($settings["alert_newticket"] == 1){ echo 'grey'; } ?>">
                <td>
                    <div class="settings-emails__row-name">New Support Notice</div>
                    <div class="settings-emails__row-description <?php if($settings["alert_newticket"] == 1){ echo 'grey'; } ?>">
                    You will be notified when a new ticket is created.                    </div>
                </td>
                <td class="settings-emails__td-actions">
                
                          <?php if($settings["alert_newticket"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                       <a href="/admin/settings/alert/off/alert_newticket">
<span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/settings/alert/on/alert_newticket">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                </td>
            </tr>
                         
                        </tbody>
        </table>
    </div>
</div>
<div class="settings-emails__block">
            <div class="settings-emails__block-title">Authorized Information</div>
        <div class="settings-emails__block-body">
            <table>
                <thead>
                <tr>
                    <th class="settings-emails__th-email-name"></th>
                    <th class="settings-emails__th-email-count"></th>
                    <th class="settings-emails__th-actions"></th>
                </tr>
                </thead>
                <tbody>
                                    <tr class="settings-emails__row">
                        <td>
                            <div class="settings-emails__row-name"><?=$settings["admin_mail"]?></div>
                        </td>
                        <td>
                            <div class="settings-emails__row-emails-count">
<?php

$count = 0;
if($settings["alert_apibalance"] == 2 ){
    $count = $count+1;
}
if($settings["alert_failorder"] == 2 ){
    $count = $count+1;
}
if($settings["alert_newmanuelservice"] == 2 ){
    $count = $count+1;
}
if($settings["alert_serviceapialert"] == 2 ){
    $count = $count+1;
}
if($settings["alert_newbankpayment"] == 2 ){
    $count = $count+1;
}
if($settings["alert_newpayment"] == 2 ){
    $count = $count+1;
}
if($settings["alert_newticket"] == 2 ){
    $count = $count+1;
}


                            echo "$count Aktif bildirim"; ?>
                            </div>
                        </td>
                        <td class="settings-emails__td-actions">
                       <a href="javascript:;" onclick="showMe('gizlebeni');" class="btn btn-xs btn-default">
                       Edit                    </a>
                        </td>
                    </tr>
                   
                                </tbody>
            </table>
        </div>

</div>   
<div id="gizlebeni" class="row" style="display: none;">
            
             <div class="form-group col-md-6">
           <label class="control-label">Email Address to Receive Notification</label>
            <input type="text" class="form-control" name="admin_mail" value="<?=$settings["admin_mail"]?>">
            </div>
            
            <div class="form-group col-md-6">
            <label class="control-label">Notification Phone Number</label>
            <input type="text" class="form-control" name="admin_telephone" value="<?=$settings["admin_telephone"]?>">
            </div>
			
             </div>
<hr>
        <div class="row">
          <div class="col-md-12 form-group">
            <label class="control-label">Notification Form</label>
            <select class="form-control" name="alert_type">
              <option value="3" <?= $settings["alert_type"] == 3 ? "selected" : null; ?> >Email and SMS</option>
              <option value="2" <?= $settings["alert_type"] == 2 ? "selected" : null; ?>>Email</option>
              <option value="1" <?= $settings["alert_type"] == 1 ? "selected" : null; ?>>SMS</option>
            </select>
          </div>   
        <div class="form-group col-md-6">
            <label class="control-label">Password Renewal via SMS</label>
            <select class="form-control" name="resetsms">
              <option value="2" <?= $settings["resetpass_sms"] == 2 ? "selected" : null; ?> >Active</option>
              <option value="1" <?= $settings["resetpass_sms"] == 1 ? "selected" : null; ?>>Passive</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Password Renewal by Email</label>
            <select class="form-control" name="resetmail">
              <option value="2" <?= $settings["resetpass_email"] == 2 ? "selected" : null; ?> >Active</option>
              <option value="1" <?= $settings["resetpass_email"] == 1 ? "selected" : null; ?>>Passive</option>
            </select>
          </div>   

          <div class="col-md-12 help-block">
         <small>En sağlıklı bildirim şekli SMS'tir.</small>
        </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-3 form-group">
            <label class="control-label">SMS Provider</label>
            <select class="form-control" name="sms_provider">
              <option value="bizimsms" <?= $settings["sms_provider"] == "bizimsms" ? "selected" : null; ?> >Bizimsms</option>
              <option value="netgsm" <?= $settings["sms_provider"] == "netgsm" ? "selected" : null; ?> >NetGSM</option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">SMS Header</label>
            <input type="text" class="form-control" name="sms_title" value="<?=$settings["sms_title"]?>">
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Username</label>
            <input type="text" class="form-control" name="sms_user" value="<?=$settings["sms_user"]?>">
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">password</label>
            <input type="text" class="form-control" name="sms_pass" value="<?=$settings["sms_pass"]?>"> 
          </div>
          <div class="col-md-12 help-block">
         <small><i class="fa fa-warning"></i> <code>Bizim SMS</code> Type your API Key in the password section for.</small><br>
         <small>Pay attention to Turkish characters when writing the SMS Title..</small>
         </div>
        </div>
        <hr>
        <div class="row">
          <div class="form-group col-md-6">
            <label class="control-label">Email</label>
            <input type="text" class="form-control" name="smtp_user" value="<?=$settings["smtp_user"]?>">
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">E mail password</label>
            <input type="text" class="form-control" name="smtp_pass" value="<?=$settings["smtp_pass"]?>">
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">SMTP Server</label>
            <input type="text" class="form-control" name="smtp_server" value="<?=$settings["smtp_server"]?>">
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">SMTP Port</label>
            <input type="text" class="form-control" name="smtp_port" value="<?=$settings["smtp_port"]?>">
          </div>
          <div class="col-md-3 form-group">
            <label class="control-label">SMTP Protocol</label>
            <select class="form-control" name="smtp_protocol">
              <option value="0" <?= $settings["smtp_protocol"] == 0 ? "selected" : null; ?> >no</option>
              <option value="tls" <?= $settings["smtp_protocol"] == "tls" ? "selected" : null; ?>>TLS</option>
              <option value="ssl" <?= $settings["smtp_protocol"] == "ssl" ? "selected" : null; ?>>SSL</option>
            </select>
          </div> 
        </div>
   
        <hr>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>

<script type="text/javascript">
// göster/gizle
function showMe(blockId) {
     if ( document.getElementById(blockId).style.display == 'none' ) {
          document.getElementById(blockId).style.display = ''; }
else if ( document.getElementById(blockId).style.display == '' ) {
          document.getElementById(blockId).style.display = 'none'; }
}
</script>