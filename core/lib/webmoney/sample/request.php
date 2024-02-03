<?php

/*
  The form transmits the request from the merchant's site to Merchant WebMoney
  Transfer through the customer's browser
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Webmoney Request Form</title>

<style type="text/css">
<!--
#wmbtn {
  border-width: 2px;
  border-color: maroon;
  border-style: solid;
  background-color: #cccccc;
  color: maroon;
  font-weight: bold;
}
//-->
</style>

</head>

<body>

<?php

  /*
    Include the webmoney library.
  */
  require_once '../webmoney.inc.php';

  /*
    Create a new WM_Request object.
  */
  $wm_request = new WM_Request();

  /*
    The amount the merchant wants to receive from the customer. The amount must
    be more than zero; decimal separator is used.
  */
  $wm_request->payment_amount = 12.08;

  /*
    Description of products or services. It must be added to the WebMoney transfer
    payment form as well. Maximum 255 characters in length.
  */
  $wm_request->payment_desc = 'payment under the bill';

  /*
    The merchant specifies the number of the purchase in accordance with his
    accounting system. The parameter is not required, but we advise you to always
    use it. You should use a unique number for each payment so that you can easily
    find all the information about it. Number must be an integer, greater than
    2147483647.
  */
  $wm_request->payment_no = 1234;

  /*
    The merchant's purse to which the customer has to pay. Format is a letter and
    twelve digits. Presently, Z, R, E and D purses are used in the service.
  */
  $wm_request->payee_purse = 'Z123456789012';

  /*
    The field is used only in the test mode. It may have one of the following
    values:
    0 or WM_ALL_SUCCESS: All test payments will be successful;
    1 or WM_ALL_FAIL: All test payments will fail;
    2 or WM_SUCCESS_FAIL: 80% of test payments will be successful, 20% of test
    payments will fail.
  */
  $wm_request->sim_mode = WM_ALL_SUCCESS;

  /*
    URL (on the merchant's website) to which Merchant WebMoney Transfer will send
    an HTTP POST or SMTP notification about the payment and its details. This
    field lets the merchant temporarily replace the Result URL specified on a
    special web page of the Merchant WebMoney Transfer site. If the 'Allow URLs
    transmitted in the form' option is enabled, the transmitted URL will replace
    the Result URL specified on the site of Merchant WebMoney Transfer. URL must
    begin with "http://", "https://" or "mailto:". If the URL begins with mailto:,
    the notification will be sent to the provided email. The notification will be
    sent through ports 80 and 443 if you are using the URL beginning with "http://"
    or "https://".
  */
  $wm_request->result_url = 'http://www.myshop.com/result.php';

  /*
    URL (on the merchant's website) to which the customer's browser will be
    redirected in case of a successful payment through Merchant WebMoney Transfer.
    This field lets the merchant temporarily replace the Success URL specified
    on a special web page of the Merchant WebMoney Transfer site. If the
    'Allow URLs transmitted in the form' option is enabled, the transmitted URL
    will replace the Success URL specified on the site of Merchant WebMoney
    Transfer. Otherwise, the URL specified at the site of Merchant WebMoney
    Transfer will be used. URL must begin with "http://" or "https://".
  */
  $wm_request->success_url = 'http://www.myshop.com/success.php';

  /*
    This field lets the merchant temporarily replace the parameter
    'Method of requesting the Success URL' that he had set up at the site of
    Merchant WebMoney Transfer (Settings page). If the 'Allow URLs transmitted
    in the form' option is enabled, the URL in the form will replace the parameter
    'Method of requesting Success URL' specified on the website of Merchant
    WebMoney Transfer. The field may have values WM_GET, WM_POST or WM_LINK equal
    to values of the 'Method of requesting Success URL' - 'GET', 'POST' or 'LINK'.
  */
  $wm_request->success_method = WM_POST;

  /*
    URL (on the merchant's website) to which the customer's browser will be
    redirected if the payment failed. This field lets the merchant temporarily
    replace the parameter 'Fail URL ' he had set up at the site of Merchant
    WebMoney Transfer (Settings page). If the 'Allow URLs transmitted in the form'
    option is enabled, the URL in the form will replace the parameter 'Fail URL'
    specified on the website of Merchant WebMoney Transfer. Otherwise, the URL
    specified at the website will be used. URL must begin with http:// or "https://".
  */
  $wm_request->fail_url = 'http://www.myshop.com/fail.php';

  /*
    This field lets the merchant temporarily replace the parameter 'Method of
    requesting Fail URL' specified at the website of Merchant WebMoney Transfer.
    If the 'Allow URLs transmitted in the form' option is enabled, the URL in the
    form will replace the parameter 'Method of requesting Fail URL' specified on
    the website of Merchant WebMoney Transfer. Otherwise, the URL specified at
    the website will be used. The field may have the values WM_GET, WM_POST or
    WM_LINK equal to values of the 'Method of requesting Fail URL' - 'GET', 'POST'
    or 'LINK'.
  */
  $wm_request->fail_method = WM_POST;

  /*
    Merchant WebMoney Transfer processes the fields without the 'LMI_' prefix
    automatically and transmits the data specified in them to the merchant's
    website after the payment is made.
  */
  $wm_request->extra_fields = array('FIELD1'=>'VALUE 1', 'FIELD2'=>'VALUE 2');

  /*
    URL which process the request. https://merchant.wmtransfer.com/lmi/payment.asp
    or https://merchant.webmoney.ru/lmi/payment.asp
  */
  $wm_action = 'https://merchant.wmtransfer.com/lmi/payment.asp';

  /*
    The button label.
  */
  $wm_btn_label = 'Pay Webmoney';

  /*
    Show the button that submits the request.
  */
  $wm_request->SetForm();

?>

</body>

</html>