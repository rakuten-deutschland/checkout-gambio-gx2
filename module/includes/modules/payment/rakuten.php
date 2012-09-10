<?php
/**
 * Copyright (c) 2012, Rakuten Deutschland GmbH. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Rakuten Deutschland GmbH nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL RAKUTEN DEUTSCHLAND GMBH BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class rakuten {
	var $code, $version, $title, $description, $enabled;

	function rakuten() {
		global $order;
		$this->code = 'rakuten';
        $this->version = '1.0.6';
		$this->title = '<img src="../rakuten/images/logo_payment_methods.png" border="0" alt="' . MODULE_PAYMENT_RAKUTEN_TEXT_TITLE . '">'
                       . ' ver. ' . $this->version;

		$this->description = '
			<a href="http://checkout.rakuten.de/" target="_blank"><img src="../rakuten/images/payment_banner_small.png" border="0" alt="' . MODULE_PAYMENT_RAKUTEN_TEXT_TITLE . '"></a><br /><br />
			<a href="http://checkout.rakuten.de/" target="_blank"><u>' . MODULE_PAYMENT_RAKUTEN_TEXT_SIGNUP . '</u></a><br /><br />
			' . MODULE_PAYMENT_RAKUTEN_TEXT_DESCRIPTION . '<br />'
          . ($_SESSION['rakuten_install_msg'] ? '<font color="red">' . $_SESSION['rakuten_install_msg'] . '</font>' : '' );

		$this->sort_order = MODULE_PAYMENT_RAKUTEN_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_RAKUTEN_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_RAKUTEN_TEXT_INFO;
	}

	function javascript_validation() {
		return false;
	}

	function selection() {
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		return false;
	}

	function before_process() {
		return false;
	}

	function admin_order($oID) {
		return false; 
	}

	function output_error() {
		return false;
	}

	function check()
    {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_RAKUTEN_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
    {
        $errors = array();

        if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/')) {
            $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/';
        }

        if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php')) {
            $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php';
        }

        if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php')) {
            $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php';
        }

        if (is_dir(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/')) {
            if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html')) {
                $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html';
            }
        }

        if (is_dir(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/')) {
            if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html')) {
                $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html';
            }
        }

        if (is_dir(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/')) {
            if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html')) {
                $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html';
            }
        }

        if (is_dir(DIR_FS_DOCUMENT_ROOT . 'templates/gambio/')) {
            if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html')) {
                $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html';
            }
        }

        if (!is_writable(DIR_FS_DOCUMENT_ROOT . 'admin/orders.php')) {
            $errors[] = 'Make writable ' . DIR_FS_DOCUMENT_ROOT . 'admin/orders.php';
        }

        if ( count($errors) > 0 ) {
            $_SESSION['rakuten_install_msg'] = implode('<br /><br />', $errors);
            return false;
        } else {
            $_SESSION['rakuten_install_msg'] = '';
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/payment.php')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/payment.php');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/payment.php', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/classes/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/includes/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/ContentView.inc.php')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/ContentView.inc.php');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/ContentView.inc.php', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/views/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/system/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown.html')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown.html');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown.html', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown_fixed.html')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown_fixed.html');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/box_cart_dropdown_fixed.html', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/boxes/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/shopping_cart.html')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/shopping_cart.html');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/shopping_cart.html', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/module/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/EyeCandy/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/shopping_cart.html')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/shopping_cart.html');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/shopping_cart.html', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/module/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/gambio/', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/templates/', 0777);
        }

        if (!file_exists(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/orders.php')) {
            if (!is_dir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/')) {
                mkdir(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/', 0777, true);
            }
            copy(DIR_FS_DOCUMENT_ROOT . 'admin/orders.php', DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/orders.php');
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/orders.php', 0777);
            chmod(DIR_FS_DOCUMENT_ROOT . 'rakuten/bak/admin/', 0777);
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php');
        if (!strpos($content, 'BOF RAKUTEN')) {
            if (strpos($content, 'BOF TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
            } else {
                $str = '$t_gm_module_payment_installed = str_replace(\'paypalexpress.php\', \'\', MODULE_PAYMENT_INSTALLED);';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php';
                } else {
                    $strput = '
                    // BOF RAKUTEN
                    $t_gm_module_payment_installed = str_replace(\'rakuten.php\', \'\', $t_gm_module_payment_installed);
                    $t_gm_module_payment_installed = str_replace(\';;\', \';\', $t_gm_module_payment_installed);
                    // EOF RAKUTEN
                    ';

                    $content = str_replace($str, $str.$strput, $content);
                }
            }
            file_put_contents(DIR_FS_DOCUMENT_ROOT . 'includes/classes/payment.php', $content);
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php');
        if (!strpos($content, 'BOF RAKUTEN')) {
            if (strpos($content, 'BOF TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
                file_put_contents(DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php', $content);
            } else {
                $str = 'function build_html(';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php';
                } else {
                    $strpos = strpos($content, '{' , $strpos);
                    $str1 = substr($content, 0, $strpos+1);
                    $str2 = substr($content, $strpos+1);

                    $strput = '
                    // BOF RAKUTEN
                    if (defined(\'MODULE_PAYMENT_RAKUTEN_STATUS\') && MODULE_PAYMENT_RAKUTEN_STATUS == \'True\') {
                        /** @var $rakuten_checkout rakuten_checkout */
                        $rakuten_checkout = MainFactory::create_object(\'rakuten_checkout\');
                        $this->set_content_data(\'BUTTON_RAKUTEN\', $rakuten_checkout->build_rakuten_checkout_button());
                        $this->set_content_data(\'ERROR_RAKUTEN\', $rakuten_checkout->build_rakuten_checkout_error());
                    }
                    // EOF RAKUTEN
                    ';

                    file_put_contents(DIR_FS_DOCUMENT_ROOT . 'system/views/ContentView.inc.php', $str1.$strput.$str2);
                }
            }
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html');
        if (!strpos($content, 'BUTTON_RAKUTEN')) {
            if (strpos($content, 'BOF TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
                file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html', $content);
            } else {
                $str = '$button.checkout';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html';
                } else {
                    $strpos = strpos($content, "\n", $strpos);
                    $str1 = substr($content, 0, $strpos+1);
                    $str2 = substr($content, $strpos+1);

                    $strput = '
                    {if $BUTTON_RAKUTEN}<div style="float: right;  margin: 10px 20px 10px 0;">{$BUTTON_RAKUTEN}</div>{/if}
                    ';

                    file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown.html', $str1.$strput.$str2);
                }
            }
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html');
        if (!strpos($content, 'BUTTON_RAKUTEN')) {
            if (strpos($content, 'BUTTON_TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
                file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html', $content);
            } else {
                $str = '$button.checkout';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html';
                } else {
                    $strpos = strpos($content, "\n", $strpos);
                    $str1 = substr($content, 0, $strpos+1);
                    $str2 = substr($content, $strpos+1);

                    $strput = '
                    {if $BUTTON_RAKUTEN}<div style="float: right;  margin: 10px 20px 10px 0;">{$BUTTON_RAKUTEN}</div>{/if}
                    ';

                    file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/boxes/box_cart_dropdown_fixed.html', $str1.$strput.$str2);
                }
            }
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html');
        if (!strpos($content, 'BUTTON_RAKUTEN')) {
            if (strpos($content, 'BUTTON_TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
            } else {
                $str = '<div class="checkout_button">';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html';
                } else {
                    $strpos = strpos($content, '</div>', $strpos);
                    $str1 = substr($content, 0, $strpos);
                    $str2 = substr($content, $strpos);
                    $strput = ' {if $BUTTON_RAKUTEN}<br /><br />{$BUTTON_RAKUTEN}{/if} {if $ERROR_RAKUTEN}{$ERROR_RAKUTEN}{/if} ';
                    $content = $str1.$strput.$str2;

                    $strpos = strpos($content, '<div class="checkout_button">', $strpos+1);
                    $str1 = substr($content, 0, $strpos+29);
                    $str2 = substr($content, $strpos+29);
                    $strput = ' {if $BUTTON_RAKUTEN}{$BUTTON_RAKUTEN}<br /><br />{/if} ';
                    $content = $str1.$strput.$str2;
                }
            }
            file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/EyeCandy/module/shopping_cart.html', $content);
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html');
        if (!strpos($content, 'BUTTON_RAKUTEN')) {
            if (strpos($content, 'BUTTON_TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
            } else {
                $str = '{$BUTTON_RELOAD}';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html';
                } else {
                    $strput = ' {if $BUTTON_RAKUTEN}<br /><br />{$BUTTON_RAKUTEN}<br/>{/if}{if $ERROR_RAKUTEN}{$ERROR_RAKUTEN}<br />{/if} ';
                    $content = str_replace($str, $str.$strput, $content);
                }
            }
            file_put_contents(DIR_FS_DOCUMENT_ROOT . 'templates/gambio/module/shopping_cart.html', $content);
        }

        $content = file_get_contents(DIR_FS_DOCUMENT_ROOT . 'admin/orders.php');
        if (!strpos($content, 'BOF RAKUTEN')) {
            if (strpos($content, 'BOF TRADORIA')) {
                $content = str_replace('TRADORIA', 'RAKUTEN', $content);
                $content = str_replace('tradoria', 'rakuten', $content);
            } else {
                $str = '$order_updated = true;';
                $strpos = strpos($content, $str);
                if (!$strpos) {
                    $errors[] = 'Cannot patch ' . DIR_FS_DOCUMENT_ROOT . 'admin/orders.php';
                } else {
                    $strput = '

                    // BOF RAKUTEN
                    require_once (\'../user_classes/rakuten_checkout.php\');
                    $rakutenCheckout = new rakuten_checkout();
                    $rakutenCheckout->send_order_shipment(xtc_db_input($oID), $check_status[\'orders_status\']);
                    // EOF RAKUTEN';

                    $content = str_replace($str, $str.$strput, $content);
                }
            }
            file_put_contents(DIR_FS_DOCUMENT_ROOT . 'admin/orders.php', $content);
        }

        if (is_file(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/tradoria.php')) {
            @unlink(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/tradoria.php');
        }
        if (is_file(DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/tradoria.php')) {
            $errors[] = 'Please delete ' . DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/tradoria.php';
        }

        if ( count($errors) > 0 ) {
            $_SESSION['rakuten_install_msg'] = implode('<br /><br />', $errors);
            return false;
        } else {
            $_SESSION['rakuten_install_msg'] = '';
        }

        if (xtc_db_num_rows(xtc_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='MODULE_PAYMENT_TRADORIA_STATUS'")) > 0) {
            foreach ($this->keys() as $key) {
                xtc_db_query("update ".TABLE_CONFIGURATION." set configuration_key='".$key."' where configuration_key='".str_replace('RAKUTEN','TRADORIA',$key)."'");
            }
        } else {
            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_STATUS', 'True', '6', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD', 'Standard', '6', '10', 'xtc_cfg_select_option(array(\'Standard\', \'Inline\'), ', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES', '', '6', '15', 'xtc_cfg_nc_textarea(\'configuration[MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES]\',', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_SANDBOX', 'Yes', '6', '20', 'xtc_cfg_select_option(array(\'Yes\', \'No\'), ', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_DEBUG', 'Yes', '6', '25', 'xtc_cfg_select_option(array(\'Yes\', \'No\'), ', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_PROJECT_ID', '', '6', '30', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_API_KEY', '', '6', '35', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE', 'All Addresses', '6', '40', 'xtc_cfg_select_option(array(\'All Addresses\', \'Business Addresses Only\', \'Private Addresses Only\'), ', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_ALLOWED', '', '6', '45', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE', '0',  '6', '50', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED', '0',  '6', '55', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED', '0',  '6', '60', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

            xtc_db_query("insert into ".TABLE_CONFIGURATION."
                (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values
                ('MODULE_PAYMENT_RAKUTEN_SORT_ORDER', '0', '65', '0', now())");
        }

        $fields = mysql_list_fields(DB_DATABASE, TABLE_ORDERS);
        $columns = mysql_num_fields($fields);
        for ($i = 0; $i < $columns; $i++) {
            $field_array[] = mysql_field_name($fields, $i);
        }
        if (!in_array('rakuten_order_no', $field_array)) {
            if (in_array('tradoria_order_no', $field_array)) {
                xtc_db_query("alter table ".TABLE_ORDERS." drop index tradoria_order_no");
                xtc_db_query("alter table ".TABLE_ORDERS." change column tradoria_order_no rakuten_order_no varchar(255)");
            } else {
                xtc_db_query("alter table ".TABLE_ORDERS." add column rakuten_order_no varchar(255)");
            }
            xtc_db_query("alter table ".TABLE_ORDERS." add index rakuten_order_no (rakuten_order_no)");
        }
    }

	function remove()
    {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys()
    {
		return array (
            'MODULE_PAYMENT_RAKUTEN_STATUS',

            'MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD',
            'MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES',
            'MODULE_PAYMENT_RAKUTEN_SANDBOX',
            'MODULE_PAYMENT_RAKUTEN_DEBUG',

            'MODULE_PAYMENT_RAKUTEN_PROJECT_ID',
            'MODULE_PAYMENT_RAKUTEN_API_KEY',

            'MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE',

            'MODULE_PAYMENT_RAKUTEN_ALLOWED',

            'MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE',
            'MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED',
            'MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED',

            'MODULE_PAYMENT_RAKUTEN_SORT_ORDER',
        );
	}
}
?>