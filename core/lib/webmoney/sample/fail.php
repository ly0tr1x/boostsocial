<?php
  require_once '../webmoney.inc.php';
  $wm_result = new WM_Result();
  $wm_result->method = WM_POST;
  if ($wm_result->GetForm() == WM_RES_OK)
  {
    echo '<b>Transaction failed!</b><br /><br />';
    echo 'Purchase number: ' . $wm_result->payment_no . '<br />';
    echo 'Bill number: ' . $wm_result->sys_invs_no . '<br />';
    echo 'Payment number: ' .  $wm_result->sys_trans_no . '<br />';
    echo 'Date of payment: ' .  $wm_result->sys_trans_date . '<br />';
    foreach ($wm_result->extra_fields as $field=>$value)
    {
      echo $field . ': ' . $value . '<br />';
    }
  }
?>

<!--
Just for test....
<form method="post" action="fail.php">
<input type="hidden" name="LMI_PAYMENT_NO" value="1234" />
<input type="hidden" name="LMI_SYS_INVS_NO" value="281" />
<input type="hidden" name="LMI_SYS_TRANS_NO" value="558" />
<input type="hidden" name="LMI_SYS_TRANS_DATE" value="20020314 14:01:14" />
<input type="hidden" name="FIELD1" value="VALUE 1" />
<input type="hidden" name="FIELD2" value="VALUE 2" />
<input type="submit" />
</form>
-->