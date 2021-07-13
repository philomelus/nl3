<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009 Russell E. Gibson

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
	require 'Smarty.class.php';
	if (!defined('ROOT'))
		define('ROOT', '');
	$smarty = new Smarty();
	$smarty->template_dir = ROOT . 'tmpl';
	$smarty->compile_dir = ROOT . 'tmpl/tmpl_c';
	$smarty->cache_dir = ROOT . 'smarty/cache';
	$smarty->config_dir = ROOT . 'smarty/configs';
	$smarty->plugins_dir[] = ROOT . 'tmpl/plugins';
    $smarty->caching = FALSE;
    $smarty->compile_check = TRUE;
    $smarty->error_reporting = -1;
//    $smarty->debugging = TRUE;
//    $smarty->debug = TRUE;
?>
