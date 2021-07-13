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
    IDM2_ADMIN => array
        (
            MI_PAGE => SE_ADMIN,
            MI_TIP => 'Route Administration',
        ),
*/

define('ROOT', '../');

require_once 'inc/security.inc.php';

define('PAGE', SA_ROUTES);

require_once 'inc/login.inc.php';
require_once 'inc/menu.inc.php';
require_once 'inc/common.inc.php';
require_once 'inc/errors.inc.php';
require_once 'inc/form.inc.php';
require_once 'inc/popups/customers.inc.php';
require_once 'inc/popups/routes.inc.php';

global $err, $errCode, $errContext;
global $smarty;
global $username;
global $message;

$smarty->assign('ROOT', ROOT);
$smarty->assign('path', 'Admin / Routes');
$smarty->assign('title', get_config('default-title', 'NewsLedger'));
$smarty->assign('username', $username);
$smarty->assign('action', $_SERVER['PHP_SELF']);

$errContext = 'Admin / Routes';
$message = '';

$routes = gen_routesArrayFull(true);
$smarty->assign('routes', $routes);
$smarty->assign('count', count($routes));
$smarty->assign('message', $message);
$smarty->assign('action', $_SERVER['PHP_SELF']);
$smarty->assign('menu', $_REQUEST['menu']);
$smarty->display("admin/routes.tpl");
?>
