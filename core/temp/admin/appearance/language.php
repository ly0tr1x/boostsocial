<?php if( !route(3) ): ?>
<div class="col-md-8">
  <div class="settings-header__table">
    <a href="<?php echo site_url("admin/appearance/language/new") ?>"  class="btn btn-default m-b">Add New Language</a>
  </div>
   <table class="table report-table" style="border:1px solid #ddd">
      <thead>
         <tr>
                <th><div style="float:left;">Language Name</div></th>
            <th>Status</th>
            <th></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach($languageList as $language): ?>
         <tr class="<?php if( $language["language_type"] == 1 ): echo 'grey'; endif; ?>">
            <td><div style="float:left;"> <?php echo $language["language_name"]; if( $language["default_language"] == 1 ): echo ' <span class="badge">Active</span>'; endif; ?></div> </td>
            
       <?php if( $language["language_type"] == 2 ): ?> 
       <td>
             <div class="tt" style="font-size: 1.5em;">    <a href="<?php echo site_url('admin/appearance/language/?lang-id='.$language["language_code"].'&lang-type=1') ?>"><span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);"><i class="tt-switch-on"></i></span></a></div></td>
             <?php endif; ?>
    
                 
               <?php if( $language["language_type"] == 1 ): ?>
                  <td><div class="tt" style="font-size: 1.5em;">   <a href="<?php echo site_url('admin/appearance/language/?lang-id='.$language["language_code"].'&lang-type=2') ?>"><span class="tt-icon tt-switch-color" style="color: rgb(153, 153, 153);"><i class="tt-switch-off"></i></span></a></div></td>
                  <?php endif; ?>
 
            <td class="text-right col-md-1">
              <div class="dropdown pull-right">
             
                <a class="btn btn-default btn-xs pull-right" href="<?php echo site_url('admin/appearance/language/'.$language["language_code"]) ?>">
                Edit                            </a>
              </div>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</div>
<?php elseif( route(3) == "new" ): ?>
<div class="col-md-12">
   <div class="panel panel-default">

        <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
         <form action="<?php echo site_url('admin/appearance/language/new') ?>" method="post" enctype="multipart/form-data">
		 <div class="language-editor__container" style="max-height: 680px;">
           <div class="form-group">
              <label class="control-label">Dil Adı</label>
              <input type="text" class="form-control" name="language">
           </div>
           <div class="form-group">
              <label class="control-label">Dil Kodu</label>
              <select class="form-control" name="languagecode">
                 <option value="ar">ar (Arabic)</option>
                 <option value="af">af (Afrikaans)</option>
                 <option value="am">am (Amharic)</option>
                 <option value="sq">sq (Albanian)</option>
                 <option value="hy">hy (Armenian)</option>
                 <option value="az">az (Azerbaijani)</option>
                 <option value="eu">eu (Basque)</option>
                 <option value="bn">bn (Bengali)</option>
                 <option value="bg">bg (Bulgarian)</option>
                 <option value="ca">ca (Catalan)</option>
                 <option value="zh-HK">zh-HK (Chinese Hong Kong)</option>
                 <option value="zh-CN">zh-CN (Chinese Simplified)</option>
                 <option value="zh-TW">zh-TW (Chinese Traditional)</option>
                 <option value="hr">hr (Croatian)</option>
                 <option value="cs">cs (Czech)</option>
                 <option value="da">da (Danish)</option>
                 <option value="nl">nl (Dutch)</option>
                 <option value="en-GB">en-GB (English UK)</option>
                 <option value="en">en (English US)</option>
                 <option value="et">et (Estonian)</option>
                 <option value="fil">fil (Filipino)</option>
                 <option value="fi">fi (Finnish)</option>
                 <option value="fr">fr (French)</option>
                 <option value="fr-CA">fr-CA (French Canadian)</option>
                 <option value="gl">gl (Galician)</option>
                 <option value="ka">ka (Georgian)</option>
                 <option value="de">de (German)</option>
                 <option value="de-AT">de-AT (German Austria)</option>
                 <option value="de-CH">de-CH (German Switzerland)</option>
                 <option value="el">el (Greek)</option>
                 <option value="gu">gu (Gujarati)</option>
                 <option value="iw">iw (Hebrew)</option>
                 <option value="hi">hi (Hindi)</option>
                 <option value="hu">hu (Hungarain)</option>
                 <option value="is">is (Icelandic)</option>
                 <option value="id">id (Indonesian)</option>
                 <option value="it">it (Italian)</option>
                 <option value="ja">ja (Japanese)</option>
                 <option value="kn">kn (Kannada)</option>
                 <option value="ko">ko (Korean)</option>
                 <option value="lo">lo (Laothian)</option>
                 <option value="lv">lv (Latvian)</option>
                 <option value="lt">lt (Lithuanian)</option>
                 <option value="ms">ms (Malay)</option>
                 <option value="ml">ml (Malayalam)</option>
                 <option value="mr">mr (Marathi)</option>
                 <option value="mn">mn (Mongolian)</option>
                 <option value="no">no (Norwegian)</option>
                 <option value="fa">fa (Persian)</option>
                 <option value="pl">pl (Polish)</option>
                 <option value="pt">pt (Portuguese)</option>
                 <option value="pt-BR">pt-BR (Portuguese Brazil)</option>
                 <option value="pt-PT">pt-PT (Portuguese Portugal)</option>
                 <option value="ro">ro (Romanian)</option>
                 <option value="ru">ru (Russian)</option>
                 <option value="sr">sr (Serbian)</option>
                 <option value="si">si (Sinhalese)</option>
                 <option value="sk">sk (Slovak)</option>
                 <option value="sl">sl (Slovenian)</option>
                 <option value="es">es (Spanish)</option>
                 <option value="es-419">es-419 (Spanish Latin America)</option>
                 <option value="sw">sw (Swahili)</option>
                 <option value="sv">sv (Swedish)</option>
                 <option value="ta">ta (Tamil)</option>
                 <option value="te">te (Telugu)</option>
                 <option value="th">th (Thai)</option>
                 <option value="tr">tr (Turkish)</option>
                 <option value="uk">uk (Ukrainian)</option>
                 <option value="ur">ur (Urdu)</option>
                 <option value="vi">vi (Vietnamese)</option>
                 <option value="zu">zu (Zulu)</option>
              </select>
           </div>
           <hr>
            <?php foreach( $languageArray as $key => $val ): ?>
              <div class="form-group">
                 <label class="control-label"><?php echo $key; ?></label>
                 <input type="text" class="form-control" name="Language[<?php echo $key; ?>]" value="<?php echo $val;?>">
              </div>
            <?php endforeach; ?> </div>
          <div class="language-editor__container-footer">
                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-6 text-right">
                                                                  
                                                           
                                    <button type="submit" id="edit-language-btn" class="btn btn-primary" name="save-button">Update</button>   
                                      <a href="/admin/appearance/language" class="btn btn-default">Back</a></div>
                            </div>
                    </div>
         </form>
		
      </div>
   </div>
</div>
<?php elseif( route(3) ): ?>
<div class="col-md-12">
   <div class="panel panel-default">
       <div class="language-editor__header">
                    <div class="row"><div class="language-editor__header-title">
                        <div class="col-md-6">
                            
                         <strong><?php echo $language["language_name"] ?></strong>
										<?php if( $language["default_language"] == 1 ): echo ' <span class="badge">Aktif</span>'; endif; ?>
										   <?php if( $language["default_language"] == 0 ): ?>

                      <a class="btn btn-xs btn-default" href="<?php echo site_url('admin/appearance/language/?lang-id='.$language["language_code"].'&lang-default=1') ?>" id="default-language">
                      Set as Default                                    </a>
                    
                  <?php endif; ?>
                     </div>
           
         
         <div class="col-md-6 text-right">
                                <div class="pull-right">
  <input class="form-control" placeholder="Kelime ara..."  id="myInput" onkeyup="myFunction()" type="text" value="">                                </div>
                            </div>

                              </div>                              
                       
                    </div>
                </div>
		 <div class="language-editor__container" style="max-height: 680px;">
         <form action="<?php echo site_url('admin/appearance/language/'.route(3)) ?>" method="post" enctype="multipart/form-data">
           <div class="form-group">
              <label class="control-label">Language Name</label>
              <input type="text" class="form-control" name="language" value="<?php echo $language["language_name"] ?>">
           </div>
           <hr>
         <div id="myUL">
            <?php foreach( $languageArray as $key => $val ): ?>
            <eg><a>  <div class="form-group">
                 <label class="control-label"><?php echo $key; ?></label>
                 <input type="text" class="form-control" name="Language[<?php echo $key; ?>]" value="<?php echo $val;?>">
              </div></a></eg>
            <?php endforeach; ?>
            </div>
			</div>
                   <div class="language-editor__container-footer">
                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-6 text-right">
                                                                  
                                                           
                                    <button type="submit" id="edit-language-btn" class="btn btn-primary" name="save-button">Update</button>
                                      <a href="/admin/appearance/language" class="btn btn-default">Back</a></div>
                            </div>
                    </div>
         </form>
      </div>
   </div>
</div>
<script>
function myFunction() {
  // Declare variables
  var input, filter, ul, eg, a, i, txtValue;
  input = document.getElementById('myInput');
  filter = input.value.toUpperCase();
  ul = document.getElementById("myUL");
  eg = ul.getElementsByTagName('eg');

  // Loop through all egst items, and hide those who don't match the search query
  for (i = 0; i < eg.length; i++) {
    a = eg[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      eg[i].style.display = "";
    } else {
      eg[i].style.display = "none";
    }
  }
}
</script>
<?php endif; ?>
