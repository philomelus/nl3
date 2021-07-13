<?php
/*
	Copyright 2005,2006,2007,2008,2009 Russell E. Gibson

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
	define('ROOT', '../../../');
	set_include_path(ROOT . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SL_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_GET['cid']) || !isset($_GET['iid'])
			|| !preg_match('/^\d+$/', $_GET['cid'])
			|| !preg_match('/^\d+$/', $_GET['iid']))
	{
		echo invalid_parameters('View Bill', 'popups/customers/bills/view.php');
		return;
	}
	$CID = intval($_GET['cid']);
	$IID = intval($_GET['iid']);

	$err = ERR_SUCCESS;
	$bill = lup_c_bill($CID, $IID);
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}
	populate_routes();
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}
	populate_periods();
	if ($err < ERR_SUCCESS)
	{
		echo fatal_error('Edit Bill', $errText);
		return;
	}

	$smarty->assign('CID', $CID);
	$smarty->assign('ROOT', ROOT);
	$smarty->assign('bill', $bill);
	$smarty->assign('clientName', get_config('client-name', 'ERROR'));
	$smarty->assign('clientAddress1', get_config('client-address-1', 'ERROR'));
	$smarty->assign('clientAddress2', get_config('client-address-2', 'ERROR'));
	$smarty->assign('clientTelephone', get_config('client-telephone', 'ERROR'));
	$smarty->assign('billCreated', strtotime($bill->created));
	$smarty->assign('billUpdated', strtotime($bill->when));
	$smarty->display('popups/customers/bills/view.tpl');
?>
