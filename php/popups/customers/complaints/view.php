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

	define('PAGE', SQ_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/regex.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('View Complaint', 'popups/customers/complaints/view.php');
		return;
	}

	$ID = $_REQUEST['id'];
	$complaint = lup_c_complaint($ID);

	if ($complaint->result == RESULT_CREDIT
			|| $complaint->result == RESULT_CHARGE)
	{
		$showAmount = true;
	}
	else
		$showAmount = false;

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('View Complaint', $style, $script);

?>
	<body>
<?php
	// Display error if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);

?>
		<table>
			<tr>
				<td>Complaint ID</td>
				<td><?php printf('%08d', $ID); ?></td>
			</tr>
			<tr>
				<td>Customer ID</td>
				<td>
					<span>
<?php
	echo CustomerViewLink($complaint->customer_id, '../../../') . sprintf('%06d', $complaint->customer_id) . '</a>';
?>
					</span>
				</td>
			</tr>
			<tr>
				<td>Bill</td>
				<td>
<?php
	if (is_null($complaint->period_id))
		echo '<span>pending</span>';
	else
		echo iid2title($complaint->period_id);
?>
				</td>
			</tr>
			<tr>
				<td>When</td>
				<td>
<?php
	echo strftime('%m/%d/%Y', strtotime($complaint->when));
?>
				</td>
			</tr>
			<tr>
				<td>Result</td>
				<td>
<?php
	switch ($complaint->result)
	{
	case RESULT_NOTHING:		echo 'None';			break;
	case RESULT_CREDIT1DAILY:	echo 'Credit 1 Daily';	break;
	case RESULT_CREDIT1SUNDAY:	echo 'Credit 1 Sunday';	break;
	case RESULT_REDELIVERED:	echo 'Redelivered';		break;
	case RESULT_CREDIT:			echo 'Credit';			break;
	case RESULT_CHARGE:			echo 'Charge';			break;
	default:	echo '<span>ERROR!</span>';	break;
	}
?>
				</td>
			</tr>
			<tr>
				<td>Amount</td>
				<td>
<?php
	printf('$%01.2f', floatval($complaint->amount));
?>
				</td>
			</tr>
			<tr>
				<td>Ignore for Billing</td>
				<td>
<?php
	if ($complaint->ignoreOnBill == 'Y')
		echo 'Yes';
	else
		echo 'No';
?>
				</td>
			</tr>
			<tr>
				<td>Cause</td>
				<td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($complaint->why)));
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
	$val = valid_text(htmlspecialchars(stripslashes($complaint->note)));
	if ($val == '&nbsp;')
		$val = '<span>blank</span>';
	echo $val;
?>
				</td>
			</tr>
		</table>
<?php
    echo gen_htmlHeader();
?>
