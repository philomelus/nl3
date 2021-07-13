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

	require_once 'inc/menubase.inc.php';
	
	define('IDM_ADMIN', 1);
	define('IDM_BILLING', IDM_ADMIN + 1);
	define('IDM_CUSTOMERS', IDM_BILLING + 1);
	define('IDM_HOME', IDM_CUSTOMERS + 1);
	define('IDM_LOGOUT', IDM_HOME + 1);
	define('IDM_PROFILE', IDM_LOGOUT + 1);
	define('IDM_STORES', IDM_PROFILE + 1);
	define('IDM_ROUTES', IDM_STORES + 1);
	
	class MainMenu extends _MenuBase
	{
		private static $MENUS = array
			(
				IDM_HOME => array
					(
						MI_PAGE => S_HOME,
						MI_NAME => 'Home',
						MI_URL => 'index.php',
						MI_TIP => 'NewsLedger Home Page',
					),
				IDM_CUSTOMERS => array
					(
						MI_PAGE => S_CUSTOMERS,
						MI_NAME => 'Customers',
						MI_URL => 'customers.php',
						MI_TIP => 'Customer maintenance',
					),
				IDM_STORES => array
					(
						MI_PAGE => S_STORES,
						MI_NAME => 'Stores &amp; Racks',
						MI_URL => 'stores.php',
						MI_TIP => 'Stores and Racks mainenance',
                        MI_SCRIPTS => array('js/stores.js',
                                            'js/stores2.js.php'),
                        MI_STYLES => array(),
					),
				IDM_ROUTES => array
					(
						MI_PAGE => S_ROUTES,
						MI_NAME => 'Routes',
						MI_URL => 'routes.php',
						MI_TIP => 'Route maintenance',
					),
				IDM_PROFILE => array
					(
						MI_PAGE => S_PROFILE,
						MI_NAME => 'Profile',
						MI_URL => 'profile.php',
						MI_TIP => 'Change user specific settings',
						MI_SCRIPTS => array('js/popups/profile.js.php',
											'js/Profile.js'),
                        MI_STYLES => array(),
					),
				IDM_ADMIN => array
					(
						MI_PAGE => S_ADMIN,
						MI_NAME => 'Administration',
						MI_URL => 'admin.php',
						MI_TIP => 'NewsLedger Administration',
					),
				IDM_LOGOUT => array
					(
						MI_NAME => 'Logout',
						MI_URL => 'logout.php',
						MI_TIP => 'Log out of NewsLedger'
					)
			);
		
		public function __construct()
		{
			parent::__construct(MainMenu::$MENUS, 1);
		}
	}

	$MENUS = new MainMenu();

	// Make sure the user doesn't get to a page they aren't allowed
	if (!allowed('page', PAGE))
	{
		$item = $MENUS->begin();
		if (isset($item[key($item)][MI_URL]))
		{
			header('Location: ' . $item[key($item)][MI_URL]);
			exit;
		}
		
		// No place for user to go, so just let 'em know and bail
		echo 'Contact the administrator of this site:  You have a valid login '
				. 'and password, but are not allowed to access any of the pages.';
		exit;
	}

?>
