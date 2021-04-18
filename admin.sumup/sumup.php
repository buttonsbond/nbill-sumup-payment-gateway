<?php
/**
* This gateway was developed by and is copyright of All Tech Plus Rojales.
* Sections of code may be copyrighted to other parties (eg. where sample code was used
* from the SumUp documentation). All parts (of this gateway only) written by 
* All Tech Plus, Rojales are licensed for use in any way you wish, as long 
* as this copyright message remains intact, and without any guarantee of any sort - 
* use at your own risk.
*/

//Ensure this file has been reached through a valid entry point (not always necessary eg. for class files, but included on every file to be safe!)
(defined('_VALID_MOS') || defined('_JEXEC') || defined('NBILL_VALID_NBF')) or die('Access Denied.');

if (file_exists(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup." . nbf_cms::$interop->language . ".php"))
{
	include_once(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup." . nbf_cms::$interop->language . ".php");
}
else
{
	if (file_exists(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup.en-GB.php"))
	{
		include_once(nbf_cms::$interop->nbill_admin_base_path . "/admin.gateway/admin.sumup/sumup.en-GB.php");
	}
}

  $nb_database = nbf_cms::$interop->database;
  
  $total_net = $standard_totals->total_net;
  $total_tax = $standard_totals->total_tax;
  $total_carriage = $standard_totals->total_shipping;
  $total_carriage_tax = $standard_totals->total_shipping_tax;
  $total_gross = $standard_totals->total_gross;
  $regular_total_gross = $regular_totals->total_gross;
  
  $clientemail = $billing_data['email_address'];
  if ($clientemail=="") { $clientemail="No e-mail address has been specified for this client."; }
  
  
switch ($payment_frequency)
{
	case "AA":
		//Ok - one off payment (SumUp can support recurring but not implemented)
		break;
	default:
		echo NBILL_SUMUP_PAYMENT_FREQ_NOT_SUPPORTED;
			$abort = true;
			return;
}

$sql = "SELECT * FROM #__nbill_payment_gateway WHERE gateway_id = 'sumup'";
// coding here was based from worldpay plugin have changed var names throughout
$nb_database->setQuery($sql);
$sumup_fields = $nb_database->loadAssocList('g_key');
if (!array_key_exists('auth_url', $sumup_fields))
{
	//loadAssocList has not worked - I don't know why, but this happens on some servers (so we need to rebuild the array with the correct keys)
	$sumup_fields = array();
	$alt_sumup_fields = $nb_database->loadObjectList();
	foreach ($alt_sumup_fields as $alt_sumup_field)
	{
		$sumup_fields[$alt_sumup_field->g_key] = array();
		$sumup_fields[$alt_sumup_field->g_key]['g_key'] = $alt_sumup_field->g_key;
		$sumup_fields[$alt_sumup_field->g_key]['g_value'] = $alt_sumup_field->g_value;
	}
}

// probably a good spot for inserting my functions from the boxbilling plugin that get the token needed to proceed with creating
// and then manipulating a checkout - checkout once created, the id from that goes into the sumup widget form
// functions from box-billing here (with minor mods)
function get_sumup_token($fields) {
       // need to get an auth token first using the TOKEN_URL
       $clientid=$fields['client_id']['g_value'];
       $clientse=$fields['client_secret']['g_value'];
        $granttype="client_credentials";
        $authurl=$fields['token_url']['g_value'];    
  /*      echo "<pre>"; print_r($clientid); print_r($clientse); print_r($authurl); echo "</pre>"; */
        $tokenpayload=Array('client_id'=>$clientid,'client_secret'=>$clientse,'grant_type'=>$granttype);
        $authresponse=sumup_query($authurl, $tokenpayload, "=", "POST","");
        // do we have access_token?
        $dresponse=json_decode($authresponse);
/*               echo "<pre>"; print_r($dresponse); echo "</pre>"; */
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
 
        //  echo var_dump($headr) . " those were the headers<br/><br/>";
        //  echo   var_dump($paramfields) . " those were the paramfields<br/><br/>";
       //   echo var_dump($jresult) . " and that was the result</br></br>";
       //   echo var_dump($result) . " this is the raw result";
          return $result;
     }

// end mvb code

$product_ordered = "";
$product_code = "";
foreach ($orders as &$order)
{
	if ($order['net_price'] > 0)
	{
		//Concatenate products
		if (strlen($product_ordered) > 0)
		{
			$product_ordered .= " + ";
		}
		$product_ordered .= $order['product_name'];
		if (strlen($product_code) > 0)
		{
			$product_code .= "+";
		}
		$product_code .= $order['product_code'];
	}
}
?>

<!-- this will be where our sumup form needs to go  $sumup_fields['auth_url']['g_value'] and more are available -->
<?php
// this PHP is from my boxbilling payment plugin with a few changes to integrate it with this code MVB start
		$default_locale=$sumup_fields['default_locale']['g_value'];
		$default_currency=$sumup_fields['default_currency']['g_value'];
		$pay_to_email=$sumup_fields['pay_to_email']['g_value'];
		$mcode=$sumup_fields['client_code']['g_value'];
		$redirecturl=$sumup_fields['redirect_url']['g_value'] . "&g_tx_id=" . $g_tx_id;
		$returnurl=$sumup_fields['return_url']['g_value'] . "&g_tx_id=" . $g_tx_id;
		$testing=$sumup_fields['testing']['g_value'];
		$checkout_url=$sumup_fields['checkout_url']['g_value'];
		
		$title=$product_ordered;

        // by default the GB locale will be used to present the SumUp form
        // unless overridden in the config of the payment gateway
        if ($default_locale== "") {
            $default_locale="en-GB";
        }
        if ($default_currency== "") {
            $default_currency="EUR";
        } 
               
            $data = array();
            $data['itemname']        = $title;
            $data['currency']          = $default_currency;  // this isn't taking into account if the invoice is in a different currency - need to look at this
            $data['merchant']         = $pay_to_email;
            $data['clientemail']       = $clientemail;
            $data['totaltopay']         = $total_gross;
            $data['locale'] = $default_locale;
        
        
        $myuniqueid=uniqid(); // using this instead of session id as if the code fails we get duplicate checkout this should avoid that
        
        $accesstoken=get_sumup_token($sumup_fields);
        
        $payto=$pay_to_email;
        // $mcode=$this->config['client_code']; // already got the value earlier
        // redirect url is where control is sent once payment process completed
        // return url is where the status of the transaction is sent
        
        // change from redirect so both are notify 
        //$redirecturl=$this->config['redirect_url'] . "&currency=" . $data['currency'] . "&amount=" . $data[totaltopay] . "&pay_to_email=" . $payto;
       // $returnurl=$this->config['notify_url'] . "&currency=" . $data['currency'] . "&amount=" . $data[totaltopay] . "&pay_to_email=" . $payto;;
        
        $mycheckout=Array('checkout_reference'=>$myuniqueid,'amount'=>$data['totaltopay'],'currency'=>$data['currency'],'pay_to_email'=>$payto,'description'=>$data['itemname'], 'merchant_code'=>$mcode, 'redirect_url'=>$redirecturl,'return-url'=>$returnurl);
        
        $sumupresult=sumup_query($checkout_url,$mycheckout,":","POST",$accesstoken);
        
        // do we now have an id of the checkout resource?
        
        $idt=json_decode($sumupresult);
        $idtoken=$idt->id;
        
        if ($testing == true) {
            $test="<h1>You are in test mode - providing you are using test credentials in the payment module settings your card will not be charged.</h1>";
        } else {
            $test="";
        }
        // new addition to prevent displaying the form if there is no checkout tokenid - this could be because the 'payments' scope has not been enabled
        
        if ($idtoken != "") {
        $form  = '';
        $form .= $test;
        $form .= "<p class='alert alert-danger'>" . $data['itemname'] . "</p>";
        $form .= "<p>Receipts are sent to " . $data['clientemail'] . " (if your e-mail does not appear here or you do not recieve a receipt please get in touch with us.)</p>";
      
      
        $form .= '<script src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>';
        $form .=
                '<div id="sumup-card"></div>
                <script type="text/javascript" src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>
                <script type="text/javascript">';
        $form .= "
        SumUpCard.mount({
        checkoutId: '$idtoken',
        onResponse: function(type, body) {
            console.log('Type', type);
            console.log('Body', body);
        },
        amount: '" . $data['totaltopay'] . "',
        currency: '" . $data['currency'] ."',
        email: '" . $data['clientemail'] . "',
        locale: '" . $data['locale'] . "'
        });
                </script><p class='alert alert-info'>Once you press the pay button, please wait, do not keep pressing!</p>";

// end of my boxbilling bits MVB end

echo $form;
	} else {
		
	echo "<p class='alert alert-danger'>At the present time it is not possible to process your payment using SumUp. Please contact the site owner.</p>";
	if ($testing == true) { echo "<p class='alert alert-warning'>Please double check that you have enabled the <i>payments</i> scope with SumUp"; }
		
	}

$abort = true; //Don't redirect to success page yet!