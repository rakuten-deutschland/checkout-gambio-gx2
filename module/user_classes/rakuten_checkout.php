<?php
/**
 * Copyright (c) 2012, Rakuten Deutschland GmbH. All rights reserved.
 *
 *	Redistribution and use in source and binary forms, with or without
 *	modification, are permitted provided that the following conditions are met:
 *
 * 	 * Redistributions of source code must retain the above copyright
 *  	   notice, this list of conditions and the following disclaimer.
 * 	 * Redistributions in binary form must reproduce the above copyright
 *   	   notice, this list of conditions and the following disclaimer in the
 *   	   documentation and/or other materials provided with the distribution.
 * 	 * Neither the name of the Rakuten Deutschland GmbH nor the
 *   	   names of its contributors may be used to endorse or promote products
 *   	   derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL RAKUTEN DEUTSCHLAND GMBH BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if (!function_exists('xtc_get_zone_code')) {
	include(DIR_FS_INC . 'xtc_get_zone_code.inc.php');
}

class rakuten_checkout
{
    const VERSION               = '1.0.5';

    const ROCKIN_SANDBOX_URL    = 'https://sandbox.rakuten-checkout.de/rockin';
    const ROCKIN_LIVE_URL       = 'https://secure.rakuten-checkout.de/rockin';

    const ROCKIN_SHIPMENT_SANDBOX_URL    = 'https://sandbox.rakuten-checkout.de/rockin/shipment';
    const ROCKIN_SHIPMENT_LIVE_URL       = 'https://secure.rakuten-checkout.de/rockin/shipment';

    const ROCKBACK_URL          = 'callback/rakuten/rope.php';

    const RAKUTEN_PIPE_URL      = 'https://images.rakuten-checkout.de/images/files/pipe.html';
    const PIPE_URL              = 'callback/rakuten/pipe.php';

	var $ROCKIN_URL,
        $ROCKIN_SHIPMENT_URL,
        $ROCKBACK_URL,
        $ERROR_URL,
        $RAKUTEN_PIPE_URL,
        $PIPE_URL,
        $_order_node,
        $_process_function,
        $_request;

	function rakuten_checkout()
    {
        if (ENABLE_SSL == true) {
            if (HTTPS_SERVER != '') {
                $url_prefix = HTTPS_SERVER . DIR_WS_CATALOG;
            } else {
                $url_prefix = "https://" . $_SERVER['HTTP_HOST'] . DIR_WS_CATALOG;
            }
		} else {
            if (HTTP_SERVER != '') {
                $url_prefix = HTTP_SERVER . DIR_WS_CATALOG;
            } else {
                $url_prefix = "http://" . $_SERVER['HTTP_HOST'] . DIR_WS_CATALOG;
            }
		}

        $this->ROCKBACK_URL = $url_prefix . self::ROCKBACK_URL;
        $this->PIPE_URL     = $url_prefix . self::PIPE_URL;
        $this->ERROR_URL    = $url_prefix . FILENAME_SHOPPING_CART . '?rakuten_error_code=%s&rakuten_error=%s';

        $this->RAKUTEN_PIPE_URL = self::RAKUTEN_PIPE_URL;

        if (MODULE_PAYMENT_RAKUTEN_SANDBOX == 'Yes') {
            $this->ROCKIN_URL = self::ROCKIN_SANDBOX_URL;
            $this->ROCKIN_SHIPMENT_URL = self::ROCKIN_SHIPMENT_SANDBOX_URL;
        } else {
            $this->ROCKIN_URL = self::ROCKIN_LIVE_URL;
            $this->ROCKIN_SHIPMENT_URL = self::ROCKIN_SHIPMENT_LIVE_URL;
        }
	}

    function build_rakuten_checkout_button()
    {
        if (defined('MODULE_PAYMENT_RAKUTEN_STATUS') && MODULE_PAYMENT_RAKUTEN_STATUS == 'True') {
            if ($_SESSION['languages_id']=='2') { // de
                $source = 'rakuten/images/checkout-button_light-bg_175x50.png';
            } else {
                $source = 'rakuten/images/checkout-button_light-bg_175x50.png';
            }
            return '<a class="paypal_checkout" href="'.xtc_href_link('rakuten_checkout.php', '', 'SSL').'"><img border="0" src="'.$source.'"></a>';
        }
    }

    function build_rakuten_checkout_error()
    {
        if (isset($_GET['rakuten_error_code']) && isset($_GET['rakuten_error'])) {
            return '<p><strong><font color=red>Error #' . htmlentities(stripslashes($_GET['rakuten_error_code'])) . ': '
                   . htmlentities(stripslashes($_GET['rakuten_error'])) . '</font></strong></p>';
        }
    }

    function _str_get_csv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null)
    {
        $temp=fopen("php://memory", "rw");
        fwrite($temp, $input);
        fseek($temp, 0);
        $r = array();
        while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
            $r[] = $data;
        }
        fclose($temp);
        return $r;
    }

    function _escape_str($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8');
        $string = str_replace('&', '&amp;', $string);
        return $string;
    }

    function _add_CDATA($node, $value)
    {
        $value = mb_convert_encoding($value, 'UTF-8');
        $domNode = dom_import_simplexml($node);
        $domDoc = $domNode->ownerDocument;
        $domNode->appendChild($domDoc->createCDATASection($value));
    }

    function get_redirect_url($inline = false)
    {
        /**
         * Create Rakuten Checkout Insert Cart XML request
         */
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><tradoria_insert_cart />");

        $merchantAuth = $xml->addChild('merchant_authentication');
        $merchantAuth->addChild('project_id', MODULE_PAYMENT_RAKUTEN_PROJECT_ID);
        $merchantAuth->addChild('api_key', MODULE_PAYMENT_RAKUTEN_API_KEY);


        $xml->addChild('language', 'DE');
        $xml->addChild('currency', $_SESSION['currency']);

        $merchantCart = $xml->addChild('merchant_carts')->addChild('merchant_cart');
        
        $merchantCart->addChild('custom_1', xtc_session_name());
        $merchantCart->addChild('custom_2', xtc_session_id());
        $merchantCart->addChild('custom_3', $_SESSION['customer_id']);
        $merchantCart->addChild('custom_4');

        $merchantCartItems = $merchantCart->addChild('items');

		if ($_SESSION['cart']->count_contents() > 0) {

			$products = $_SESSION['cart']->get_products();

			for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {

                $t_image = '';
                if ($products[$i]['image'] != '') {
                    $t_image = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $products[$i]['image'];
                }

                $merchantCartItemsItem = $merchantCartItems->addChild('item');

                $merchantCartItemsItemName = $merchantCartItemsItem->addChild('name');
                $this->_add_CDATA($merchantCartItemsItemName, $products[$i]['name']);

                $merchantCartItemsItem->addChild('sku', $this->_escape_str($products[$i]['model']));
                $merchantCartItemsItem->addChild('external_product_id', $this->_escape_str($products[$i]['id']));
                $merchantCartItemsItem->addChild('qty', $products[$i]['quantity']);
                $merchantCartItemsItem->addChild('unit_price', $products[$i]['price']);
                $merchantCartItemsItem->addChild('tax_class', $this->get_rakuten_tax_class($products[$i]['tax_class_id']));
                $merchantCartItemsItem->addChild('image_url', $this->_escape_str($t_image));

                $product_url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'],$products[$i]['name']));
                $merchantCartItemsItem->addChild('product_url', $this->_escape_str($product_url));

                $comment = array();
                if (isset ($products[$i]['attributes'])) {
                    while (list ($option, $value) = each($products[$i]['attributes'])) {
                        $attributes = xtc_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock,pa.products_attributes_id,pa.attributes_model,pa.weight_prefix,pa.options_values_weight
                                                              from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                                              where pa.products_id = '".(int)$products[$i]['id']."'
                                                               and pa.options_id = '".(int)$option."'
                                                               and pa.options_id = popt.products_options_id
                                                               and pa.options_values_id = '".(int)$value."'
                                                               and pa.options_values_id = poval.products_options_values_id
                                                               and popt.language_id = '".(int) $_SESSION['languages_id']."'
                                                               and poval.language_id = '".(int) $_SESSION['languages_id']."'");
                        $attributes_values = xtc_db_fetch_array($attributes);
                        $comment[] = $attributes_values['products_options_name'] . ': ' . $attributes_values['products_options_values_name'];
                    }
                }
                $comment = implode('; ', $comment);

                $merchantCartItemsItemComment = $merchantCartItemsItem->addChild('comment');
                $this->_add_CDATA($merchantCartItemsItemComment, $comment);

                $merchantCartItemsItemCustom = $merchantCartItemsItem->addChild('custom');
                $this->_add_CDATA($merchantCartItemsItemCustom, $products[$i]['id']);
			}
        }

        $merchantCartShippingRates = $merchantCart->addChild('shipping_rates');

        $shippingRates = $this->_str_get_csv(MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES);

        foreach ($shippingRates as $shippingRate) {
            if (isset($shippingRate[0]) && isset($shippingRate[1]) && is_numeric($shippingRate[1])) {               
                $merchantCartShippingRate = $merchantCartShippingRates->addChild('shipping_rate');
                $merchantCartShippingRate->addChild('country', (string)$shippingRate[0]);
                $merchantCartShippingRate->addChild('price', (float)$shippingRate[1]);
                if (isset ($shippingRate[2]) && (int)$shippingRate[2]>0) {
                    $merchantCartShippingRate->addChild('delivery_date', date('Y-m-d', strtotime('+' . (int)$shippingRate[2] . ' days')));
                }
            }
        }

        $billingAddressRestrictions = $xml->addChild('billing_address_restrictions');
                                            // restrict invoice address to require private / commercial and by country
        switch (MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE) {
            // 1=all 2=business 3=private
            case 'All Addresses':
                $billingAddressRestrictions->addChild('customer_type')->addAttribute('allow', 1);
                break;
            case 'Business Addresses Only':
                $billingAddressRestrictions->addChild('customer_type')->addAttribute('allow', 2);
                break;
            case 'Private Addresses Only':
                $billingAddressRestrictions->addChild('customer_type')->addAttribute('allow', 3);
                break;
        }
       
        $xml->addChild('callback_url', $this->ROCKBACK_URL);
        $xml->addChild('pipe_url', $this->PIPE_URL);

        $request = $xml->asXML();

        $response = $this->send_request($request);

        $redirectUrl = false;
        $inlineUrl = false;
        $inlineCode = false;

        try {
            $response = new SimpleXMLElement($response);
            if ($response->success != 'true') {
                throw new Exception((string)$response->message, (int)$response->code);
            } else {
                $redirectUrl = $response->redirect_url;
                $inlineUrl = $response->inline_url;
                $inlineCode = $response->inline_code;
            }
        } catch (Exception $e) {
            xtc_redirect(sprintf($this->ERROR_URL, urlencode($e->getCode()), urlencode($e->getMessage())));            
        }

        if ($inline) {
            return $inlineCode;
        } else {
            return $redirectUrl;
        }
    }

    function send_order_shipment($oID, $old_status)
    {
        $check_status_query = xtc_db_query("select rakuten_order_no, orders_status from ".TABLE_ORDERS." where orders_id = '".$oID."'");
        $check_status = xtc_db_fetch_array($check_status_query);

        if ($check_status['rakuten_order_no']
            && $check_status['orders_status'] != $old_status
            && $check_status['orders_status'] == MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED) {
				
        		/**
        		 * Create Rakuten Checkout Send Order Shipment XML request
        		 */
            $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><tradoria_order_shipment />");

            $merchantAuth = $xml->addChild('merchant_authentication');
            $merchantAuth->addChild('project_id', MODULE_PAYMENT_RAKUTEN_PROJECT_ID);
            $merchantAuth->addChild('api_key', MODULE_PAYMENT_RAKUTEN_API_KEY);

            $xml->addChild('order_no', $check_status['rakuten_order_no']);
            $carrierTrackingId = $xml->addChild('carrier_tracking_id');
            $carrierTrackingUrl = $xml->addChild('carrier_tracking_url');
            $carrierTrackingCode = $xml->addChild('carrier_tracking_code');

            $request = $xml->asXML();

            $response = $this->send_request($request, $this->ROCKIN_SHIPMENT_URL);

            global $messageStack;

            try {
                $response = simplexml_load_string($response);
                if ($response->success == 'true') {
                    $messageStack->add_session('<strong>Successfully sent order shipment to Rakuten Checkout.</strong>', 'success');
                    return true;
                } else {
                    throw new Exception((string)$response->message, (int)$response->code);
                }
            } catch (Exception $e) {
                if ($e->getCode()) {
                    $error_code = $e->getCode();
                } else {
                    $error_code = '000';
                }
                if ($e->getMessage()) {
                    $error_message = $e->getMessage();
                } else {
                    $error_message = 'Unknown error';
                }

                $messageStack->add_session('<strong>Error sending shipment to Rakuten Checkout. Shipment wasn\'t sent.</strong><br />
                        Error #' . htmlentities($error_code) . ': ' . htmlentities($error_message), 'warning');

                return false;
            }
        }
    }

    function send_request($xml, $rockin_url=null)
    {
        try {
            if (is_null($rockin_url)) {
                $rockin_url = $this->ROCKIN_URL;
            }
   
            /**
             * Setting the curl parameters. 
             */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rockin_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

            /**
             * Setting the request fields
             */
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            /**
             * Get server response
             */
            $response = curl_exec($ch);
         
            if(curl_errno($ch)) {                
                $_SESSION['curl_error_no'] = curl_errno($ch) ;
                $_SESSION['curl_error_msg'] = curl_error($ch);                
            } else {
                /** Close CURL connection **/
                curl_close($ch);
            }
        } catch (Exception $e) {            
            throw $e;
        }

        return $response;
    }

    function prepare_response($success, $tag = 'general_error')
    {
        if ($success === true) {
            $success = 'true';
        } elseif ($success === false) {
            $success = 'false';
        } else {
            $success = (string)$success;
        }

        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><{$tag} />");
        $xml->addChild('success', $success);
        $response = $xml->asXML();

        return $response;
    }

    function _auth()
    {
        if (trim((string)$this->_request->merchant_authentication->project_id) == trim(MODULE_PAYMENT_RAKUTEN_PROJECT_ID)
                && trim((string)$this->_request->merchant_authentication->api_key) == trim(MODULE_PAYMENT_RAKUTEN_API_KEY)) {
            return true;
        } else {
            return false;
        }
    }

    function process_rope_request($request)
    {        
        try {
            $this->_request = new SimpleXMLElement(urldecode($request), LIBXML_NOCDATA);

            if (!$this->_auth()) {
                throw new Exception('Authentication failed');
            }

            $init_session = true;
				/**	
				 * Check type of request and call proper handler
				 */
            switch ($this->_request->getName()) {
                case 'tradoria_check_order':
                    $this->_order_node = 'order';
                    $this->_process_function = '_check_order';
                    $response_tag = 'tradoria_check_order_response';
                    break;
                case 'tradoria_order_process':
                    $this->_order_node = 'cart';
                    $this->_process_function = '_process_order';
                    $response_tag = 'tradoria_order_process_response';
                    break;
                case 'tradoria_order_status':
                    $init_session = false;
                    $this->_process_function = '_status_update';
                    $response_tag = 'tradoria_order_status_response';
                    break;
                default:
                    /**
                     * Unrecognised request error
                     */
                    $response_tag = 'unknown_error';
                    return $this->prepare_response(false, $response_tag);
            }

            if ($init_session) {
                /** 
                 * Instantiate Session
                 */
                $session_name = (string)$this->_request->{$this->_order_node}->custom_1;
                $session_id = (string)$this->_request->{$this->_order_node}->custom_2;
                $customer_id = (string)$this->_request->{$this->_order_node}->custom_3;

                xtc_session_name($session_name);
                if (STORE_SESSIONS != 'mysql') session_save_path(SESSION_WRITE_DIRECTORY);
                xtc_session_id($session_id);
                xtc_session_start();

                /**
                 * Load the correct language file
                 */
                require_once (DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/rakuten.php');

                require_once (DIR_WS_CLASSES . 'xtcPrice.php');
                global $xtPrice;
                $xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

                require_once (DIR_WS_CLASSES . 'main.php');
                global $main;
                $main = new main();
            }

            $response = $this->{$this->_process_function}();

        } catch (Exception $e) {           
            return $this->prepare_response(false);
        }

        return $this->prepare_response($response, $response_tag);
    }

    function _check_order()
    {
        require_once (DIR_FS_INC . 'xtc_get_products_stock.inc.php');
        $gm_stock_error = false;
        if (STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT == 'false') {
            $gm_attributes_array = array_keys($_SESSION['cart']->contents);
            for ($i = 0; $i < count($gm_attributes_array); $i++) {
                if (strstr($gm_attributes_array[$i], '{') != false) {
                    if (ATTRIBUTE_STOCK_CHECK == 'true') {
                        $gm_attribute_array = explode('{', str_replace('}', '{', $gm_attributes_array[$i]));
                        $gm_attributes = $_SESSION['cart']->contents[$gm_attributes_array[$i]]['attributes'];
                        for ($j = 0; $j < count($gm_attributes); $j++) {
                            $gm_attribute_stock = xtc_db_query("SELECT products_attributes_id
                                                                        FROM products_attributes
                                                                        WHERE products_id = '" . $gm_attribute_array[0] . "'
                                                                            AND options_id = '" . key($gm_attributes) . "'
                                                                            AND options_values_id = '" . current($gm_attributes) . "'
                                                                            AND (attributes_stock - " . $_SESSION['cart']->contents[$gm_attributes_array[$i]]['qty'] . ") < 0");
                            if (mysql_num_rows($gm_attribute_stock) == 1) {
                                $gm_stock_error = true;
                            }
                            next($gm_attributes);
                        }
                    }
                } elseif(xtc_get_products_stock($gm_attributes_array[$i]) - (double)$_SESSION['cart']->contents[$gm_attributes_array[$i]]['qty'] < 0) {
                    $gm_stock_error = true;
                }
            }
        }
        return (!$gm_stock_error);
    }

    function _process_order()
    {
        try {
        		/**
        		 * Process the internal cartID to match the cartID in the $_SESSION 
        		 */
            if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
                if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
                    return false;
                }
            }

            $order = new order();

            /** 
             * PropertiesControl Object
             */
            $coo_properties = MainFactory::create_object('PropertiesControl');

            $tmp_status = $order->info['order_status'];

            if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                $discount = $_SESSION['customers_status']['customers_status_ot_discount'];
            } else {
                $discount = '0.00';
            }
            
            if (gm_get_conf("GM_SHOW_IP") == '1' && gm_get_conf("GM_LOG_IP") == '1') {
                $customers_ip = $_SESSION['user_info']['user_ip'];
            }

            $comments = '';

            
            if (trim((string)$this->_request->comment_client) != '') {
                $comments .= sprintf('Customer\'s Comment: %s', trim((string)$this->_request->comment_client) . "\n");
            }

            $comments .= sprintf('Rakuten Order No: %s', (string)$this->_request->order_no . "\n")
                        . sprintf('Rakuten Client ID: %s', (string)$this->_request->client->client_id . "\n");

            $order->info['comments'] = $comments;

            $order->info['rakuten_order_no'] = (string)$this->_request->order_no;

            $billing_addr = $this->_request->client;

            $billing_country_result = xtc_db_query("SELECT countries_id, countries_name from " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . (string)$billing_addr->country . "' ");
            if (xtc_db_num_rows($billing_country_result)) {
                $billing_country = xtc_db_fetch_array($billing_country_result);
            } else {
                $billing_country['countries_id'] = -1;
                $billing_country['countries_name'] = (string)$billing_addr->country;
            }

            $order->billing['firstname'] = (string)$billing_addr->first_name;
            $order->billing['lastname'] = (string)$billing_addr->last_name;
            $order->billing['company'] = (string)$billing_addr->company;
            $order->billing['street_address'] = (string)$billing_addr->street . " " . (string)$billing_addr->street_no . ((string)$billing_addr->address_add ? '<br />'.(string)$billing_addr->address_add : '');
            $order->billing['city'] = (string)$billing_addr->city;
            $order->billing['postcode'] = (string)$billing_addr->zip_code;
            $order->billing['country']['title'] = $billing_country['countries_name'];
            $order->billing['country']['iso_code_2'] = (string)$billing_addr->country;
            $order->billing['format_id'] = '5';

            $shipping_addr = $this->_request->delivery_address;

            $shipping_country_result = xtc_db_query("SELECT countries_id, countries_name from " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . (string)$shipping_addr->country . "' ");
            if (xtc_db_num_rows($shipping_country_result)) {
                $shipping_country = xtc_db_fetch_array($shipping_country_result);
            } else {
                $shipping_country['countries_id'] = -1;
                $shipping_country['countries_name'] = (string)$shipping_addr->country;
            }

            $order->delivery['firstname'] = (string)$shipping_addr->first_name;
            $order->delivery['lastname'] = (string)$shipping_addr->last_name;
            $order->delivery['company'] = (string)$shipping_addr->company;
            $order->delivery['street_address'] = (string)$shipping_addr->street . " " . (string)$shipping_addr->street_no . ((string)$shipping_addr->address_add ? '<br />'.(string)$shipping_addr->address_add : '');
            $order->delivery['city'] = (string)$shipping_addr->city;
            $order->delivery['postcode'] = (string)$shipping_addr->zip_code;
            $order->delivery['country']['title'] = $shipping_country['countries_name'];
            $order->delivery['country']['iso_code_2'] = (string)$shipping_addr->country;
            $order->delivery['format_id'] = '5';

            $order->info['payment_method'] = 'rakuten';
            $order->info['payment_class'] = '';
            $order->info['shipping_method'] = 'rakuten';
            $order->info['shipping_class'] = '';

            $sql_data_array = array('customers_id' => $_SESSION['customer_id'],
                                    'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
                                    'customers_firstname' => $order->customer['firstname'],
                                    'customers_lastname' => $order->customer['lastname'],
                                    'customers_cid' => $order->customer['csID'],
                                    'customers_vat_id' => $_SESSION['customer_vat_id'],
                                    'customers_company' => $order->customer['company'],
                                    'customers_status' => $_SESSION['customers_status']['customers_status_id'],
                                    'customers_status_name' => $_SESSION['customers_status']['customers_status_name'],
                                    'customers_status_image' => $_SESSION['customers_status']['customers_status_image'],
                                    'customers_status_discount' => $discount,
                                    'customers_street_address' => $order->customer['street_address'],
                                    'customers_suburb' => $order->customer['suburb'],
                                    'customers_city' => $order->customer['city'],
                                    'customers_postcode' => $order->customer['postcode'],
                                    'customers_state' => $order->customer['state'],
                                    'customers_country' => $order->customer['country']['title'],
                                    'customers_telephone' => $order->customer['telephone'],
                                    'customers_email_address' => $order->customer['email_address'],
                                    'customers_address_format_id' => $order->customer['format_id'],

                                    'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
                                    'delivery_firstname' => $order->delivery['firstname'],
                                    'delivery_lastname' => $order->delivery['lastname'],
                                    'delivery_company' => $order->delivery['company'],
                                    'delivery_street_address' => $order->delivery['street_address'],
                                    'delivery_suburb' => $order->delivery['suburb'],
                                    'delivery_city' => $order->delivery['city'],
                                    'delivery_postcode' => $order->delivery['postcode'],
                                    'delivery_state' => $order->delivery['state'],
                                    'delivery_country' => $order->delivery['country']['title'],
                                    'delivery_country_iso_code_2' => $order->delivery['country']['iso_code_2'],
                                    'delivery_address_format_id' => $order->delivery['format_id'],

                                    'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
                                    'billing_firstname' => $order->billing['firstname'],
                                    'billing_lastname' => $order->billing['lastname'],
                                    'billing_company' => $order->billing['company'],
                                    'billing_street_address' => $order->billing['street_address'],
                                    'billing_suburb' => $order->billing['suburb'],
                                    'billing_city' => $order->billing['city'],
                                    'billing_postcode' => $order->billing['postcode'],
                                    'billing_state' => $order->billing['state'],
                                    'billing_country' => $order->billing['country']['title'],
                                    'billing_country_iso_code_2' => $order->billing['country']['iso_code_2'],
                                    'billing_address_format_id' => $order->billing['format_id'],

                                    'payment_method' => $order->info['payment_method'],
                                    'payment_class' => $order->info['payment_class'],
                                    'shipping_method' => $order->info['shipping_method'],
                                    'shipping_class' => $order->info['shipping_class'],

                                    'cc_type' => $order->info['cc_type'],
                                    'cc_owner' => $order->info['cc_owner'],
                                    'cc_number' => $order->info['cc_number'],
                                    'cc_expires' => $order->info['cc_expires'],
                                    'cc_start' => $order->info['cc_start'],
                                    'cc_cvv' => $order->info['cc_cvv'],
                                    'cc_issue' => $order->info['cc_issue'],

                                    'date_purchased' => 'now()',
                                    'orders_status' => $tmp_status,
                                    'currency' => $order->info['currency'],
                                    'currency_value' => $order->info['currency_value'],
                                    'customers_ip' => $customers_ip,
                                    'language' => $_SESSION['language'],
                                    'comments' => $order->info['comments'],

                                    'rakuten_order_no' => $order->info['rakuten_order_no'],
            );

            xtc_db_perform(TABLE_ORDERS, $sql_data_array);
            $insert_id = xtc_db_insert_id();
            $_SESSION['tmp_oID'] = $insert_id;

            $sql_data_array = array('orders_id' => $insert_id,
                                    'title' => MODULE_PAYMENT_RAKUTEN_SUBTOTAL . ':',
                                    'text' => ' ' . sprintf("%01.2f EUR", (float)$this->_request->total - (float)$this->_request->shipping - (float)$this->_request->total_tax_amount), // TODO: format currency
                                    'value' => ((float)$this->_request->total - (float)$this->_request->shipping - (float)$this->_request->total_tax_amount),
                                    'class' => 'ot_subtotal',
                                    'sort_order' => 10);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            $sql_data_array = array('orders_id' => $insert_id,
                                    'title' => MODULE_PAYMENT_RAKUTEN_SHIPPING . ':',
                                    'text' => ' ' . sprintf("%01.2f EUR", (float)$this->_request->shipping),
                                    'value' => (float)$this->_request->shipping,
                                    'class' => 'ot_shipping',
                                    'sort_order' => 30);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            $sql_data_array = array('orders_id' => $insert_id,
                                    'title' => MODULE_PAYMENT_RAKUTEN_TAX . ':',
                                    'text' => ' ' . sprintf("%01.2f EUR", (float)$this->_request->total_tax_amount),
                                    'value' => (float)$this->_request->total_tax_amount,
                                    'class' => 'ot_tax',
                                    'sort_order' => 97);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            $sql_data_array = array('orders_id' => $insert_id,
                                    'title' => MODULE_PAYMENT_RAKUTEN_TOTAL . ':',
                                    'text' => sprintf("<b> %01.2f EUR</b>", (float)$this->_request->total),
                                    'value' => (float)$this->_request->total,
                                    'class' => 'ot_total',
                                    'sort_order' => 99);
            xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

            $customer_notification = '0';
            $sql_data_array = array('orders_id' => $insert_id, 'orders_status_id' => $order->info['order_status'], 'date_added' => 'now()', 'customer_notified' => $customer_notification, 'comments' => $order->info['comments']);
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);


            require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');

            for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                /**
                 * Stock update
                 */
                if (STOCK_LIMITED == 'true') {
                    if (DOWNLOAD_ENABLED == 'true') {                       
                        $stock_query_raw = "SELECT p.products_quantity, pad.products_attributes_filename
                                                    FROM " . TABLE_PRODUCTS . " p
                                                    LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                                     ON p.products_id=pa.products_id
                                                    LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                                     ON pa.products_attributes_id=pad.products_attributes_id
                                                    WHERE p.products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'";
                        
                        $products_attributes = $order->products[$i]['attributes'];
                        if (is_array($products_attributes)) {
                            $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
                        }
                        $stock_query = xtc_db_query($stock_query_raw);
                    } else {
                        $stock_query = xtc_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'");
                    }
                    if (xtc_db_num_rows($stock_query) > 0) {
                        $stock_values = xtc_db_fetch_array($stock_query);
                        
                        /** 
                         * Do not decrement quantities if products_attributes_filename exists
                         */
                        if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
                            $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
                        } else {
                            $stock_left = $stock_values['products_quantity'];
                        }

                        xtc_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'");
                       
                        if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false') && GM_SET_OUT_OF_STOCK_PRODUCTS == 'true') {
                            xtc_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'");
                        }
                        
                        set_shipping_status($order->products[$i]['id']);

                        if ($stock_left <= STOCK_REORDER_LEVEL) {
                            $gm_get_products_name = xtc_db_query("SELECT products_name
                                                                                            FROM products_description
                                                                                            WHERE
                                                                                                products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'
                                                                                                AND language_id = '" . $_SESSION['languages_id'] . "'");
                            $gm_stock_data = mysql_fetch_array($gm_get_products_name);

                            $gm_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $gm_stock_data['products_name'];
                            $gm_body = GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$stock_left . "\n\n" .
                                       HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($order->products[$i]['id']);

                            /**
                             * Send the email
                             */
                            xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', '', $gm_subject, nl2br(htmlentities($gm_body)), $gm_body);
                        }
                    }
                }

                /**
                 * Update products_ordered (for bestsellers list)
                 */
                xtc_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . (double)$order->products[$i]['qty'] . " where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "'");

                $sql_data_array = array('orders_id' => $insert_id, 'products_id' => xtc_get_prid($order->products[$i]['id']), 'products_model' => $order->products[$i]['model'], 'products_name' => $order->products[$i]['name'], 'products_shipping_time' => $order->products[$i]['shipping_time'], 'products_price' => $order->products[$i]['price'], 'final_price' => $order->products[$i]['final_price'], 'products_tax' => xtc_get_tax_rate($order->products[$i]['tax_class_id'], $shipping_country['countries_id']), 'products_discount_made' => $order->products[$i]['discount_allowed'], 'products_quantity' => $order->products[$i]['qty'], 'allow_tax' => $_SESSION['customers_status']['customers_status_show_price_tax']);

                xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
                $order_products_id = xtc_db_insert_id();

                if (!empty($order->products[$i]['quantity_unit_id'])) {
                    xtc_db_query("INSERT INTO orders_products_quantity_units
                        SET orders_products_id = '" . (int)$order_products_id . "',
                            quantity_unit_id = '" . (int)$order->products[$i]['quantity_unit_id'] . "',
                            unit_name = '" . xtc_db_input($order->products[$i]['unit_name']) . "'");
                }

                /** 
                 * Save selected properties_combi in product
                 */
                $t_combis_id = $coo_properties->extract_combis_id($order->products[$i]['id']);

                $GLOBALS['coo_debugger']->log('checkout_process: $order->products[$i][id] ' . $order->products[$i]['id'], 'Properties');
                $GLOBALS['coo_debugger']->log('checkout_process: extract_combis_id ' . $t_combis_id, 'Properties');

                if (empty($t_combis_id) == false) {
                    $coo_properties->add_properties_combi_to_orders_product($t_combis_id, $order_products_id);                    
                    /**
                     * Update properties_combi quantity
                     */ 
                    $t_quantity_change = $order->products[$i]['qty'] * -1;
                    $val = $coo_properties->change_combis_quantity($t_combis_id, $t_quantity_change);
                }

                $specials_result = xtc_db_query("SELECT products_id, specials_quantity from " . TABLE_SPECIALS . " WHERE products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
                if (xtc_db_num_rows($specials_result)) {
                    $spq = xtc_db_fetch_array($specials_result);

                    $new_sp_quantity = ($spq['specials_quantity'] - $order->products[$i]['qty']);

                    if ($new_sp_quantity >= 1) {
                        xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $new_sp_quantity . "' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
                    } elseif (STOCK_CHECK == 'true') {
                        xtc_db_query("update " . TABLE_SPECIALS . " set status = '0', specials_quantity = '" . $new_sp_quantity . "' where products_id = '" . xtc_get_prid($order->products[$i]['id']) . "' ");
                    }
                }

                if (isset ($order->products[$i]['attributes'])) {
                    $attributes_exist = '1';
                    for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
                        if (DOWNLOAD_ENABLED == 'true') {
                            $attributes_query = "select popt.products_options_name,
                                                               poval.products_options_values_name,
                                                               pa.options_values_price,
                                                               pa.price_prefix,
                                                               pad.products_attributes_maxdays,
                                                               pad.products_attributes_maxcount,
                                                               pad.products_attributes_filename
                                                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                                                on pa.products_attributes_id=pad.products_attributes_id
                                                               where pa.products_id = '" . $order->products[$i]['id'] . "'
                                                                and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                                and pa.options_id = popt.products_options_id
                                                                and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                                and pa.options_values_id = poval.products_options_values_id
                                                                and popt.language_id = '" . $_SESSION['languages_id'] . "'
                                                                and poval.language_id = '" . $_SESSION['languages_id'] . "'";
                            $attributes = xtc_db_query($attributes_query);
                        } else {
                            $attributes = xtc_db_query("select popt.products_options_name,
                                                                             poval.products_options_values_name,
                                                                             pa.options_values_price,
                                                                             pa.price_prefix
                                                                             from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                                                             where pa.products_id = '" . $order->products[$i]['id'] . "'
                                                                             and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                                             and pa.options_id = popt.products_options_id
                                                                             and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                                             and pa.options_values_id = poval.products_options_values_id
                                                                             and popt.language_id = '" . $_SESSION['languages_id'] . "'
                                                                             and poval.language_id = '" . $_SESSION['languages_id'] . "'");
                        }
                        /**
                         * update attribute stock
                         */ 
                        xtc_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " set
                                                       attributes_stock=attributes_stock - '" . $order->products[$i]['qty'] . "'
                                                       where
                                                       products_id='" . $order->products[$i]['id'] . "'
                                                       and options_values_id='" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                       and options_id='" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                       ");

                        $attributes_values = xtc_db_fetch_array($attributes);

                        $sql_data_array = array('orders_id' => $insert_id, 'orders_products_id' => $order_products_id, 'products_options' => $attributes_values['products_options_name'], 'products_options_values' => $attributes_values['products_options_values_name'], 'options_values_price' => $attributes_values['options_values_price'], 'price_prefix' => $attributes_values['price_prefix']);
                        xtc_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                        if ((DOWNLOAD_ENABLED == 'true') && isset ($attributes_values['products_attributes_filename']) && xtc_not_null($attributes_values['products_attributes_filename'])) {
                            $sql_data_array = array('orders_id' => $insert_id, 'orders_products_id' => $order_products_id, 'orders_products_filename' => $attributes_values['products_attributes_filename'], 'download_maxdays' => $attributes_values['products_attributes_maxdays'], 'download_count' => $attributes_values['products_attributes_maxcount']);
                            xtc_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                        }

                        /**
                         * BOF GM_MOD attributes stock_notifier
                         */
                        $gm_get_attributes_stock = xtc_db_query("SELECT
                                                                                                    pd.products_name,
                                                                                                    pa.attributes_stock,
                                                                                                    po.products_options_name,
                                                                                                    pov.products_options_values_name
                                                                                                FROM
                                                                                                    products_description pd,
                                                                                                    products_attributes pa,
                                                                                                    products_options po,
                                                                                                    products_options_values pov
                                                                                                WHERE pa.products_id = '" . $order->products[$i]['id'] . "'
                                                                   AND pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                                   AND pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                                                                 AND po.products_options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                                                                 AND po.language_id = '" . $_SESSION['languages_id'] . "'
                                                                                                 AND pov.products_options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                                                                 AND pov.language_id = '" . $_SESSION['languages_id'] . "'
                                                                                                 AND pd.products_id = '" . $order->products[$i]['id'] . "'
                                                                                                 AND pd.language_id = '" . $_SESSION['languages_id'] . "'");
                        if (xtc_db_num_rows($gm_get_attributes_stock) == 1) {
                            $gm_attributes_stock_data = xtc_db_fetch_array($gm_get_attributes_stock);

                            if ($gm_attributes_stock_data['attributes_stock'] <= STOCK_REORDER_LEVEL) {
                                $gm_subject = GM_OUT_OF_STOCK_NOTIFY_TEXT . ' ' . $gm_attributes_stock_data['products_name'] . ' - ' . $gm_attributes_stock_data['products_options_name'] . ': ' . $gm_attributes_stock_data['products_options_values_name'];
                                $gm_body = GM_OUT_OF_STOCK_NOTIFY_TEXT . ': ' . (double)$gm_attributes_stock_data['attributes_stock'] . ' (' . $gm_attributes_stock_data['products_name'] . ' - ' . $gm_attributes_stock_data['products_options_name'] . ': ' . $gm_attributes_stock_data['products_options_values_name'] . ")\n\n" .
                                           HTTP_SERVER . DIR_WS_CATALOG . 'product_info.php?info=p' . xtc_get_prid($order->products[$i]['id']);

                                xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', STORE_OWNER_EMAIL_ADDRESS, STORE_NAME, '', '', $gm_subject, nl2br(htmlentities($gm_body)), $gm_body);
                            }
                        }                      

                    }
                }                
                $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
                $total_cost += $total_products_price;
            }

            if (isset ($_SESSION['tracking']['refID'])) {
                xtc_db_query("update " . TABLE_ORDERS . " set
                                     refferers_id = '" . $_SESSION['tracking']['refID'] . "'
                                     where orders_id = '" . $insert_id . "'");

                /** 
                 * Check if late or direct sale 
                 */
                $customers_logon_query = "SELECT customers_info_number_of_logons
                                            FROM " . TABLE_CUSTOMERS_INFO . "
                                            WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
                $customers_logon_query = xtc_db_query($customers_logon_query);
                $customers_logon = xtc_db_fetch_array($customers_logon_query);

                if ($customers_logon['customers_info_number_of_logons'] == 0) {
                    /**
                     * direct sale
                     */ 
                    xtc_db_query("update " . TABLE_ORDERS . " set
                                         conversion_type = '1'
                                         where orders_id = '" . $insert_id . "'");
                } else {
                    /**
                     * late sale
                     */
                    xtc_db_query("update " . TABLE_ORDERS . " set
                                         conversion_type = '2'
                                         where orders_id = '" . $insert_id . "'");
                }

            } else {
                $customers_query = xtc_db_query("SELECT refferers_id as ref FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $_SESSION['customer_id'] . "'");
                $customers_data = xtc_db_fetch_array($customers_query);
                if (xtc_db_num_rows($customers_query)) {
                    xtc_db_query("update " . TABLE_ORDERS . " set
                                         refferers_id = '" . $customers_data['ref'] . "'
                                         where orders_id = '" . $insert_id . "'");
                    /** 
                     * check if late or direct sale
                     */
                    $customers_logon_query = "SELECT customers_info_number_of_logons
                                                FROM " . TABLE_CUSTOMERS_INFO . "
                                                WHERE customers_info_id  = '" . $_SESSION['customer_id'] . "'";
                    $customers_logon_query = xtc_db_query($customers_logon_query);
                    $customers_logon = xtc_db_fetch_array($customers_logon_query);

                    if ($customers_logon['customers_info_number_of_logons'] == 0) {
                        /**
                         * Direct sale
                         */
                        xtc_db_query("update " . TABLE_ORDERS . " set
                                             conversion_type = '1'
                                             where orders_id = '" . $insert_id . "'");
                    } else {
                        /**
                         * Late sale
                         */
                        xtc_db_query("update " . TABLE_ORDERS . " set
                                             conversion_type = '2'
                                             where orders_id = '" . $insert_id . "'");
                    }
                }
            }

            $_SESSION['cart']->reset(true);

            /**
             * Unregister session variables used during checkout
             */ 
            unset ($_SESSION['sendto']);
            unset ($_SESSION['billto']);
            unset ($_SESSION['shipping']);
            unset ($_SESSION['payment']);
            unset ($_SESSION['comments']);
            unset ($_SESSION['last_order']);
            unset ($_SESSION['tmp_oID']);
            unset ($_SESSION['cc']);
            unset ($_SESSION['nvpReqArray']);
            unset ($_SESSION['reshash']);
            $last_order = $insert_id;            
            if (isset ($_SESSION['credit_covers']))
                unset ($_SESSION['credit_covers']);
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    function _status_update()
    {
        $rakuten_order_no = (string)$this->_request->order_no;
        $status = $this->get_status((string)$this->_request->status);
        $oID = false;

        $order_updated = false;
        $check_status_query = xtc_db_query("select orders_id, customers_name, customers_email_address, orders_status, date_purchased from ".TABLE_ORDERS." where rakuten_order_no = '".xtc_db_input($rakuten_order_no)."'");
        if ($check_status = xtc_db_fetch_array($check_status_query)) {
            $oID = $check_status['orders_id'];
        }
        if ($oID && $check_status['orders_status'] != $status) {
            xtc_db_query("update ".TABLE_ORDERS." set orders_status = '".xtc_db_input($status)."', last_modified = now() where orders_id = '".$oID."'");

            $customer_notified = '0';
            $comments='';
            xtc_db_query("insert into ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".$oID."', '".xtc_db_input($status)."', now(), '".$customer_notified."', '".xtc_db_input($comments)."')");

            $order_updated = true;
        }

        return $order_updated;
    }

    function get_rakuten_tax_class($gambio_tax_class)
    {
        $tax_class_map = array(
            '1' => 0,       /** DE 0% **/
            '2' => 7,       /** DE 7% **/
            '3' => 10.7,    /** DE 10.7% **/
            '4' => 19,      /** DE 19% **/            
            '6' => 10,      /** AT 10% **/
            '7' => 12,      /** AT 12% **/
            '8' => 20,      /** AT 20% **/
        );

        $tax_class_default = '4';

        $percent = xtc_get_tax_rate($gambio_tax_class);
        $percent = round($percent, 2);

        if ($tax_class = array_search($percent, $tax_class_map)) {
            return (string)$tax_class;
        } else {
            return $tax_class_default;
        }
    }

    function get_status($rakuten_status)
    {
        $rakuten_status_map = array(
            'editable' => MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE,
            'shipped' => MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED,
            'cancelled' => MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED,
        );

        return $rakuten_status_map[trim($rakuten_status)];
    }


    


	function _logAPICall($call)
	{
		$line = date("d.m.Y H:i", time()) .' | '. $call;
		$logFilePP = DIR_FS_CATALOG . 'export/payment.rakuten_api_calls-'.substr(md5($this->API_UserName), 0, 4).'.log';
		error_log($line . "\n", 3, $logFilePP);
	}

	function _logTransactions($parameters)
	{
		$logFilePP = DIR_FS_CATALOG . 'export/payment.rakuten_ipn-'.substr(md5($this->API_UserName), 0, 4).'.log';
		$line = 'RAKUTEN TRANS|' . date("d.m.Y H:i", time()) . '|' . xtc_get_ip_address() . '|';
		foreach ($_POST as $key => $val)
		{
			$line .= $key . ':' . $val . '|';
		}
		error_log($line . "\n", 3, $logFilePP);
	}

	function _logTrans($data)
	{
		while (list ($key, $value) = each($data)) {
			$line .= $key . ':' . $val . '|';
		}
		xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_ADDRESS, '', EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, false, false, 'Rakuten ROPE Invalid Process', $line, $line);
	}
}