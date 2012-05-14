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

define('MODULE_PAYMENT_RAKUTEN_TEXT_TITLE', 'Kaufabwicklung &uuml;ber Rakuten Checkout');
define('MODULE_PAYMENT_RAKUTEN_TEXT_DESCRIPTION', 'Kaufabwicklung &uuml;ber Rakuten Checkout');
define('MODULE_PAYMENT_RAKUTEN_TEXT_INFO', '');
define('MODULE_PAYMENT_RAKUTEN_TEXT_SIGNUP', 'Jetzt informieren und anmelden');

define('MODULE_PAYMENT_RAKUTEN_STATUS_TITLE', 'Rakuten Checkout aktiv');
define('MODULE_PAYMENT_RAKUTEN_STATUS_DESC', 'Wollen Sie den Rakuten Checkout aktivieren?');

define('MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD_TITLE', 'Integrations-Varianten');
define('MODULE_PAYMENT_RAKUTEN_INTEGRATION_METHOD_DESC', 'Welche Integrations-Variante bevorzugen Sie: Standard- (&quot;Standard&quot;) oder Inline-Checkout (&quot;Inline&quot;)?');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES_TITLE', 'Versandkosten');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING_RATES_DESC', '(erforderlich)<br />CSV Format: Land, Preis, Lieferzeit.<br />Zum Beispiel:<br /><i>DE,7.99,3<br />AT,14.99</i>');
define('MODULE_PAYMENT_RAKUTEN_SANDBOX_TITLE', 'Sandbox-Mod&uacute;s');
define('MODULE_PAYMENT_RAKUTEN_SANDBOX_DESC', 'Wollen Sie den Sandbox-Modus verwenden?');
define('MODULE_PAYMENT_RAKUTEN_DEBUG_TITLE', 'Debug-Modus');
define('MODULE_PAYMENT_RAKUTEN_DEBUG_DESC', 'Wollen Sie Debugging aktivieren?');

define('MODULE_PAYMENT_RAKUTEN_PROJECT_ID_TITLE', 'Projekt ID');
define('MODULE_PAYMENT_RAKUTEN_PROJECT_ID_DESC', 'Um eine Projekt-ID zu erhalten m&uuml;ssen Sie sich zuerst anmelden');
define('MODULE_PAYMENT_RAKUTEN_API_KEY_TITLE', 'API');
define('MODULE_PAYMENT_RAKUTEN_API_KEY_DESC', 'Um einen API-Key zu erhalten m&uuml;ssen Sie sich zuerst anmelden');

define('MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE_TITLE', 'Rechnungsadressen einschr&auml;nken');
define('MODULE_PAYMENT_RAKUTEN_BILLING_ADDR_TYPE_DESC', 'W&auml;hlen Sie welche Art von Adressen Sie akzeptieren wollen');

define('MODULE_PAYMENT_RAKUTEN_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_RAKUTEN_ALLOWED_DESC', 'Zonen die f&uuml;r diese Modul erlaubt sind <b>individuell</b> eingeben. (e.g. DE, AT oder leer lassen um alle Zonen zu erlauben)');

define('MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE_TITLE', 'Order Editable Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_EDITABLE_DESC', 'Here you can match the editable status of orders in your system to the Rakuten editable status.');
define('MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED_TITLE', 'Shipping Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_SHIPPED_DESC', 'Here you can match the shipping status of orders in your system to the Rakuten shipping status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED_TITLE', 'Cancellation Status');
define('MODULE_PAYMENT_RAKUTEN_STATUS_CANCELLED_DESC', 'Here you can match the cancellation status of orders in your system to the Rakuten cancellation status.');

define('MODULE_PAYMENT_RAKUTEN_SORT_ORDER_TITLE', 'Anzeigen Sortieren Zuordnen');
define('MODULE_PAYMENT_RAKUTEN_SORT_ORDER_DESC', 'Der niedrigste Wert wird zuerst angezeigt');

define('MODULE_PAYMENT_RAKUTEN_SUBTOTAL', 'Zwischensumme');
define('MODULE_PAYMENT_RAKUTEN_SHIPPING', 'Versand');
define('MODULE_PAYMENT_RAKUTEN_TAX', 'MwSt.');
define('MODULE_PAYMENT_RAKUTEN_TOTAL', '<b>Summe</b>');