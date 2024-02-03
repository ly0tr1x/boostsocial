<?php

/*
  This page can be called twice. It receive the details of an initiated payment
  to the merchant directly before the payment. It alse receive the details of a
  settled payment to the merchant.
*/

  require_once '../webmoney.inc.php';

  /*
    Handling prerequest form data. If the "parameters transmission" flag is
    enabled, the payment processing will continue if the merchant's website
    returns "Yes" (string). If the merchant's website returns anything else,
    the payment will not be completed and the customer will receive an error
    message.
  */
  $wm_prerequest = new WM_Prerequest();
  if ($wm_prerequest->GetForm() == WM_RES_OK)
  {
    if (
      $wm_prerequest->payment_no == 1234 &&
      $wm_prerequest->payee_purse == 'Z123456789012' &&
      $wm_prerequest->payment_amount == 12.08 &&
      $wm_prerequest->extra_fields['FIELD1'] == 'VALUE 1'
    )
    {
      echo 'YES';
    }
    else
    {
      echo 'NO';
    }
    exit();
  }


  /*
    Handling payment notification data.
  */
  $wm_notif = new WM_Notification();
  if ($wm_notif->GetForm() != WM_RES_NOPARAM)
  {
    if ($wm_notif->CheckMD5('Z123456789012', 12.08, 1234, 'SALT_XXX') == WM_RES_OK)
    {
      /*
        Successful payment. Here you can update the database or send an email
        regarding $wm_notif parameters...
      */
    }
    else
    {
      /*
        Unsuccessful payment. Here you can update the database or send an email
        regarding $wm_notif parameters...
      */
    }
  }

?>

<!--
Prequest form test...
<form method="POST" action="result.php">
<input type="hidden" name="LMI_PREREQUEST" value="1" />
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="12.08" />
<input type="hidden" name="LMI_PAYMENT_NO" value="1234" />
<input type="hidden" name="LMI_PAYEE_PURSE" value="Z123456789012" />
<input type="hidden" name="LMI_MODE" value="1" />
<input type="hidden" name="LMI_PAYER_WM" value="809399319852" />
<input type="hidden" name="FIELD1" value="VALUE 1" />
<input type="hidden" name="FIELD2" value="VALUE 2" />
<input type="submit" />
</form>
-->

<!--
Notification form test...
<form method="POST" action="result.php">
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="12.08" />
<input type="hidden" name="LMI_PAYMENT_NO" value="1234" />
<input type="hidden" name="LMI_PAYEE_PURSE" value="Z123456789012" />
<input type="hidden" name="LMI_MODE" value="1" />
<input type="hidden" name="LMI_SYS_INVS_NO" value="281" />
<input type="hidden" name="LMI_SYS_TRANS_NO" value="558" />
<input type="hidden" name="LMI_PAYER_PURSE" value="Z397656178472" />
<input type="hidden" name="LMI_PAYER_WM" value="809399319852" />
<input type="hidden" name="LMI_SYS_TRANS_DATE" value="20020314 14:01:14" />
<input type="hidden" name="LMI_HASH" value="<?php
  echo strtoupper(md5('Z123456789012' . '12.08' . '1234' . '1' . '281' . '558' . '20020314 14:01:14' . 'SALT_XXX' . 'Z397656178472' . '809399319852')); ?>" />
<input type="hidden" name="FIELD1" value="VALUE 1" />
<input type="hidden" name="FIELD2" value="VALUE 2" />
<input type="submit" />
</form>
-->