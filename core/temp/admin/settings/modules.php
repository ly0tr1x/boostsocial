<div class="col-md-8">
        <?php if($active_modules){ ?>
            <div class="settings-emails__block">
    <div class="settings-emails__block-title">
        Active    </div>
    <div class="settings-emails__block-body">
        <table>
            <thead>
            <tr>
                <th class="settings-emails__th-name"></th>
                <th class="settings-emails__th-actions"></th>
            </tr>
            </thead>
            <tbody>
                                    
<?php foreach( $active_modules as $module ){ ?>
                            <tr class="settings-emails__row">
                    <td>
                        <div class="settings-emails__row-name"><?=$module["name"]?></div>
                        <div class="settings-emails__row-description">
                           <?=$module["description"]?></div>
                    </td>                                             

                        <td class="settings-emails__td-actions">  
                           <div class="tt" style="font-size: 1.5em;">   <?php if($module["id"] != 4): ?>
                        <a href="/admin/settings/modules/disable/<?=$module["id"]?>"><span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a>                    <?php endif; ?>

                        </div>
                        
                        
                        </td>
                    <td class="settings-emails__td-actions">
                        <?php if($module["id"] == 4): ?>
                            <a href="/admin/settings/subject" class="btn btn-default btn-xs pull-right">Edit</a>
                        
                        <?php else: ?>
                                                          <button type="button" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#modalDiv" data-action="<?=$module["ajax_name"]?>">Edit</button>
                                    <?php endif; ?>
                                            </td>
                </tr>
                
                <?php } ?>
                            
                        </tbody>
        </table>
    </div>
</div>
<?php } ?>
<?php if($passive_modules){ ?>
    <div class="settings-emails__block">
        <div class="settings-emails__block-title">
        Other        </div>
        <div class="settings-emails__block-body">
            <table>
                <thead>
                <tr>
                    <th class="settings-emails__th-name"></th>
                    <th class="settings-emails__th-actions"></th>
                </tr>
                </thead>
                <tbody>
                    
                    
<?php foreach( $passive_modules as $module_1 ){ ?>

 <tr class="settings-emails__row settings-emails__row-disable">
                        <td>
                            <div class="settings-emails__row-name"><?=$module_1["name"]?></div>
                            <div class="settings-emails__row-description">
                             <?=$module_1["description"]?></div>
                        </td>
                 
                        <td class="settings-emails__td-actions">
                         
                        <a href="/admin/settings/modules/enable/<?=$module_1["id"]?>">    <button type="button" class="btn btn-default btn-xs pull-right" >Activate</button> </a>
                        </td>
                    </tr>
                    
<?php } ?>
                                   
                                    
                                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</div>