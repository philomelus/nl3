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

	require_once 'inc/security.inc.php';

	define('PAGE', S_CUSTOMERS);

	require_once 'inc/login.inc.php';
	require_once 'inc/menu.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';

	define('IDSM_ADMIN',		1);
	define('IDSM_ADDNEW',		2);
	define('IDSM_BILLING',		4);
	define('IDSM_FLAGSTOPS',	5);
	define('IDSM_SEARCH',		6);
	define('IDSM_PAYMENTS',		7);
	define('IDSM_REPORTS',		8);
    define('IDSM_COMBINED',     9);

	class CustomersMenu extends _MenuBase
	{
		private static $MENUS = array
			(
				IDSM_REPORTS => array
					(
						MI_PAGE => SC_REPORTS,
						MI_NAME => 'Reports',
						MI_TIP => 'Customer reports',
						MI_URL => 'customers.php?menu=8',						// IDSM_REPORTS
						MI_CODE => 'customers/reports.php'
					),
				IDSM_BILLING => array
					(
						MI_PAGE => SC_BILLING,
						MI_NAME => 'Billing',
						MI_TIP => 'Handle Customer Billing',
						MI_URL => 'customers.php?menu=4',						// IDSM_BILLING
						MI_CODE => 'customers/billing.php'
					),
				IDSM_ADMIN => array
					(
						MI_PAGE => SC_ADMIN,
						MI_NAME => 'Administration',
						MI_TIP => 'Customer Administration',
						MI_URL => 'customers.php?menu=1',
						MI_CODE => 'customers/admin.php'
					)
			);
		
		public function __construct()
		{
			global $MENUS;
			
			parent::__construct(CustomersMenu::$MENUS, 2);
			parent::super($MENUS, IDM_CUSTOMERS);
		}

		public function display_page($active, $styles='', $script='')
		{
			switch ($active)
			{
			case IDSM_ADMIN:
				self::load(IDSM_ADMIN);
				break;
			
			case IDSM_BILLING:
				self::load(IDSM_BILLING);
				break;
			
			case IDSM_PAYMENTS:
				self::load(IDSM_PAYMENTS);
				break;
				
			case IDSM_REPORTS:
				self::load(IDSM_REPORTS);
				break;
				
			default:
				parent::display_page($active, $styles, $script);
				break;
			}
		}
		
		protected function error_begin()
		{
			return false;
		}
		
		public function title($active)
		{
			return 'Customers / ' . $this->_menus[$active][MI_NAME];
		}
		
		public function title_linked($active)
		{
			return '<a href="customers.php">Customers</a> / ' . $this->_menus[$active][MI_NAME];
		}
	}

//-----------------------------------------------------------------------------

	$SUBMENUS = new CustomersMenu();
	if (!isset($_REQUEST['menu']))
	{
		$item = $SUBMENUS->begin();
		$_REQUEST['menu'] = key($item);
	}
	$SUBMENUS->display_page($_REQUEST['menu']);
?>
