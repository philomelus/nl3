<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

	define('IDSSM_ADDNEW', 1);
	define('IDSSM_LOOKUP', 2);
	
	class PaymentsMenu extends _MenuBase
	{
		private static $MENUS = array
			(
				IDSSM_LOOKUP => array
					(
						MI_PAGE => SCP_LOOKUP,
						MI_NAME => 'Search',
						MI_TIP => 'Lookup Payment(s)',
						MI_URL => 'customers.php?menu=7&submenu=2',				// IDSSM_LOOKUP
						MI_CODE => 'customers/payments/lookup.php',
						MI_SCRIPTS => array('js/popups/customers.js.php',
											'js/popups/customers/payments.js.php'),
                        MI_STYLES => array(),
					)
			);
		
		public function __construct()
		{
			global $SUBMENUS;
			
			parent::__construct(PaymentsMenu::$MENUS, 3);
			self::super($SUBMENUS, IDSM_PAYMENTS);
		}
	
		protected function error_begin()
		{
			return false;
		}
		
		public function title($active)
		{
			return 'Customers / Payments / ' . $this->_menus[$active][MI_NAME];
		}
	
		public function title_linked($active)
		{
			return '<a href="customers.php">Customers</a> / <a href="customers.php?menu=7">Payments</a> / ' . $this->_menus[$active][MI_NAME];
		}
	}
	
	$SUBSUBMENUS = new PaymentsMenu();
	if (!isset($_REQUEST['submenu']))
	{
		$item = $SUBSUBMENUS->begin();
		$_REQUEST['submenu'] = key($item);
	}
	$SUBSUBMENUS->display_page($_REQUEST['submenu']);
	
?>
