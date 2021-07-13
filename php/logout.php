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
	require_once 'inc/security.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/audit.inc.php';
	
	$title = get_globalConfig('default-title', 'NewsLedger');
	session_name('newsledger' . $title);
	session_start();
	
	audit($_SESSION['name'] . ' logged out.');
	
	unset($_SESSION['user']);
	unset($_SESSION['name']);
	unset($_SESSION['password']);
	unset($_SESSION['gid']);
	unset($_SESSION['uid']);
	header('Location: index.php');
	exit();
?>
