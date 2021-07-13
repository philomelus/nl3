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

    define('IDM3_DRAW',			2);
    define('IDM3_ROUTELIST',	IDM3_DRAW + 1);
    define('IDM3_STATUS',		IDM3_ROUTELIST + 1);
    define('IDM3_TIPS',			IDM3_STATUS + 1);

    class RoutesReportsMenu extends _MenuBase
    {
        private static $MENUS = array
            (
                IDM3_DRAW => array
                    (
                        MI_PAGE => SER_DRAW,
                        MI_NAME => 'Draw',
                        MI_TIP => 'Daily Paper Draw',
                        MI_URL => 'routes.php?menu=2&submenu=2',			// IDM3_DRAW
                        MI_CODE => 'routes/reports/draw.php',
                        MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
                    ),
                IDM3_ROUTELIST => array
                    (
                        MI_PAGE => SER_ROUTE,
                        MI_NAME => 'Route',
                        MI_TIP => 'Route List',
                        MI_URL => 'routes.php?menu=2&submenu=3',			// IDM3_ROUTELIST
                        MI_CODE => 'routes/reports/route.php',
                        MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
                    ),
                IDM3_STATUS => array
                    (
                        MI_PAGE => SER_STATUS,
                        MI_NAME => 'Status',
                        MI_TIP => 'Status of all Customers included in a Route',
                        MI_URL => 'routes.php?menu=2&submenu=4',			// IDM3_STATUS
                        MI_CODE => 'routes/reports/status.php',
                        MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
                    ),
                IDM3_TIPS => array
                    (
                        MI_PAGE => SER_TIPS,
                        MI_NAME => 'Tips',
                        MI_TIP => 'Tips by Customers Per Route',
                        MI_URL => 'routes.php?menu=2&submenu=5',			// IDM3_TIPS
                        MI_CODE => 'routes/reports/tips.php',
                        MI_SCRIPTS => array(),
                        MI_STYLES => array(),
                    )
            );
        
        public function __construct()
        {
            global $SUBMENUS;
            
            parent::__construct(RoutesReportsMenu::$MENUS, 3);
            parent::super($SUBMENUS, IDM2_REPORTS);
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
        
        public function title($active)
        {
            return 'Routes / Reports / ' . $this->_menus[$active][MI_NAME];
        }
        
        public function title_linked($active)
        {
            return '<a href="routes.php">Routes</a> / <a href="routes.php?menu=2">Reports</a> / ' . $this->_menus[$active][MI_NAME];
        }
    }

	global $SUBSUBMENUS;
	$SUBSUBMENUS = new RoutesReportsMenu();
	if (!isset($_REQUEST['submenu']))
	{
		$item = $SUBSUBMENUS->begin();
		$_REQUEST['submenu'] = key($item);
	}
	$SUBSUBMENUS->display_page($_REQUEST['submenu']);

?>
