Before starting to work with this class you need:

1) reqister a Webmoney account at http://www.webmoney.ru
or http://www.wmtransfer.com;
2) set up a number of parameters at https://merchant.webmoney.ru
or https://merchant.wmtransfer.com regulating the receipt of payments and
notification about payments.

For detailed information about Webmoney merchant system, please reffer the
Detailed Guide at https://merchant.wmtransfer.com/conf/guide.asp webpage.

class WM_Request
  var $payee_purse = '';
  var $payment_amount = 0.0;
  var $payment_no = -1;
  var $payment_desc = '';
  var $sim_mode = -1;
  var $result_url = '';
  var $success_url = '';
  var $success_method = -1;
  var $fail_url = '';
  var $fail_method = -1;
  var $payment_creditdays = -1;
  var $extra_fields = array();
  var $action = 'https://merchant.wmtransfer.com/lmi/payment.asp';
  var $btn_label = 'Pay Webmoney';
  function SetForm($output = true)

class WM_Prerequest
  var $payee_purse = '';
  var $payment_amount = '';
  var $payment_no = '';
  var $mode = '';
  var $payer_wm = '';
  var $paymer_number = '';
  var $paymer_email = '';
  var $telepat_phonenumber = '';
  var $telepat_orderid = '';
  var $payment_creditdays = '';
  var $extra_fields = array();
  function GetForm()

class WM_Notification
  var $payee_purse = '';
  var $payment_amount = '';
  var $payment_no = '';
  var $mode = '';
  var $sys_invs_no = '';
  var $sys_trans_no = '';
  var $payer_purse = '';
  var $payer_wm = '';
  var $paymer_number = '';
  var $paymer_email = '';
  var $telepat_phonenumber = '';
  var $telepat_orderid = '';
  var $payment_creditdays = '';
  var $hash = '';
  var $sys_trans_date = '';
  var $secret_key = '';
  function GetForm()
  function CheckMD5($payee_purse, $payment_amount, $payment_no, $secret_key)

class WM_Result
  var $payment_no = '';
  var $sys_invs_no = '';
  var $sys_trans_no = '';
  var $sys_trans_date = '';
  var $method = WM_POST;
  function GetForm()



