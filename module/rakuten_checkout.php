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

include ('includes/application_top.php');

/** 
 * @var $rakuten_checkout rakuten_checkout 
 **/
$rakuten_checkout = MainFactory::create_object('rakuten_checkout');

/** 
 * minimum/maximum order value
 */
if ($_SESSION['cart']->show_total() > 0 ) {
 if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
 }
 if  ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
  if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  }
 }
}

/**
 * Check if checkout is allowed
 */
if ($_SESSION['allow_checkout'] == 'false')
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));


if (MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD == 'Standard') {

    $redirect_url = $rakuten_checkout->get_redirect_url();
    xtc_redirect($redirect_url);

} else { 
	
	 /**
	  * Inline
	  */ 
    $smarty = new Smarty;

    require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
    require (DIR_WS_INCLUDES.'header.php');

    $inline_code = $rakuten_checkout->get_redirect_url(true);
    $smarty->assign('main_content', '<br />' . $inline_code);

    $smarty->assign('language', $_SESSION['language']);
    $smarty->caching = 0;
    if (!defined(RM))
        $smarty->load_filter('output', 'note');
    $smarty->display(CURRENT_TEMPLATE.'/index.html');
    include ('includes/application_bottom.php');
}