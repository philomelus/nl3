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

	define('PAGE', SP_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers.inc.php';

	//-------------------------------------------------------------------------
	// Make sure we were passed required parameters
	if (!isset($_REQUEST['pid']))
	{
		echo invalid_parameters('View Payment', 'Customers/Payments/View.php');
		return;
	}
	$PID = intval($_REQUEST['pid']);

	//-------------------------------------------------------------------------
	// Get payment record
	$payment = lup_c_payment($PID);

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	$PIDSTR = sprintf('P%08d', $PID);
	echo gen_htmlHeader('View Payment ' . $PIDSTR, $style, $script);

?>
	<body>
<?php
	// Display error if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);
?>
		<table>
			<tr>
				<td>PID</td>
				<td>
					<span><?php echo $PIDSTR ?></span>
				</td>
			</tr>
			<tr>
				<td>CID</td>
				<td>
<?php
		echo CustomerViewLink($payment->customer_id, '../../../') . sprintf('C%06d', $payment->customer_id) . '</a>';
?>
				</td>
			</tr>
			<tr>
				<td>When</td>
				<td>
<?php
	echo strftime('%m/%d/%Y %H:%M:%S', strtotime($payment->created));
?>
				</td>
			</tr>
			<tr>
				<td>Period</td>
<?php
	populate_periods();
	$val = $Periods[$payment->period_id]['title'];
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Type</td>
<?php
	if (isset($_REQUEST['type']))
		$val = $_REQUEST['type'];
	else
		$val = $payment->type;
	$temp = array
			(
				'CHECK' => '',
				'MONEYORDER' => '',
				'CASH' => ''
			);
	$temp[$val] = ' selected="selected"';
	switch ($payment->type)
	{
	case 'CHECK':
		$val = 'Check';
		break;

	case 'MONEYORDER':
		$val = 'Money Order';
		break;

	case 'CASH':
		$val = 'Cash';
		break;

	default:
		$val = '<span>Invalid Type</span>';
		break;
	}
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>ID</td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($payment->extra1)));
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Amount</td>
<?php
	$val = sprintf('$%01.2f', $payment->amount);
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Tip</td>
<?php
	$val = sprintf('$%01.2f', $payment->tip);
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Note</td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($payment->note)));
	if ($val == '&nbsp;')
		$val = '<span>blank</span>';
?>
				<td><?php echo $val ?></td>
			</tr>
		</table>
<?php
    gen_htmlFooter();
?>
