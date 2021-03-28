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

//Administrator
define("NBILL_SUMUP_AUTHURL","SumUp API Authorisation URL");
  define("NBILL_SUMUP_AUTHURL_HELP","The SumUp Authorisation URL is usually something like https://api.sumup.com/authorize");
define("NBILL_SUMUP_TOKENURL","SumUp API Token URL");
  define("NBILL_SUMUP_TOKENURL_HELP","The SumUp Token URL is usually something like https://api.sumup.com/token");
define("NBILL_SUMUP_CHECKOUTURL","SumUp API Checkout URL");
  define("NBILL_SUMUP_CHECKOUTURL_HELP","The SumUp Checkout URL is usually something like https://api.sumup.com/v0.1/checkouts");
define("NBILL_SUMUP_CLIENTID","SumUp API Client ID");
 define("NBILL_SUMUP_CLIENTID_HELP","You will need to generate the JSON file in your SumUp account API section to get this");
define("NBILL_SUMUP_CLIENTSECRET","SumUp API Client Secret");
 define("NBILL_SUMUP_CLIENTSECRET_HELP","You will need to generate the JSON file in your SumUp account API section to get this");
define("NBILL_SUMUP_CLIENTCODE","SumUp Client Code");
 define("NBILL_SUMUP_CLIENTCODE_HELP","You'll find this in your profile details of your SumUp account");
define("NBILL_SUMUP_PAYTOEMAIL","SumUp Pay To Email");
 define("NBILL_SUMUP_PAYTOEMAIL_HELP","Should be self explanatory but will usually be the e-mail address you login with for your SumUp account");
define("NBILL_SUMUP_DEFAULTLOCALE","SumUp Default Local eg. en-GB");
 define("NBILL_SUMUP_DEFAULTLOCALE_HELP","You may change the default locale if you wish from en-GB to another value");
define("NBILL_SUMUP_DEFAULTCURRENCY","SumUp Default 3 letter currency eg. EUR");
 define("NBILL_SUMUP_DEFAULTCURRENCY_HELP","You may change the default currency if you wish from EUR to another value");
define("NBILL_SUMUP_DESC", "Payment Gateway for SumUp");
define("NBILL_SUMUP_TESTMODE", "Test Mode");
 define("NBILL_SUMUP_TESTMODE_HELP", "Enter 1 to signify you are using test credentials, or 0 if you are using live credentials. Only a warning message will be output when using test credentials.");
define("NBILL_SUMUP_REDIRECTURL","Redirect URL");
 define("NBILL_SUMUP_REDIRECTURL_HELP","Will usually be something like http(s)://yourwebsite.com/index.php?option=com_nbill&action=gateway&task=sumup_receipt&gateway=sumup");
define("NBILL_SUMUP_RETURNURL","Return URL");
 define("NBILL_SUMUP_RETURNURL_HELP","Will usually be the same as the redirect URL as we will handle all transaction processing there regardless of outcome");
define("NBILL_SUMUP_THANKYOUURL","If the payment is successful send the user to this page - omit the http(s)//yourwebsite.com/");
 define("NBILL_SUMUP_THANKYOUURL_HELP","You may wish to create a content page and set the URL to show that page - omit the http(s)//yourwebsite.com/");
define("NBILL_SUMUP_PROBLEMURL","If there was a problem with the payment send the user to this page - omit the http(s)//yourwebsite.com/");
 define("NBILL_SUMUP_PROBLEMURL_HELP","You may wish to create a content page and set the URL to show that page - omit the http(s)//yourwebsite.com/");


//Front End / Processing
define("NBILL_SUMUP_PAYMENT_FREQ_NOT_SUPPORTED", "ERROR - The selected payment frequency is not supported by this payment gateway.");

//Errors
define("NBILL_SUMUP_ERR_FAILURE", "Your payment was not successful. No action has been taken. The response received from your bank was: '%s'");
define("NBILL_SUMUP_CHECKOUTID_MISSING","The checkout id was missing on return from SumUp, the payment may not have been processed");

//Success
define("NBILL_SUMUP_SUCCESS", "Thank you - your payment was received successfully.");