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

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SV_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('View Stop / Start', 'popups/customers/service/view.php');
		return;
	}

	$ID = intval($_REQUEST['id']);
	$service = lup_c_service($ID);

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script language="JavaScript">
</script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('View Stop / Start', $style, $script);

?>
	<body>
<?php
	// Display error if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);

?>
		<table>
			<tr>
				<td>Start/Stop ID</td>
				<td>
<?php
	printf('%08d', $ID);
?>
				</td>
			</tr>
			<tr>
				<td>Customer ID</td>
				<td>
<?php
	echo CustomerViewLink($service->customer_id, '../../../') . sprintf('C%06d', $service->customer_id) . '</a>';
?>
				</td>
			</tr>
			<tr>
				<td>Created</td>
				<td>
<?php
	echo strftime('%m/%d/%Y %H:%M:%S', strtotime($service->created));
?>
				</td>
			</tr>
			<tr>
				<td>Bill</td>
				<td>
<?php
	if (is_null($service->period_id))
		echo '<span>pending</span>';
	else
		echo iid2title($service->period_id);
?>
				</td>
			</tr>
			<tr>
				<td>When</td>
				<td>
<?php
	echo strftime('%m/%d/%Y', strtotime($service->when));
?>
				</td>
			</tr>
			<tr>
				<td>Type</td>
				<td>
<?php
	switch ($service->type)
	{
	case SERVICE_START:		echo 'Start';			break;
	case SERVICE_STOP:		echo 'Stop';			break;
	default:	echo '<span>ERROR!</span>';	break;
	}
?>
				</td>
			</tr>
			<tr>
				<td>Include in Billing</td>
				<td>
<?php
	if ($service->ignoreOnBill == 'Y')
		echo 'No';
	else
		echo 'Yes';
?>
				</td>
			</tr>
			<tr>
				<td>Reason</td>
				<td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($service->why)));
	if ($val == '&nbsp;')
		$val = '<span>blank</span>';
	echo $val;
?>
				</td>
			</tr>
			<tr>
				<td>Note</td>
				<td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($service->note)));
	if ($val == '&nbsp;')
		$val = '<span>blank</span>';
	echo $val;
?>
				</td>
			</tr>
		</table>
<?php
    gen_htmlFooter();
?>
