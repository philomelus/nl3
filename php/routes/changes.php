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

    define('IDM3_NOTES',    1);
    define('IDM3_REPORT',   IDM3_NOTES + 1);
    define('IDM3_HISTORY',  IDM3_REPORT + 1);

    class RoutesChangesMenu extends _MenuBase
    {
        private static $MENUS = array
            (
                IDM3_HISTORY => array
                    (
                        MI_PAGE => SERC_HISTORY,
                        MI_NAME => 'History',
                        MI_TIP => 'Displays Notes from Previous Change Reports',
                        MI_URL => 'routes.php?menu=4&submenu=3',				// IDSM_HISTORY
                        MI_CODE => 'routes/changes/history.php',
                        MI_SCRIPTS => array(),
                        MI_STYLES => array(),
                    ),
                IDM3_NOTES => array
                    (
                        MI_PAGE => SERC_NOTES,
                        MI_NAME => 'Notes',
                        MI_TIP => 'Change List Notes',
                        MI_URL => 'routes.php?menu=4&submenu=1',				// IDSM_NOTES
                        MI_CODE => 'routes/changes/notes.php',
                        MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
                    ),
                IDM3_REPORT => array
                    (
                        MI_PAGE => SERC_REPORT,
                        MI_NAME => 'Report',
                        MI_TIP => 'Change List Report',
                        MI_URL => 'routes.php?menu=4&submenu=2',				// IDSM_REPORT
                        MI_CODE => 'routes/changes/report.php',
                        MI_SCRIPTS => array('js/calendar.js'),
                        MI_STYLES => array(),
                    )
            );
        
        public function __construct()
        {
            global $SUBMENUS;
            
            parent::__construct(RoutesChangesMenu::$MENUS, 3);
            self::super($SUBMENUS, IDM2_CHANGES);
        }
        
        public function submit($active)
        {
            global $action;
            global $Routes;
            
            populate_routes();
            
            // If function doesn't exist, then there is nothing to do
            $name = $this->prefix() . 'submit';
            if (!function_exists($name))
                return '';
            
            // Check for action
            if (isset($_REQUEST['action']) && !empty($_REQUEST['action']))
            {
                $action = $_REQUEST['action'];
                return $name();
            }
            
            // Check for actionnotes
            if ((isset($_REQUEST['actionnotes']) && !empty($_REQUEST['actionnotes'])))
                return $name();
            
            // Check for actionsnotes for a route
            reset($Routes);
            foreach($Routes as $rid => $dummy)
            {
                if (isset($_REQUEST['actionnotes' . $rid]) && !empty($_REQUEST['actionnotes' . $rid]))
                    return $name();
            }

            return '';
        }
        
        public function title($active)
        {
            return 'Routes / Changes / ' . $this->_menus[$active][MI_NAME];
        }
        
        public function title_linked($active)
        {
            return '<a href="routes.php">Routes</a>'
                 . ' / <a href="routes.php?menu=4">Changes</a>'
                 . ' / ' . $this->_menus[$active][MI_NAME];
        }
    }

    global $SUBSUBMENUS;
    $SUBSUBMENUS = new RoutesChangesMenu();
    if (!isset($_REQUEST['submenu']))
    {
        $item = $SUBSUBMENUS->begin();
        $_REQUEST['submenu'] = key($item);
    }
    $SUBSUBMENUS->display_page($_REQUEST['submenu']);
?>
