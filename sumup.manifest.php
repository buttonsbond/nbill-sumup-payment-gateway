<?php
/**
* Feature Manifest File for SumUp gateway - indicates what features are supported by this gateway
* @version 1
* @package nBill
* @copyright (C) All Tech Plus, Rojales
* @license http://all-tech-plus.com/eula.html
*
* @access private
*
* All Rights Reserved. You may make amendments to any unencrypted files for your own use only or
* for the use of your customers if you are a website developer. HOWEVER, you are not permitted to
* re-distribute or re-sell this software in any form without the express permission of the copyright
* holder.
*
* This component was developed by All Tech Plus, Rojales (all-tech-plus.com). Use of this
* software is entirely at your own risk.
*/

//Ensure this file has been reached through a valid entry point (not always necessary eg. for class files, but included on every file to be safe!)
(defined('_VALID_MOS') || defined('_JEXEC') || defined('NBILL_VALID_NBF')) or die('Access Denied.');

class nbill_sumup_manifest
{
    /** @var Whether or not recurring payments are supported */
    public $recurring_payments = false;
    /** @var Which pre-defined recurring payment frequencies are supported (comma separated list) */
    public $defined_frequencies = 'AA';
    /** @var Whether or not the first payment can be zero (free trial) */
    public $first_payment_zero = false;
    /** @var Whether or not the first payment can be a different (non-zero) amount to the repeat payments */
    public $first_payment_different = false;
    /** @var Whether or not a fixed number of payments or expiry date is allowed (eg. for paying a fixed sum in installments) */
    public $fixed_no_of_payments = false;
    /** @var If a fixed number of payments is allowed, but there is a minimum number of installments, this property should hold the minimum (it is assumed that 1 single installment is always allowed) */
    public $minimum_no_of_payments = 1; 
}