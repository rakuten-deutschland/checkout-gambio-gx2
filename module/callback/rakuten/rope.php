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
include ('../../includes/application_top_callback.php');

chdir(DIR_FS_DOCUMENT_ROOT);

require_once(DIR_FS_CATALOG.'gm/classes/FileLog.php');
require_once(DIR_FS_CATALOG.'gm/classes/ErrorHandler.php');
require_once(DIR_FS_CATALOG.'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG.'system/gngp_layer_init.inc.php');

/**
 * Include the list of project filenames
 */
require_once (DIR_WS_INCLUDES.'filenames.php');

/**
 * Include the list of project database tables
 */
require_once (DIR_WS_INCLUDES.'database_tables.php');

/**
 * Include Database files
 */
require_once (DIR_FS_INC.'xtc_db_connect.inc.php');
require_once (DIR_FS_INC.'xtc_db_close.inc.php');
require_once (DIR_FS_INC.'xtc_db_error.inc.php');
require_once (DIR_FS_INC.'xtc_db_perform.inc.php');
require_once (DIR_FS_INC.'xtc_db_query.inc.php');
require_once (DIR_FS_INC.'xtc_db_queryCached.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_array.inc.php');
require_once (DIR_FS_INC.'xtc_db_num_rows.inc.php');
require_once (DIR_FS_INC.'xtc_db_data_seek.inc.php');
require_once (DIR_FS_INC.'xtc_db_insert_id.inc.php');
require_once (DIR_FS_INC.'xtc_db_free_result.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_fields.inc.php');
require_once (DIR_FS_INC.'xtc_db_output.inc.php');
require_once (DIR_FS_INC.'xtc_db_input.inc.php');
require_once (DIR_FS_INC.'xtc_db_prepare_input.inc.php');
require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');
require_once (DIR_FS_INC.'xtc_hide_session_id.inc.php');

/**
 * HTML basic helpers
 */
require_once (DIR_FS_INC.'xtc_href_link.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');

require_once (DIR_WS_CLASSES.'class.phpmailer.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

require_once (DIR_FS_INC.'xtc_product_link.inc.php');
require_once (DIR_FS_INC.'xtc_category_link.inc.php');
require_once (DIR_FS_INC.'xtc_manufacturer_link.inc.php');

/**
 * HTML Functions
 */
require_once (DIR_FS_INC.'xtc_draw_checkbox_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_form.inc.php');
require_once (DIR_FS_INC.'xtc_draw_hidden_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_input_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC.'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_selection_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');
require_once (DIR_FS_INC.'xtc_draw_textarea_field.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');

require_once (DIR_FS_INC.'xtc_not_null.inc.php');
require_once (DIR_FS_INC.'xtc_update_whos_online.inc.php');
require_once (DIR_FS_INC.'xtc_activate_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_specials.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
require_once (DIR_FS_INC.'xtc_redirect.inc.php');
require_once (DIR_FS_INC.'xtc_get_uprid.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_has_product_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_image.inc.php');
require_once (DIR_FS_INC.'xtc_check_stock_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_currency_exists.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_ip_address.inc.php');
require_once (DIR_FS_INC.'xtc_setcookie.inc.php');
require_once (DIR_FS_INC.'xtc_check_agent.inc.php');
require_once (DIR_FS_INC.'xtc_count_cart.inc.php');
require_once (DIR_FS_INC.'xtc_get_qty.inc.php');
require_once (DIR_FS_INC.'create_coupon_code.inc.php');
require_once (DIR_FS_INC.'xtc_gv_account_update.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate_from_desc.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'xtc_cleanName.inc.php');
require_once (DIR_FS_INC.'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC.'xtc_input_validation.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');
require_once (DIR_FS_INC.'xtc_get_products_name.inc.php');

require_once (DIR_FS_CATALOG . 'gm/modules/gm_gprint_application_top.php');
require_once (DIR_FS_CATALOG . 'gm/classes/GMCounter.php');
require_once (DIR_FS_CATALOG . 'gm/classes/GMLightboxControl.php');
require_once (DIR_FS_CATALOG . 'admin/gm/classes/GMOpenSearch.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_clear_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_prepare_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_content_by_group_id.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_categories_icon.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_mega_flyover_prepare.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_is_valid_trusted_shop_id.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_convert_qty.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_create_corner.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_privacy_link.inc.php');

if(is_dir(DIR_FS_CATALOG.'StyleEdit/'))
{
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMSESecurity.php');
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMCSSManager.php');
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMBoxesMaster.php');
}

require_once (DIR_FS_INC.'xtc_Security.inc.php');

require_once (DIR_WS_CLASSES.'class.inputfilter.php');

MainFactory::create_object('FilterManager');

global $gmLangFileMaster;
$gmLangFileMaster = MainFactory::create_object('GMLangFileMaster');

global $gmSEOBoost;
$gmSEOBoost = MainFactory::create_object('GMSEOBoost');

function xtDBquery($query) {
	if (DB_CACHE == 'true') {
		$result = xtc_db_queryCached($query);
	} else {
		$result = xtc_db_query($query);
	}
	return $result;
}

/**
 * Shopping cart class
 */
require_once (DIR_WS_CLASSES.'shopping_cart.php');
require_once (DIR_WS_CLASSES.'wish_list.php');

/**
 * Navigation History class
 */
require_once (DIR_WS_CLASSES.'navigation_history.php');

/**
 * Data for backward compatibility
 */
require_once (DIR_WS_FUNCTIONS.'compatibility.php');

require_once (DIR_WS_CLASSES . 'order.php');

/**
 * The Rakuten Checkout class
 */
require_once ('user_classes/rakuten_checkout.php');
$rakutenCheckout = new rakuten_checkout();

$request = file_get_contents('php://input');

if (strlen(trim($request)) <= 0) {
    echo $rakutenCheckout->prepare_response(false);
    die;
}

try {
    /**
     * Process Rope and output result
     */
    echo $rakutenCheckout->process_rope_request($request);
} catch (Exception $e) {}