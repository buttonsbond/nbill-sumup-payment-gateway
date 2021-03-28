<?php
/**
* This gateway was developed by and is copyright of All Tech Plus Rojales.
* Sections of code may be copyrighted to other parties (eg. where sample code was used
* from the SumUp documentation). All parts (of this gateway only) written by 
* All Tech Plus, Rojales are licensed for use in any way you wish, as long 
* as this copyright message remains intact, and without any guarantee of any sort - 
* use at your own risk.
*
* For processing of receipts - sumup should redirect to this script to send out confirmations etc...
*
*/

//Ensure this file has been reached through a valid entry point (not always necessary eg. for class files, but included on every file to be safe!)
(defined('_VALID_MOS') || defined('_JEXEC') || defined('NBILL_VALID_NBF')) or die('Access Denied.');

function get_sumup_token($fields) {
       // need to get an auth token first using the TOKEN_URL
       $clientid=$fields['client_id']['g_value'];
       $clientse=$fields['client_secret']['g_value'];
        $granttype="client_credentials";
        $authurl=$fields['token_url']['g_value'];    
 /*     echo "<pre>"; print_r($clientid); print_r($clientse); print_r($authurl); echo "</pre>";  */
        $tokenpayload=Array('client_id'=>$clientid,'client_secret'=>$clientse,'grant_type'=>$granttype);
        $authresponse=sumup_query($authurl, $tokenpayload, "=", "POST","");
        // do we have access_token?
        $dresponse=json_decode($authresponse);
/*         echo "<pre>"; print_r($dresponse); echo "</pre>";  */
        $accesstoken=$dresponse->access_token; // this worked to here    
    return $accesstoken;
}
/**
     * Calls to the sumup API
     * 
     * @param String $url
     * @param Array $params
     * @param String $delim
     * @param String $postorget
     * @param String $at
     * 
     * 
     * @return string - json response
     */
function sumup_query($url, $params, $delim, $postorget, $at) {
          // Setup cURL options and make call to API
          // $url = API url to use
          // $params = the parameters to pass, comes in as an array
          // $delim = one of : or =
          // $postorget = can be POST or GET (though only POST is used)
          // $at = either blank or the authorisation token from first call
          //
          // I borrowed this function from elsewhere and have adapted it
          // The first call to sumup is to get an authorization token
          // this sends data as a normal post where parameters are
          // delimited with the & sign.
          // The second call uses that a-t to get a resource id - the data
          // that is passed this time is json formatted so the delimiters
          // will be a : and the key and values with quotes ' around them and
          // curly braces.
          // For clarity I may rewrite and perhaps have 2 separate functions
          // to handle - maybe!
          //
          $curl = curl_init();
          $headr = array();
          
          if ($delim == ":") {
            // in this case we've already got our token and are creating
            // a checkout
            $headr[] = 'Authorization: Bearer '.$at;
            $headr[] = 'Content-type: application/json';
          } else {
              // assuming non json call to get the token so header will be
              // if it's this, we're getting the authentication token using
              // normal post vars
              $headr[] = 'Content-Type: application/x-www-form-urlencoded';
          }
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headr);
            $paramfields="";

              if (($delim == ":") && (is_array($params))) {
                  $paramfields = json_encode($params);
              } else {   
                  
                // only do this next bit if we're not sending json as I've already
                // encoded json to the variable $paramfields above if so
                $quoteit = "";
                $s="&";
                  if (is_array($params)) {
                       // Prepare the params list to be posted with curl
                       foreach($params as $key => $value) {
                           if ($key != "amount") {
                           $paramfields .= $quoteit . $key . $quoteit . $delim . $quoteit .$value. $quoteit . $s;
                           } else
                           {
                            $paramfields .= $quoteit . $key . $quoteit . $delim . $value . $s;
                           }
                       }
                       $paramfields = rtrim($paramfields, $s); // this removes the last comma

                } // end of normal posting parameter setup
                       
              }   
               
               curl_setopt($curl, CURLOPT_HEADER, 0); 
               
               $extra="";
               if ($postorget == "POST") {
                   curl_setopt($curl, CURLOPT_POST, count($params));
                   curl_setopt($curl, CURLOPT_POSTFIELDS, $paramfields);
               }
               if ($postorget == "GET") {
                   $extra="?" . $paramfields;
                   curl_setopt($curl,CURLOPT_HTTPGET, true);
               }
              curl_setopt($curl, CURLOPT_URL, $url . $extra);             
              // Configure cURL request options
              
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($curl, CURLOPT_TIMEOUT, 200);
              curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
              curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    
              // Run cURL and check for errors
              $result = curl_exec($curl);
              $jresult=$result;
              if (curl_errno($curl)) { $result = Array('success' => 'false', 'result' => curl_errno($curl).' - '.curl_error($curl)); }
              curl_close($curl);
 
      //    echo var_dump($headr) . " those were the headers<br/><br/>";
     //   echo   var_dump($paramfields) . " those were the paramfields<br/><br/>";
     //    echo var_dump($jresult) . " and that was the result</br></br>";
    //     echo var_dump($result) . " this is the raw result";
          return $result;
     }

// end mvb code



$nb_database = nbf_cms::$interop->database;
include_once(nbf_cms::$interop->nbill_admin_base_path . "/framework/classes/nbill.date.class.php");

if (file_exists(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup." . nbf_cms::$interop->language . ".php")) {
	include_once(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup." . nbf_cms::$interop->language . ".php");
} else {
	include_once(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup.en-GB.php");
}

$sql = "SELECT * FROM #__nbill_payment_gateway WHERE gateway_id = 'sumup'";
$nb_database->setQuery($sql);
$sumup_fields = $nb_database->loadAssocList('g_key');
if (!array_key_exists('auth_url', $sumup_fields)) {
	$sumup_fields = array();
	$alt_sumup_fields = $nb_database->loadObjectList();
	foreach ($alt_sumup_fields as $alt_sumup_field) {
		$sumup_fields[$alt_sumup_field->g_key] = array();
		$sumup_fields[$alt_sumup_field->g_key]['g_key'] = $alt_sumup_field->g_key;
		$sumup_fields[$alt_sumup_field->g_key]['g_value'] = $alt_sumup_field->g_value;
	}
}

$task=nbf_common::get_param($_REQUEST, 'task');
$warning_message=""; $error_message="";
$redirect_url=nbf_cms::$interop->live_site; // if the checkout isnt valid in the first place we will just get a homepage

switch ($task)
	{
	case "sumup_receipt":
		$posted_values=$_GET;
			// check that we have all the returned values we need, should be checkout_id because we will need that to check status of completed checkout with sumup
			// and g_tx_id which is the transaction id that nbill needs to finalise the payment
			$g_tx_id = nbf_common::get_param($posted_values, 'g_tx_id');
			$checkout_id = nbf_common::get_param($posted_values, 'checkout_id');
			if (empty($checkout_id)) {
				$error_message=NBILL_SUMUP_CHECKOUTID_MISSING;
			} 
			if ($error_message == "")
			{
				// assuming transaction was ok, let's check it - we need an access_token first
				$at=get_sumup_token($sumup_fields);
				// call the checkout_url with the checkout_id so can confirm
        		// the status of the transaction
       			$checkout=$checkout_id;
       			$checkout_url=$sumup_fields['checkout_url']['g_value']; 
        $nl="";
        $checktx=sumup_query($checkout_url . "/" . $checkout,$nl,":","GET",$at);
        
        $decode=json_decode($checktx);
        $txid=$decode->transaction_id;
        $txco=$decode->transaction_code;
        $status=$decode->status;
        $t=$decode->transactions;
        $status2=$t[0]->status; // this is whether the card payment actually worked
		
		// ok, status2 will be SUCCESSFUL and so will status if the payment has been processed, status2 is the main one, we'll switch on it
			switch ($status2) {
				case "SUCCESSFUL":
					$warning_message=NBILL_SUMUP_SUCCESS;
					// do our processing now	
				   // got this bit from stripe payment processing code
				   //Do the nBill processing
    					include_once(nbf_cms::$interop->nbill_admin_base_path . "/framework/classes/nbill.payment.class.php");   					
    					nbf_payment::gateway_processing($g_tx_id, $decode->amount, $decode->currency, $warning_message, $error_message, "", "Txn id: $g_tx_id-$txid");
    					$redirect_url = nbf_cms::$interop->live_site . "/" . $sumup_fields['thankyou_url']['g_value'];
					break;
				default:
					// if we get here, assuming not successful
					$error_message=NBILL_SUMUP_ERR_FAILURE;
					$redirect_url = nbf_cms::$interop->live_site . "/" . $sumup_fields['problem_url']['g_value'];
					break;
			}
		
		
		}
		
		break;	
		
		
	}
include_once(nbf_cms::$interop->nbill_admin_base_path . "/framework/classes/nbill.payment.class.php");
nbf_payment::finish_gateway_processing($warning_message, $error_message, @$decode, $redirect_url, $g_tx_id);

 //@apache_setenv('no-gzip', 1);
 //   @ini_set('zlib.output_compression', 0);
//    @ini_set('implicit_flush', 1);
 //   for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
 //   ob_implicit_flush(1);

?>