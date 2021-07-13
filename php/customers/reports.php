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

	define('IDSSM_BEHIND', 1);
	define('IDSSM_ORDERS', 2);
	define('IDSSM_CUSTOMER', 3);
	define('IDSSM_AHEAD', 4);
	define('IDSSM_STOPPED', 5);
	
	class CustomerReportsMenu extends _MenuBase
	{
		private static $MENUS = array
			(
				IDSSM_AHEAD => array
					(
						MI_PAGE => SCR_AHEAD,
						MI_NAME => 'Ahead',
						MI_TIP => 'Show customers that are ahead in payment(s)',
						MI_URL => 'customers.php?menu=8&submenu=4',				// IDSSM_AHEAD
						MI_CODE => 'customers/reports/ahead.php',
						MI_SCRIPTS => array(),
						MI_STYLES => array(),
					),
				IDSSM_BEHIND => array
					(
						MI_PAGE => SCR_BEHIND,
						MI_NAME => 'Behind',
						MI_TIP => 'Show customers that are behind in payment(s)',
						MI_URL => 'customers.php?menu=8&submenu=1',				// IDSSM_BEHIND
						MI_CODE => 'customers/reports/behind.php',
						MI_SCRIPTS => array(),
                        MI_STYLES => array(),
					),
				IDSSM_STOPPED => array
					(
						MI_PAGE => SCR_STOPPED,
						MI_NAME => 'Inactive',
						MI_TIP => 'Show customers that are stopped and part of route list',
						MI_URL => 'customers.php?menu=8&submenu=5',				// IDSSM_STOPPED
						MI_CODE => 'customers/reports/stopped.php',
						MI_SCRIPTS => array(),
                        MI_STYLES => array(),
					),
				IDSSM_ORDERS => array
					(
						MI_PAGE => SCR_ORDERS,
						MI_NAME => 'Orders',
						MI_TIP => 'Show new customers for a specific week',
						MI_URL => 'customers.php?menu=8&submenu=2',				// IDSSM_ORDERS
						MI_CODE => 'customers/reports/orders.php',
						MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
					)
			);
		
		public function __construct()
		{
			global $SUBMENUS;
			
			parent::__construct(CustomerReportsMenu::$MENUS, 3);
			self::super($SUBMENUS, IDSM_REPORTS);
		}
	
		protected function display_begin()
		{
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
		}
		
		protected function display_end()
		{
			global $resultHtml;
			
			echo '<input type="hidden" name="menu" value="' . $_REQUEST['menu'] . '" />'
					. '<input type="hidden" name="submenu" value="' . $_REQUEST['submenu'] . '" />'
					. '</form>'
					. '</div>'
					. $resultHtml;
		}
		
		protected function header_begin()
		{
			echo '<div>';
		}

		public function title($active)
		{
			return 'Customers / Reports / ' . $this->_menus[$active][MI_NAME];
		}

		public function title_linked($active)
		{
			return '<a href="customers.php">Customers</a> / <a href="customers.php?menu=8">Reports</a> / ' . $this->_menus[$active][MI_NAME];
		}
	}
	
	$SUBSUBMENUS = new CustomerReportsMenu();
	if (!isset($_REQUEST['submenu']))
	{
		$item = $SUBSUBMENUS->begin();
		$_REQUEST['submenu'] = key($item);
	}
	$SUBSUBMENUS->display_page($_REQUEST['submenu']);
	
?>
