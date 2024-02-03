<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <div class="col-md-8">
	<div class="settings-header__table">
		<button type="button" class="btn btn-default m-b" data-toggle="modal" data-target="#modalDiv" data-action="add_currency">Add Currency</button>
	</div>
	
	
	<div style="background-color:rgba(217, 4, 41,0.2);padding:7px;border-radius:4px;margin-top:10px;"><p style="color:#D90429;">
Note : If you add any currency whose rate is unknown to you, you can enter '1' as the rate and then click on the 'Auto Currency Rate' button to fetch the current rates. This step is mandatory. Be sure to click on the 'Auto Rate Updater' to update the service conversation rate.</p></div>

<hr>
	
	
	<div class="col-md-12">
   <table class="table report-table" style="border:1px solid #ddd">
      <thead>
         <tr>
            <th>Currency Name</th>
                        <th>Auto Currency Rate Update</th>

            <th>Symbol</th>
   <th>Exchange Rate</th>
   <th></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach($currencies as $currencie): ?>
         <tr class="<?php if( $currencie["status"] == 2 ): echo "grey "; endif; ?>" data-toggle="<?php echo $currencie["id"]; ?>" data-id="<?php echo $currencie["id"]; ?>">
            <td> <?php echo $currencie["name"];  ?></td>
         <td>
  <div class="tt" style="font-size: 1.5em;">
    <?php if ($currencie["rate"] == 2) : ?>
      <span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);">
        <i class="tt-switch-on"></i>
      </span>
    <?php else : ?>
      <span class="tt-icon tt-switch-color" style="color: rgb(0, 102, 255);">
        <i class="tt-switch-off"></i>
      </span>
    <?php endif; ?>
  </div>
</td>
  
  <td> <?php echo $currencie["symbol"]; ?></td>
  <?php
  if ($currencie["rate"] == 2) :?>

  <td class="rate-<?=$currencie["name"]?>"><?php
 
    echo "loading...";

 ?> </td>
 
 <?php else: ?>
 
   <td><?php
 
    echo $currencie["value"];

 ?> </td>
 <?php endif;?>
             <td class="text-right col-md-1">
              <div class="dropdown pull-right">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Options <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li>
                <a  data-toggle="modal" data-target="#modalDiv" data-action="edit_currency" data-id="<?= $currencie["id"] ?>">Edit</a>
					
                  </li>

                    <li>
                      <a href="<?php echo site_url('admin/settings/currency/delete/'.$currencie["id"]) ?>">
                        Delete
                      </a>
                    </li>
</td>
                  
                </ul>
              </div>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</div>

 <script>
$(document).ready(function() {
  $(".tt-switch-color").click(function() {
    var switchState = $(this).find("i").hasClass("tt-switch-on");
    var rowId = $(this).closest("tr").data("id");

    if (switchState) {
      $(this).find("i").removeClass("tt-switch-on").addClass("tt-switch-off");
      $.ajax({
        url: "/admin/settings/rates",
        method: "POST",
        data: {id: rowId },
        success: function(response) {
          console.log("AJAX request success:", response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log("AJAX request error:", errorThrown);
        }
      });
    } else {
      $(this).find("i").removeClass("tt-switch-off").addClass("tt-switch-on");
      $.ajax({
        url: "/admin/settings/rate",
        method: "POST",
        data: {id: rowId },
        success: function(response) {
          console.log("AJAX request success:", response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log("AJAX request error:", errorThrown);
        }
      });
    }
  });
});
</script>
 
<script>

<?php if (!empty($currencies)) {
    $currency_codes = array();
    foreach ($currencies as $currency) {
        if($currency['rate']!==1){
            
            $currency_codes[] = $currency['name'];
        }
    }
    

    $currency_codes = array_unique($currency_codes);
    $currency_codes_str = "'" . implode("', '", $currency_codes) . "'";
    
        $currency_code = abcus("id", $settings["site_currency"], "name");
        $url = strtolower($currency_code);
?>
    var currency_codes = [<?php echo $currency_codes_str; ?>];
   function _0x4ba6(){var _0x288c8f=['1268224KIMLpZ','1671138yzjLaQ','<?=$currency_code?>','ajax','1060ZwGGyZ','209293JwXCHk','82951YeMIWE','toLowerCase','toFixed','Error\x20retrieving\x20exchange\x20rate:\x20','219BACmNC','4RCtkIL','length','text','1923915rZuKcm','GET','6FYAJmE','.rate-','json','2024305BuhISz','20112MbIeDt'];_0x4ba6=function(){return _0x288c8f;};return _0x4ba6();}var _0xa77083=_0x2a89;function _0x2a89(_0x237618,_0x1ecfd9){var _0x4ba650=_0x4ba6();return _0x2a89=function(_0x2a89eb,_0x4fb87d){_0x2a89eb=_0x2a89eb-0x161;var _0x3793aa=_0x4ba650[_0x2a89eb];return _0x3793aa;},_0x2a89(_0x237618,_0x1ecfd9);}(function(_0x1be35b,_0x5e74bb){var _0x1980c9=_0x2a89,_0x4970c9=_0x1be35b();while(!![]){try{var _0x1ee4b2=parseInt(_0x1980c9(0x161))/0x1*(parseInt(_0x1980c9(0x167))/0x2)+-parseInt(_0x1980c9(0x166))/0x3*(parseInt(_0x1980c9(0x170))/0x4)+-parseInt(_0x1980c9(0x16f))/0x5*(-parseInt(_0x1980c9(0x16c))/0x6)+parseInt(_0x1980c9(0x16a))/0x7+parseInt(_0x1980c9(0x171))/0x8+parseInt(_0x1980c9(0x172))/0x9+parseInt(_0x1980c9(0x175))/0xa*(-parseInt(_0x1980c9(0x162))/0xb);if(_0x1ee4b2===_0x5e74bb)break;else _0x4970c9['push'](_0x4970c9['shift']());}catch(_0x566843){_0x4970c9['push'](_0x4970c9['shift']());}}}(_0x4ba6,0x43690),$[_0xa77083(0x174)]({'url':'https://www.floatrates.com/daily/<?=$url;?>.json','type':_0xa77083(0x16b),'dataType':_0xa77083(0x16e),'success':function(_0x260dbd){var _0x28ec1e=_0xa77083;for(var _0x4af5c4=0x0;_0x4af5c4<currency_codes[_0x28ec1e(0x168)];_0x4af5c4++){var _0x1326e7=currency_codes[_0x4af5c4],_0x4314ab=currency_codes[_0x4af5c4][_0x28ec1e(0x163)]();_0x4314ab===_0x28ec1e(0x173)||!_0x260dbd[_0x4314ab]?$('.rate-'+_0x1326e7)['text']('1'):$(_0x28ec1e(0x16d)+_0x1326e7)[_0x28ec1e(0x169)](_0x260dbd[_0x4314ab]['rate'][_0x28ec1e(0x164)](0x3));}},'error':function(_0x5ac8fe,_0x4560a7,_0x191f39){var _0x8ef586=_0xa77083;for(var _0x12d957=0x0;_0x12d957<currency_codes[_0x8ef586(0x168)];_0x12d957++){var _0x42d1a8=currency_codes[_0x12d957],_0x17b8c2=currency_codes[_0x12d957]['toLowerCase']();$('.rate-'+_0x42d1a8)[_0x8ef586(0x169)](_0x8ef586(0x165)+_0x191f39);}}}));
<?php } ?>

</script>

 