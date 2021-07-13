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

	define('PAGE', SW_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/popups/customers.inc.php';

	if (!isset($_REQUEST['id']) || !preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['id']))
	{
		echo invalid_parameters('View Change', 'Customers/Changes/View.php');
		return;
	}

	$ID = intval($_REQUEST['id']);
	$serviceType = lup_c_service_type($ID);

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	echo gen_htmlHeader('View Type Change', $style, $script);

?>
	<body>
<?php
	// Display error if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);

?>
		<table>
			<tr>
				<td>Type Change ID</td>
				<td>
					<span>
<?php
	printf('%08d', $ID);
?>
					</span>
				</td>
			</tr>
			<tr>
				<td>Customer ID</td>
				<td>
					<span>
<?php
	echo CustomerViewLink($serviceType->customer_id, '../../../') . sprintf('C%06d', $serviceType->customer_id) . '</a>';
?>
					</span>
				</td>
			</tr>
			<tr>
				<td>Created</td>
				<td>
<?php
	echo strftime('%m/%d/%Y %H:%M:%S', strtotime($serviceType->created));
?>
				</td>
			</tr>
			<tr>
				<td>Bill</td>
				<td>
<?php
	if (is_null($serviceType->period_id))
		echo '<span>';
	else
		echo iid2title($serviceType->period_id);
?>
				</td>
			</tr>
			<tr>
				<td>When</td>
				<td>
<?php
	echo strftime('%m/%d/%Y', strtotime($serviceType->when));
?>
				</td>
			</tr>
			<tr>
				<td>Old Type</td>
				<td>
<?php
	echo tid2abbr($serviceType->type_id_from);
?>
				</td>
			</tr>
			<tr>
				<td>New Type</td>
				<td>
<?php
	echo tid2abbr($serviceType->type_id_to);
?>
				</td>
			</tr>
			<tr>
				<td>Include In Billing</td>
				<td>
<?php
	if ($serviceType->ignoreOnBill == 'Y')
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
	$val = valid_text(htmlspecialchars(stripslashes($serviceType->why)));
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
	$val = valid_text(htmlspecialchars(stripslashes($serviceType->note)));
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
