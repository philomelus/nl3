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
/*
    IDSSM_TYPES => array
        (
            MI_PAGE => SCD_TYPES,
            MI_NAME => 'Types',
            MI_TIP => 'Configure Customer Delivery Types',
            MI_URL => 'customers.php?menu=1&submenu=3',				// IDSSM_TYPES
            MI_CODE => 'customers/admin/types.php',
            MI_SCRIPTS => array('js/popups/customers/types.js.php',
                                'js/tableruler.js'),
            MI_STYLES => array(),
        )
*/

define('ROOT', '../../');

require_once 'inc/security.inc.php';

define('PAGE', SAC_TYPES);

require_once 'inc/login.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/profile.inc.php';
require_once 'inc/popups/customers/types.inc.php';

define('IDSM_CONFIG', 1);
define('IDSM_GROUPS', 2);
define('IDSM_PERIODS', 3);
define('IDSM_AUDIT', 4);
define('IDSM_BILLING', 5);
define('IDSM_SECURITY', 7);
define('IDSM_FORMS', 8);
define('IDSM_USERS', 9);
global $err, $errCode, $errContext;
global $smarty;
global $username;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Customers / Types');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Customers / Types';

$message = '';
	
$fs = get_config('flag-stop-type');
$query = 'SELECT * FROM `customers_types`';
if ($fs != CFG_NONE)
    $query .= ' WHERE `id` != ' . $fs;
$query .= ' ORDER BY `abbr`';
$types = db_query($query);
if (!$types)    // TODO: BUGBUG:
    return;
$temp = array();
while ($type = $types->fetch_assoc())
    $temp[] = $type;
$smarty->assign('types', $temp);
$smarty->assign('count', count($temp));

$smarty->display('admin/customers/types.tpl');
		
?>
