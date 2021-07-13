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
	
	define('PAGE', S_ROUTES);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/menu.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/popups/routes.inc.php';
	
	define('IDM2_ADMIN',		1);
	define('IDM2_REPORTS',		IDM2_ADMIN + 1);
	define('IDM2_SEQUENCING',	IDM2_REPORTS + 1);
    define('IDM2_CHANGES',      IDM2_SEQUENCING + 1);

	class RoutesMenu extends _MenuBase
	{
		private static $MENUS = array
			(
                IDM2_CHANGES => array
                    (
                        MI_PAGE => SER_CHANGES,
                        MI_NAME => 'Changes',
                        MI_TIP => 'Daily Changes',
                        MI_URL => 'routes.php?menu=4',			                    // IDM2_CHANGES
                        MI_CODE => 'routes/changes.php'
                    ),
				IDM2_REPORTS => array
					(
						MI_PAGE => SE_REPORTS,
						MI_NAME => 'Reports',
						MI_TIP => 'Reports related to Routes',
						MI_URL => 'routes.php?menu=2',								// IDM2_REPORTS
						MI_CODE => 'routes/reports.php'
					),
				IDM2_SEQUENCING => array
					(
						MI_PAGE => SE_SEQUENCING,
						MI_NAME => 'Sequencing',
						MI_TIP => 'Delivery order of Customers within Routes',
						MI_URL => 'routes.php?menu=3',								// IDM2_SEQUENCING
						MI_CODE => 'routes/sequencing.php',
						MI_SCRIPTS => array('js/popups/customers.js.php'),
						MI_STYLES => array(),
					),
			);
		
		public function __construct()
		{
			global $MENUS;
			
			parent::__construct(RoutesMenu::$MENUS, 2);
			parent::super($MENUS, IDM_ROUTES);
		}
		
		protected function display_begin()
		{
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
		}
		
		protected function display_end()
		{
			global $resultHtml;
			
			echo '<input type="hidden" name="menu" value="' . $_REQUEST['menu'] . '" />'
					. '</form>'
					. '</div>';
		}
		
		public function display_page($active, $styles='', $script='')
		{
            switch ($active)
            {
            case IDM2_CHANGES:
                self::load(IDM2_CHANGES);
                break;

            case IDM2_REPORTS:
                self::load(IDM2_REPORTS);
                break;

            default:
				parent::display_page($active, $styles, $script);
                break;
            }
		}
		
		public function title($active)
		{
			return 'Routes / ' . $this->_menus[$active][MI_NAME];
		}
		
		public function title_linked($active)
		{
			return '<a href="routes.php">Routes</a> / ' . $this->_menus[$active][MI_NAME];
		}
	}
	
xdebug_start_trace();
	$SUBMENUS = new RoutesMenu();
	if (!isset($_REQUEST['menu']))
	{
		$item = $SUBMENUS->begin();
		$_REQUEST['menu'] = key($item);
	}
	$SUBMENUS->display_page($_REQUEST['menu']);
?>
