<?php
/**
 * Copyright (c) 2012, Rakuten Deutschland GmbH. All rights reserved.
 *
 *	Redistribution and use in source and binary forms, with or without
 *	modification, are permitted provided that the following conditions are met:
 *
 *	    * Redistributions of source code must retain the above copyright
 *  	   notice, this list of conditions and the following disclaimer.
 *   	 * Redistributions in binary form must reproduce the above copyright
 *   	   notice, this list of conditions and the following disclaimer in the
 *   	   documentation and/or other materials provided with the distribution.
 *   	 * Neither the name of the Rakuten Deutschland GmbH nor the
 *      	names of its contributors may be used to endorse or promote products
 *      	derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL RAKUTEN DEUTSCHLAND GMBH BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

define('MODULE_PAYMENT_RAKUTEN_TEXT_TITLE', 'Rakuten Checkout');
define('MODULE_PAYMENT_RAKUTEN_TEXT_DESCRIPTION', 'Rakuten Checkout');
define('MODULE_PAYMENT_RAKUTEN_TEXT_INFO', '');
define('MODULE_PAYMENT_RAKUTEN_TEXT_SIGNUP', 'Additional Information and Sign Up');

define('MODULE_PAYMENT_RAKUTEN_STATUS_TITLE', 'Rakuten Checkout Active');
define('MODULE_PAYMENT_RAKUTEN_STATUS_DESC', 'Do you want to enable Rakuten Checkout?');

define('MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD_TITLE', 'Integration Method');
define('MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD_DESC', 'Which integration method do you prefer: full redirect (&quot;Standard&quot;) or iFrame version (&quot;Inline&quot;)?');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES_TITLE', 'Shipping Rates');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES_DESC', '(required)<br />CSV format: country, price, days_in_transit.<br />For example:<br /><i>DE,7.99,3<br />AT,14.99</i>');
define('MODULE_PAYMENT_RAKUTEN_SANDBOX_TITLE', 'Sandbox Mode');
define('MODULE_PAYMENT_RAKUTEN_SANDBOX_DESC', 'Do you want to use sandbox mode?');
define('MODULE_PAYMENT_RAKUTEN_DEBUG_TITLE', 'Debug Mode');
define('MODULE_PAYMENT_RAKUTEN_DEBUG_DESC', 'Do you want to enable debugging?');

define('MODULE_PAYMENT_RAKUTEN_PROJECT_ID_TITLE', 'Project ID');
define('MODULE_PAYMENT_RAKUTEN_PROJECT_ID_DESC', 'You need to sign up to get your Project ID');
define('MODULE_PAYMENT_RAKUTEN_API_KEY_TITLE', 'API Key');
define('MODULE_PAYMENT_RAKUTEN_API_KEY_DESC', 'You need to sign up to get your API Key');

define('MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE_TITLE', 'Billing Address Restrictions');
define('MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE_DESC', 'Select which type of addresses you accept');

define('MODULE_PAYMENT_RAKUTEN_ALLOWED_TITLE', 'Allowed Zones');
define('MODULE_PAYMENT_RAKUTEN_ALLOWED_DESC', 'Enter the zones <b>individually</b> that should be allowed for this module. (e.g. DE, AT or leave blank to allow all zones)');

define('MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE_TITLE', 'Order Editable Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE_DESC', 'Here you can match the editable status of orders in your system to the Rakuten editable status.');
define('MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED_TITLE', 'Shipping Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED_DESC', 'Here you can match the shipping status of orders in your system to the Rakuten shipping status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED_TITLE', 'Cancellation Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED_DESC', 'Here you can match the cancellation status of orders in your system to the Rakuten cancellation status.');

define('MODULE_PAYMENT_RAKUTEN_SORT_ORDER_TITLE', 'Display Sort Order');
define('MODULE_PAYMENT_RAKUTEN_SORT_ORDER_DESC', 'The lowest value is displayed first');

define('MODULE_PAYMENT_RAKUTEN_SUBTOTAL', 'Subtotal');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING', 'Shipping');
define('MODULE_PAYMENT_RAKUTEN_TAX', 'Tax (incl. 19% MwSt.)');
define('MODULE_PAYMENT_RAKUTEN_TOTAL', '<b>Total</b>');