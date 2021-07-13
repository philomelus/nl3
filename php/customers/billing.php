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

	define('IDSSM_BILL', 1);
	define('IDSSM_LOG', 2);
	
	class CustomerBillingMenu extends _MenuBase
	{
		private static $MENUS = array
			(
				IDSSM_BILL => array
					(
						MI_PAGE => SCB_BILL,
						MI_NAME => 'Bill',
						MI_TIP => 'Generate Bill(s)',
						MI_URL => 'customers.php?menu=4&submenu=1',				// IDSSM_BILL
						MI_CODE => 'customers/billing/bill.php',
						MI_SCRIPTS => array('js/popups/customers.js.php',
											'js/printf.js',
											'customers/billing/bill/js/step4.js'),
                        MI_STYLES => array(),
					),
				IDSSM_LOG => array
					(
						MI_PAGE => SCB_LOG,
						MI_NAME => 'Log',
						MI_TIP => 'Examine Billing Log',
						MI_URL => 'customers.php?menu=4&submenu=2',				// IDSSM_LOG
						MI_CODE => 'customers/billing/log.php',
                        MI_SCRIPTS => array('js/popups/customers.js.php',
                                            'js/popups/periods.js.php'),
                        MI_STYLES => array(),
					)
			);
		
		public function __construct()
		{
			global $SUBMENUS;
			
			parent::__construct(CustomerBillingMenu::$MENUS, 3);
			self::super($SUBMENUS, IDSM_BILLING);
		}
		
		public function title($active, $links = false)
		{
			if ($links)
				return '<a href="customers.php">Customers</a> / <a href="customers.php?menu=4">Billing</a> / ' . $this->_menus[$active][MI_NAME];
			else
				return 'Customers / Billing / ' . $this->_menus[$active][MI_NAME];
		}
	
		public function title_linked($active)
		{
			// BUGBUG: Deprecated
			return $this->title($active, true);
		}
	}

	global $SUBSUBMENUS;
	$SUBSUBMENUS = new CustomerBillingMenu();
	if (!isset($_REQUEST['submenu']))
	{
		$item = $SUBSUBMENUS->begin();
		$_REQUEST['submenu'] = key($item);
	}
	$SUBSUBMENUS->display_page($_REQUEST['submenu']);
	
?>
