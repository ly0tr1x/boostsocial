<div class="container">
  <div class="row">

<?php if( !route(3) ): ?>
<div class="col-md-2 col-md-offset-1">
            <ul class="nav nav-pills nav-stacked p-b">
                              <li class="settings_menus "><a href="<?=site_url("admin/appearance/pages")?>">Pages</a></li>
                              <li class="settings_menus "><a href="<?=site_url("admin/appearance/news")?>">Announcements</a></li>
                              <li class="settings_menus "><a href="<?=site_url("admin/appearance/blog")?>">Blog</a></li>
                              <li class="settings_menus "><a href="<?=site_url("admin/appearance/menu")?>">Menu</a></li>
                              <li class="settings_menus active"><a href="<?=site_url("admin/appearance/themes")?>">Themes</a></li>
                              <li class="settings_menus "><a href="<?=site_url("admin/appearance/language")?>">Languages</a></li>
                               <li class="settings_menus "><a href="<?=site_url("admin/appearance/files")?>">Folders</a></li>
                          </ul>
          </div>
<div class="container">
                <div class="row">
				<div class="col-lg-8">
                   <div class="settings-themes">

         <?php foreach($themes as $theme):
         
            $x = $theme['theme_dirname'];
            $yol = site_url("select-theme/$x");
            
         ?>
              <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="settings-themes__card settings-themes__card-active">
                                
                                <?php if( $settings["site_theme"] != $theme["theme_dirname"] ): ?>
                                <div class="settings-themes__card-preview" style="background-image: url(https://image.thum.io/get/<?=$yol?>)">
                                <?php endif;  
                                    
                                    if($_SESSION['theme']):
                                        
                                        $eye = ' <i class="fa fa-eye"></i>'; 
                                        
                                        endif;
                                    
                                    if( $settings["site_theme"] == $theme["theme_dirname"] ): echo '<div class="settings-themes__card-preview" style="background-image: url(https://image.thum.io/get/'.$yol.')"><span class="badge">Active'.$eye.'</span>'; endif; ?>  
                                    <?php if( $settings["site_theme"] != $theme["theme_dirname"] ): ?>
                  <div class="settings-themes__card--activate">
                                            <a class="btn btn-primary" href="<?php echo site_url('admin/appearance/themes/active/'.$theme["theme_dirname"]) ?>">Activate</a>                                        </div>
                  <?php endif; ?>
                                                                            
                                                   
                                                                    </div>
                                <div class="settings-themes__card-title">
                                    <?php echo $theme["theme_name"]; ?>                                    
                                    <a href="<?php echo site_url('select-theme/'.$theme["theme_dirname"]) ?>" class="btn btn-default btn-xs" target="_blank"><i class="fa fa-eye"></i></a>
                                    <a href="<?php echo site_url('admin/appearance/themes/'.$theme["theme_dirname"]) ?>" class="btn btn-default btn-xs pull-right">Edit</a>
                                </div>
                                
                            </div>
                        </div>
         <?php endforeach; ?>
 </div></div>     
<?php elseif( route(3) ): ?>
  <div class="col-md-12">
    <div class="panel">
      <div class="panel-heading edit-theme-title"><strong><?php echo $theme["theme_name"] ?></strong> being held</div>

        <div class="row">
          <div class="col-md-3 padding-md-right-null">

            <div class="panel-body edit-theme-body">
              <div class="twig-editor-block">
                <?php
                  $layouts  = [
                    "HTML"=>["header.twig","footer.twig","account.twig","addfunds.twig","api.twig",
                    "login.twig","signup.twig","neworder.twig","orders.twig","dripfeeds.twig","subscriptions.twig",
                    "services.twig","child-panels.twig","tickets.twig","viewticket.twig","blog.twig","blogpost.twig","verify.twig","affiliates.twig", 
                    "resetpassword.twig",
                    "terms.twig","faq.twig","404.twig"],
                    "CSS"=>["bootstrap.css","style.css"],
                    "JS"=>["bootstrap.js","script.js"]
                  ];
                foreach ($layouts as $style => $layout):
                  echo '<div class="twig-editor-list-title" data-toggle="collapse" href="#folder_'.$style.'"><span class="fa fa-folder-open"></span>'.$style.'</div><ul class="twig-editor-list collapse in" id="folder_'.$style.'">';
                  foreach ($layouts[$style] as $layout) :
                    if( $lyt == $layout ):
                      $active = ' class="active file-modified" ';
                    else:
                      $active = '';
                    endif;
                    echo '
                      <li '. $active .'><a href="'.site_url('admin/appearance/themes/'.$theme["theme_dirname"]).'?file='.$layout.'">'.$layout.'</a></li>';
                  endforeach;
                  echo '</ul>';
                endforeach;
              ?>
              </div>

            </div>
          </div>
          <div class="col-md-9 padding-md-left-null edit-theme__block-editor">
            <?php if( !$lyt ): ?>
              <div class="panel-body">
                <div class="row">
                   <div class="col-md-12">
                    <div class="theme-edit-block">
                      <div class="alert alert-info" role="alert">
                      Document will be added soon...<br>
                      You can access the theme codes of the page you want to edit from the left side..
                      </div>
                    </div>
                  </div>
                  </div>
              </div>
            <?php else: ?>
                  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/codemirror.min.js"></script>
                  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.56.0/mode/xml/xml.min.js"></script>
                  
                  <div id="fullscreen">

               <div class="panel-body">

                <?php
                $file = fopen($fn, "r");
                $size = filesize($fn);
                $text = fread($file, $size);
                $text = str_replace("<","&lt;",$text);
                $text = str_replace(">","&gt;",$text);
                $text = str_replace('"',"&quot;",$text);
                fclose($file);
                ?>

                <div class="row">
                    <div class="col-md-8">
                      <strong class="edit-theme-filename"><?=$dir."/".$lyt?></strong>
                        </div>
                        <div class="col-md-4 text-right">
                                    <a class="btn btn-xs btn-default fullScreenButton">
                                        <span class="glyphicon glyphicon-fullscreen"></span>
                                        Edit Full Screen </a>
                                </div>
                  </div>
           

                <form action="<?php echo site_url("admin/appearance/themes/".$theme["theme_dirname"]."?file=".$lyt) ?>" method="post" class="twig-editor__form">
                    
                  <textarea id="code" name="code" class="codemirror-textarea"><?=$text;?></textarea>
                  <div class="edit-theme-body-buttons text-right">
                    <button class="btn btn-primary click">Update</button>
                  </div>
                </form>

              </div>
            <?php endif; ?>
          </div>
        </div>

    </div>
  </div>


<?php endif; ?>

