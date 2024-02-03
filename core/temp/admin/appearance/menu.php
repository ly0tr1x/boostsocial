<div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row settings-menu__row">
                        <div class="col-md-3">
                            <div class="settings-menu__title">Open to everyone</div>
                            <div class="settings-menu__description">Members who are not logged in</div>
                        </div>
                        <div class="col-md-9">
                            <div class="dd">
                                <ol class="dd-list" id="public_menu">
                                    
                                    <?php foreach( $public as $module ){ ?>

                                                                            <li class="dd-item">
                                                                               
                                            <div  class="dd-handle">
                                                    
    <div <?php   if($module["status"] == 1){ 
                                      $type = 'style="color:grey;"'; 
                                      echo $type;
                                  } ?>> <?=$module["name"]?> </div>                                                                                          
    </div>
                                            <div class="settings-menu__action">
                                                
                                              
                        <?php if($module["status"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                        <a href="/admin/appearance/menu/public_false/<?=$module["id"]?>"><span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/appearance/menu/public_true/<?=$module["id"]?>">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                                                
 
                                                                </div>
                                        </li>
<?php } ?>                                                                     
                                                                    </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row settings-menu__row">
                        <div class="col-md-3">
                            <div class="settings-menu__title">Members Only</div>
                            <div class="settings-menu__description">Users logged in</div>
                        </div>
                        <div class="col-md-9">
                            <div class="dd" >
                                <ol class="dd-list" id="signed_menu">
                                    
                                                                     <?php foreach( $public as $module ){ ?>

                                                                            <li class="dd-item" >
                                            <div class="dd-handle">
                                   <div <?php   if($module["public"] == 1){ 
                                      $type = 'style="color:grey;"'; 
                                      echo $type;
                                  } ?>> <?=$module["name"]?> </div>                                                                                          
    </div>
                                            <div class="settings-menu__action">
                                                
                                              
                        <?php if($module["public"] == 2): ?>
                  <div class="tt" style="font-size: 1.5em;">  
                        <a href="/admin/appearance/menu/nopublic_false/<?=$module["id"]?>"><span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>
</div>
                        <?php else: ?>
                         <div class="tt" style="font-size: 1.5em;">  

 <a href="/admin/appearance/menu/nopublic_true/<?=$module["id"]?>">

<span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span>

</a> </div>   
                        <?php endif; ?>
                                         </li>
                                         <?php } ?>                                                                     

                                                                    </ol>
                            </div>
                       
                        </div>
                    </div>

                </div>
            </div>
        </div>
   