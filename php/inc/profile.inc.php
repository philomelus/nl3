<?php
/*
	Copyright 2005, 2006, 2007, 2008 Russell E. Gibson

    This file is part of NewsLedger.

    NewsLedger is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    NewsLedger is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with NewsLedger; see the file LICENSE.  If not, see
	<http://www.gnu.org/licenses/>.
*/

	//-------------------------------------------------------------------------
	// key1

	define('P1_BLANK', '');

	define('P1_ADJUSTMENT', 'adjustment');
	define('P1_BILL', 'bill');
	define('P1_BILLING', 'billing');
	define('P1_CHANGE', 'change');
	define('P1_CONFIG', 'config');
	define('P1_CUSTOMERS', 'customers');
	define('P1_DEBUG', 'debug');
	define('P1_DEFAULT', 'default');
	define('P1_HELP', 'help');
	define('P1_PAYMENT', 'payment');
	define('P1_POPUP', 'popup');
	define('P1_STORES', 'stores');
	define('P1_WATCH', 'watch');
	
	//-------------------------------------------------------------------------
	// key2

	define('P2_BLANK', '');

	define('P2_ADD', 'add');
	define('P2_ADDRESS', 'address');
	define('P2_ADJUSTMENT', 'adjustment');
	define('P2_CHANGETYPE', 'changetype');
	define('P2_CITY', 'city');
	define('P2_COMPLAINT', 'complaint');
	define('P2_CUSTOMER', 'customer');
	define('P2_DATA', 'data');
	define('P2_EDIT', 'edit');
	define('P2_FONT', 'font');
	define('P2_HELP', 'help');
	define('P2_LIST', 'list');
	define('P2_LOOKUP', 'lookup');
	define('P2_NAME', 'name');
	define('P2_PAYMENT', 'payment');
	define('P2_PAYMENTS', 'payments');
	define('P2_PERIOD', 'period');
	define('P2_STARTSTOP', 'startstop');
	define('P2_STATE', 'state');
	define('P2_STOPSTART', 'stopstart');
	define('P2_TITLE', 'title');
	define('P2_TYPE', 'type');
	define('P2_VIEW', 'view');
	define('P2_WINDOW', 'window');
	define('P2_ZIP', 'zip');

	//-------------------------------------------------------------------------
	// key3

	define('P3_BLANK', '');

	define('P3_ADD', 'add');
	define('P3_ADJUSTMENTS', 'adjustment');
	define('P3_BACKGROUND', 'bkgrnd');
	define('P3_BILLS', 'bills');
	define('P3_CHANGES', 'changes');
	define('P3_CID', 'cid');
	define('P3_COLOR', 'color');
	define('P3_COLOR1', 'color1');
	define('P3_COLOR2', 'color2');
	define('P3_HELP', 'help');
	define('P3_LIMIT', 'limit');
	define('P3_LOOKUP', 'lookup');
	define('P3_MESSAGE', 'message');
	define('P3_NAMES', 'names');
	define('P3_PAYMENTS', 'payments');
	define('P3_POPUP', 'popup');
	define('P3_RID', 'rid');
	define('P3_TID', 'tid');
	define('P3_WINDOW', 'window');
	
	//-------------------------------------------------------------------------
	// key4

	define('P4_BLANK', '');

	define('P4_COLOR', 'color');
	define('P4_COUNT', 'count');
	define('P4_HEIGHT', 'height');
	define('P4_HELP', 'help');
	define('P4_MENUBAR', 'menubar');
	define('P4_RESIZABLE', 'resizable');
	define('P4_SCROLLBARS', 'scrollbars');
	define('P4_STATUS', 'status');
	define('P4_TOOLBAR', 'toolbar');
	define('P4_WIDTH', 'width');
	
	//-------------------------------------------------------------------------
	
	function profile_title($k1, $k2 = '', $k3 = '', $k4 = '')
	{
		return htmlspecialchars($k1 . (empty($k2) ? '' :
			'-' . $k2 . (empty($k3) ? '' :
				'-' . $k3 . (empty($k4) ? '' : '-' . $k4))));
	}
	
?>
