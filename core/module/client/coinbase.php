<?php
require_once __DIR__ . "/library/coinbase_api/autoload.php";

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;

/**
 * Init ApiClient with your Api Key
 * Your Api Keys are available in the Coinbase Commerce Dashboard.
 * Make sure you don't store your API Key in your source code!
 */
function create($data){
ApiClient::init($data->key);

$chargeObj = new Charge(
		    [
		        "description" => $data->description,
		        "metadata" => [
		            "customer_id"   => $data->uid,
		            "customer_name" => $data->email
		        ],
		        'local_price' => [
			        'amount' => $data->amount,
			        'currency' => $data->currency
			    ],
		        "name" => $data->name,
		        "payments" => [],
		   "redirect_url" => $data->redirect_url,
		   "cancel_url" => $data->cancel,
		        "pricing_type" => "fixed_price"
		    ]
		);

		try {
		    $chargeObj->save();
		    $redirect_url = $chargeObj->hosted_url;
		    $result = (object)array(
		    	'status'       => 'success',
		    	'redirect_url' => $redirect_url,
		    	'txn_id'       => $chargeObj->id,
		    );
		} catch (\Exception $exception) {
		    $result = (object)array(
		    	'status'  => 'error',
		    	'message' => $exception->getMessage(),
		    );
		}
		return $result;
	}
function get_transaction_detail_info($transaction_id){

	 	try {
	        $response = Charge::retrieve($transaction_id);
	        $result = array(
				'status' => 'success',	
				'data'   => $response,	
			);

	    } catch (\Exception $exception) {
	        $result = array(
				'status' => 'error',	
				'data'   => $exception->getMessage(),	
			);
	    }
		return (object)$result;

	} 
